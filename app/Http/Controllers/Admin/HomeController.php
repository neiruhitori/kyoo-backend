<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\UpdateProfile;
use App\Branch;
use App\User;
use App\Appointment;
use Auth;
use DB;

class HomeController extends Controller
{
    public function index()
    {
        $branches = Branch::all();
        $users = User::whereRole('customer')->get();
        $appointments = Appointment::all();
        $appointmentGraph = Appointment::select(DB::raw('MONTH(date) as `month`'), DB::raw('count(id) as `total`'))->groupBy('month')->get();
        return view('admin.home', [
            'totalBranch' => count($branches),
            'totalUser' => count($users),
            'totalAppointment' => count($appointments),
            'appointmentGraph' => $appointmentGraph
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
        $request->session()->flash('warning', 'Admin profile has been updated!');
        return redirect(route('admin.profile.edit'));
    }
}
