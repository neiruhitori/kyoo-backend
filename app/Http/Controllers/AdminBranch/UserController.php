<?php

namespace App\Http\Controllers\AdminBranch;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use App\Http\Requests\AdminBranch\StoreUser;
use App\Http\Requests\AdminBranch\UpdateUser;
use Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('adminBranch.user.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('adminBranch.user.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUser $request)
    {
        if (count(Auth::user()->Branch->CS) > 0) {
            $request->session()->flash('error', 'You only able insert one account!');
            return redirect(route('adminBranch.user.index'));
        }
        $input = $request->all();
        $input['branch_id'] = Auth::user()->branch_id;
        $input['role'] = 'cs';
        $input['name'] = "BR{$input['branch_id']}_".$request->username;
        $input['username'] = "BR{$input['branch_id']}_".$request->username;
        User::create($input);
        $request->session()->flash('success', 'Account '.$input['username'].' has been inserted!');
        return redirect(route('adminBranch.user.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        return view('adminBranch.user.edit')->withUser($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUser $request, User $user)
    {
        // gate
        if ($user->branch_id != Auth::user()->branch_id) {
            return redirect(route('unauthorized'));
        }
        $input = $request->all();
        $input['name'] = "BR{$user['branch_id']}_".$request->username;
        $input['username'] = "BR{$user['branch_id']}_".$request->username;
        $user->update($input);
        $request->session()->flash('warning', 'Account '.$input['username'].' has been updated!');
        return redirect(route('adminBranch.user.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, User $user)
    {
        // gate
        if ($user->branch_id != Auth::user()->branch_id) {
            return redirect(route('unauthorized'));
        }
        $user->delete();
        $request->session()->flash('error', 'Account '.$user->name.' has been removed!');
        return redirect(route('adminBranch.user.index'));
    }
}
