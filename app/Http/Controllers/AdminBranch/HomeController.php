<?php

namespace App\Http\Controllers\AdminBranch;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\AdminBranch\UpdateProfile;
use App\User;
use App\Appointment;
use App\DirectQueue;
use Auth;
use DB;
use App\Exports\AdminBranch\ReportExport;
use Excel;

class HomeController extends Controller
{
    public function index()
    {
        $isAppointment = Auth::user()->Branch->BranchType->is_appointment;
        $isDirectQueue = Auth::user()->Branch->BranchType->is_direct_queue;

        // Appointment Queue
        if ($isAppointment) {
            $appointments = Appointment::whereHas('Slot.Service', function($query){
                $query->where('branch_id', Auth::user()->branch_id);
            })->whereMonth('date', date('m'))->get();
            // $appointmentGraph = Appointment::select(DB::raw('DAY(date) as `day`'), DB::raw('count(id) as `total`'))->whereMonth('date', date('m'))->groupBy('day')->get(); // for mysql
            $appointmentGraph = Appointment::whereHas('Slot.Service', function($query){
                $query->where('branch_id', Auth::user()->branch_id);
            })->select(DB::raw("date_part('day', date) as day"), DB::raw('count(id) as total'))->whereMonth('date', date('m'))->groupBy('day')->get(); // for pgsql
        }

        // Direct Queue
        if ($isDirectQueue) {
            $directQueues = DirectQueue::whereHas('WorkstationService.Service', function($query){
                $query->whereBranchId(Auth::user()->branch_id);
            })->whereMonth('created_at', date('m'))->get();
            $directQueueGraph = DirectQueue::whereHas('WorkstationService.Service', function($query){
                $query->whereBranchId(Auth::user()->branch_id);
            })->select(DB::raw("date_part('day', created_at) as day"), DB::raw('count(id) as total'))->whereMonth('created_at', date('m'))->groupBy('day')->get(); // for pgsql
        }

        return view('adminBranch.home', [
            'totalAppointment' => $isAppointment ? count($appointments) : 0,
            'totalServed' => $isAppointment ? count($appointments->where('status', 'served')) : 0,
            'totalNoShow' => $isAppointment ? count($appointments->where('status', 'no show')) : 0,
            'appointmentGraph' => $isAppointment ? $appointmentGraph : [],
            'totalDirectQueue' => $isDirectQueue ? count($directQueues) : 0,
            'totalDirectQueueServed' => $isDirectQueue ? count($directQueues->where('status', 'done')) : 0,
            'totalDirectQueueNoShow' => $isDirectQueue ? count($directQueues->where('status', 'unattend')) : 0,
            'directQueueGraph' => $isDirectQueue ? $directQueueGraph : [],
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
        $request->session()->flash('warning', 'Admin profile has been updated!');
        return redirect(route('adminBranch.profile.edit'));
    }

    public function exportExcel()
    {
        return Excel::download(new ReportExport, 'Kyoo - Branch Report.xlsx');
    }

    public function qr()
    {
        $id = base64_encode(Auth::user()->branch_id);
        $image = \QrCode::format('png')
                         ->size(500)->errorCorrection('H')
                         ->generate($id);
      return response($image)->header('Content-type','image/png');
    }
}
