<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\UpdateProfile;
use App\Branch;
use App\User;
use Auth;
class HomeController extends Controller
{
    public function index()
    {
        $branches = Branch::all();
        return view('admin.home', [
            'totalBranch' => count($branches)
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
