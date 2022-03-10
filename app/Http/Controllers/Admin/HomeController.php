<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\UpdateProfile;
use App\Exports\Admin\ReportExport;
use App\Branch;
use App\User;
use App\Appointment;
use App\DirectQueue;
use App\Models\Exhibition;
use Auth;
use DB;
use Excel;
class HomeController extends Controller
{
    public function index()
    {
        $appointmentGraph = Appointment::select(DB::raw("date_part('month', date) as month"), DB::raw("count(id) as total"))->groupBy('month')->orderBy('month')->get(); // for pgsql
        $exhibitionGraph = Exhibition::select(DB::raw("date_part('month', date) as month"), DB::raw("count(id) as total"))->groupBy('month')->orderBy('month')->get(); // for pgsql
        $onsiteGraph = DirectQueue::select(DB::raw("date_part('month', created_at) as month"), DB::raw("count(id) as total"))->groupBy('month')->orderBy('month')->get(); // for pgsql

        return view('admin.home', [
            'totalBranch' => Branch::count(),
            'totalUser' => User::whereRole('customer')->count(),
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
