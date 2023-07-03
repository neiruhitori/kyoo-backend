<?php

namespace App\Http\Controllers\AdminBranch;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\AdminBranch\UpdateProfile;
use App\User;
use App\Appointment;
use App\DirectQueue;
use App\Models\Exhibition;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Exports\AdminBranch\ReportExport;
use App\Exports\AdminBranch\ReportExportExhibition;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\URL;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Carbon;

class HomeController extends Controller
{
    public function index()
    {
        $isAppointment = Auth::user()->Branch->BranchType->is_appointment;
        $isDirectQueue = Auth::user()->Branch->BranchType->is_direct_queue;
        $isExhibition = Auth::user()->Branch->BranchType->is_exhibition;

        // Appointment Queue
        if ($isAppointment) {
            $appointments = Appointment::whereHas('Slot.Service', function($query){
                $query->where('branch_id', Auth::user()->branch_id);
            })
                ->whereMonth('date', date('m'))
                ->whereYear('date', date('Y'))
                ->get();
            // $appointmentGraph = Appointment::select(DB::raw('DAY(date) as `day`'), DB::raw('count(id) as `total`'))->whereMonth('date', date('m'))->groupBy('day')->get(); // for mysql
            $appointmentGraph = Appointment::whereHas('Slot.Service', function($query){
                $query->where('branch_id', Auth::user()->branch_id);
            })
                ->select(DB::raw("date_part('day', date) as day"), DB::raw('count(id) as total'))
                ->whereMonth('date', date('m'))
                ->whereYear('date', date('Y'))
                ->groupBy('day')
                ->get(); // for pgsql
        }

        if ($isExhibition) {
            $queue = Exhibition::whereHas('Slot.Service', function($query){
                $query->where('branch_id', Auth::user()->branch_id);
            })
                ->whereMonth('date', date('m'))
                ->whereYear('date', date('Y'))
                ->get();

            $exhibition['total'] = count($queue);
            $exhibition['total_served'] = count($queue->where('status', 'end served'));
            $exhibition['total_no_show'] = count($queue->where('status', 'no show'));

            $exhibition['graph'] = Exhibition::whereHas('Slot.Service', function($query){
                $query->where('branch_id', Auth::user()->branch_id);
            })
                ->select(DB::raw("date_part('day', date) as day"), DB::raw('count(id) as total'))
                ->whereMonth('date', date('m'))
                ->whereYear('date', date('Y'))
                ->groupBy('day')
                ->get();
        }

        // Direct Queue
        if ($isDirectQueue) {
            $directQueues = DirectQueue::whereHas('Service', function($query){
                $query->whereBranchId(Auth::user()->branch_id);
            })
                ->whereMonth('created_at', date('m'))
                ->get();
            $directQueueGraph = DirectQueue::whereHas('Service', function($query){
                $query->whereBranchId(Auth::user()->branch_id);
            })
                ->select(DB::raw("date_part('day', created_at) as day"), DB::raw('count(id) as total'))
                ->whereMonth('created_at', date('m'))
                ->whereYear('created_at', date('Y'))
                ->groupBy('day')
                ->get(); // for pgsql
        }

        $licenseExpirationDate = Auth::user()->Branch->license_expiration_date;
        $parseLicenseExpirationDate = Carbon::parse($licenseExpirationDate);
        $diffDayExpiredAccount = Carbon::now()->diffInDays($parseLicenseExpirationDate);
        $isShowExpiredBanner = $licenseExpirationDate && $diffDayExpiredAccount <= config('app.license_expiration_day');

        return view('adminBranch.home', [
            'totalAppointment' => $isAppointment ? count($appointments) : 0,
            'totalServed' => $isAppointment ? count($appointments->where('status', 'served')) : 0,
            'totalNoShow' => $isAppointment ? count($appointments->where('status', 'no show')) : 0,
            'appointmentGraph' => $isAppointment ? $appointmentGraph : [],
            'totalDirectQueue' => $isDirectQueue ? count($directQueues) : 0,
            'totalDirectQueueServed' => $isDirectQueue ? count($directQueues->where('status', 'end served')) : 0,
            'totalDirectQueueNoShow' => $isDirectQueue ? count($directQueues->where('status', 'no show')) : 0,
            'directQueueGraph' => $isDirectQueue ? $directQueueGraph : [],
            'exhibition' => isset($exhibition) ? $exhibition : null,
            'isShowExpiredBanner' => $isShowExpiredBanner,
            'licenseExpirationDay' => $diffDayExpiredAccount,
        ]);
    }

    public function edit()
    {
        return view('adminBranch.profile.edit');
    }

    public function update(UpdateProfile $request)
    {
        $user = User::find(Auth::user()->id);
        $input = $request->all();
        $input['is_password_changed'] = true;
        $user->update($input);
        $request->session()->flash('warning', __('Admin profile has been updated'));
        return redirect(route('admin-branch.profile'));
    }

    public function exportExcel()
    {
        return Excel::download(new ReportExport, 'Kyoo - Branch Report.xlsx');
    }

    public function exportExcelExhibition()
    {
        return Excel::download(new ReportExportExhibition, 'Kyoo - Branch Exhibition Queue Report.xlsx');
    }

    public function qr()
    {
        $json_barcode = json_encode([
                'type' => 'show_branch_action',
                'branch' => [
                    'id' => Auth::user()->branch_id
                ]
            ]);

        $barcode = base64_encode($json_barcode);
        $image = QrCode::format('png')
                         ->size(500)->errorCorrection('H')
                         ->generate($barcode);
      return response($image)->header('Content-type','image/png');
    }

    public function queueMonitor(Request $request)
    {
        $branchType = Auth::user()->Branch->BranchType;

        if (!$branchType->is_premium) {
            $request->session()->flash('warning', __('This feature for premium account only'));
            return redirect()->back();
        }

        $branchId = Crypt::encrypt(Auth::user()->branch_id);

        $routeName = '';

        if ($branchType->is_appointment) {
            $routeName = 'appointments.signage';
        }

        if ($branchType->is_direct_queue) {
            $routeName = 'directQueues.signage';
        }

        $url = URL::temporarySignedRoute(
            $routeName, now()->addDays(1), ['branch_id' => $branchId]
        );

        return redirect($url);
    }
}
