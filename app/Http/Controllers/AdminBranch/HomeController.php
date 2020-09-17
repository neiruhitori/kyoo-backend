<?php

namespace App\Http\Controllers\AdminBranch;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\AdminBranch\UpdateProfile;
use App\User;
use App\Appointment;
use Auth;
use DB;
use App\Exports\AdminBranch\ReportExport;
use Excel;

class HomeController extends Controller
{
    public function index()
    {
        $appointments = Appointment::whereHas('Slot.Service', function($query){
            $query->where('branch_id', Auth::user()->branch_id);
        })->whereMonth('date', date('m'))->get();
        // $appointmentGraph = Appointment::select(DB::raw('DAY(date) as `day`'), DB::raw('count(id) as `total`'))->whereMonth('date', date('m'))->groupBy('day')->get(); // for mysql
        $appointmentGraph = Appointment::whereHas('Slot.Service', function($query){
            $query->where('branch_id', Auth::user()->branch_id);
        })->select(DB::raw("date_part('day', date) as day"), DB::raw('count(id) as total'))->whereMonth('date', date('m'))->groupBy('day')->get(); // for pgsql
        return view('adminBranch.home', [
            'totalAppointment' => count($appointments),
            'totalServed' => count($appointments->where('status', 'served')),
            'totalNoShow' => count($appointments->where('status', 'no show')),
            'appointmentGraph' => $appointmentGraph
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

    public function miniReport(Request $request)
    {
        $date = $request->date ?: date('Y-m-d');
        $appointments = Appointment::where('date', $date)->get();
        return view('adminBranch.miniReport')->withAppointments($appointments);
    }
}
