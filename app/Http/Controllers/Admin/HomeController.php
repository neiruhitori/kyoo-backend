<?php

namespace App\Http\Controllers\Admin;

use DB;
use Auth;
use Excel;
use App\User;
use App\Branch;
use App\Appointment;
use App\DirectQueue;
use App\Models\Exhibition;
use App\Models\UserMobile;
use Illuminate\Http\Request;
use App\Exports\Admin\ReportExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateProfile;

class HomeController extends Controller
{
    public function index()
    {
        $appointmentGraph = Appointment::select(DB::raw("MONTH(date) as month"), DB::raw("count(id) as total"))->groupBy('month')->orderBy('month')->get();
        $exhibitionGraph = Exhibition::select(DB::raw("MONTH(date) as month"), DB::raw("count(id) as total"))->groupBy('month')->orderBy('month')->get();
        $onsiteGraph = DirectQueue::select(DB::raw("MONTH(created_at) as month"), DB::raw("count(id) as total"))->groupBy('month')->orderBy('month')->get();

        return view('admin.home', [
            'totalBranch' => Branch::count(),
            'totalUser' => UserMobile::count(),
            'totalAppointment' => Appointment::count(),
            'totalOnsite' => DirectQueue::count(),
            'totalExhibition' => Exhibition::count(),
            'appointmentGraph' => $appointmentGraph,
            'exhibitionGraph' => $exhibitionGraph,
            'onsiteGraph' => $onsiteGraph
        ]);
    }

    public function edit()
    {
        return view('admin.profile.edit');
    }

    public function update(UpdateProfile $request)
    {
        $user = User::find(Auth::user()->id);
        $user->update($request->all());
        $request->session()->flash('warning', __('Admin profile has been updated'));
        return redirect(route('admin.profile.edit'));
    }

    public function exportExcel()
    {
        return Excel::download(new ReportExport, 'Kyoo - Admin Report.xlsx');
    }
}
