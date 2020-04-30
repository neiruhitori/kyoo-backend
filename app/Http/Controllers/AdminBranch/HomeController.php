<?php

namespace App\Http\Controllers\AdminBranch;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\AdminBranch\UpdateProfile;
use App\User;
use App\Appointment;
use Auth;
use DB;

class HomeController extends Controller
{
    public function index()
    {
        $appointments = Appointment::whereMonth('date', date('m'))->get();
        $appointmentGraph = Appointment::select(DB::raw('DAY(date) as `day`'), DB::raw('count(id) as `total`'))->whereMonth('date', date('m'))->groupBy('day')->get();
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
        $user->update($request->all());
        $request->session()->flash('warning', 'Admin profile has been updated!');
        return redirect(route('adminBranch.profile.edit'));
    }
}
