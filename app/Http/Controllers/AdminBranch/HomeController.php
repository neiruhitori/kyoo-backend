<?php

namespace App\Http\Controllers\AdminBranch;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\AdminBranch\UpdateProfile;
use App\User;
use Auth;
class HomeController extends Controller
{
    public function index()
    {
        return view('adminBranch.home');
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
