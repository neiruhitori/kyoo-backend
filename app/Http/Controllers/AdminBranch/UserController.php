<?php

namespace App\Http\Controllers\AdminBranch;

use App\Http\Controllers\Controller;
use App\User;
use App\Workstation;
use App\WorkstationVct;
use App\Log;
use Illuminate\Http\Request;
use App\Http\Requests\AdminBranch\StoreUser;
use App\Http\Requests\AdminBranch\UpdateUser;
use Auth;
use Hash;

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
        $workstations = Workstation::whereHas('Department', function($query){
            return $query->whereBranchId(Auth::user()->branch_id);
        })->get();
        return view('adminBranch.user.create')->withWorkstations($workstations);
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
        $input['name'] = "KY{$input['branch_id']}_".$request->username;
        $input['username'] = "KY{$input['branch_id']}_".$request->username;
        $user = User::create($input);
        WorkstationVct::create([
            'workstation_id' => $request->workstation_id,
            'vct_id' => $user->id
        ]);
        Log::create([
            'user_id' => Auth::id(),
            'description' => 'Create VCT User'
        ]);
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
        $workstations = Workstation::whereHas('Department', function($query){
            return $query->whereBranchId(Auth::user()->branch_id);
        })->get();
        return view('adminBranch.user.edit')->withUser($user)->withWorkstations($workstations);
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

        if (!Hash::check($request->old_password, $user->password)) {
            $request->session()->flash('error', 'Please insert correct old password!');
            return redirect()->back();
        }
        $input = $request->all();
        $input['name'] = "KY{$user['branch_id']}_".$request->username;
        $input['username'] = "KY{$user['branch_id']}_".$request->username;
        if (!$request->password) {
            unset($input['password']);
        }
        $user->update($input);
        WorkstationVct::whereVctId($user->id)->update([
            'workstation_id' => $request->workstation_id
        ]);
        Log::create([
            'user_id' => Auth::id(),
            'description' => 'Update VCT User'
        ]);
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
        Log::create([
            'user_id' => Auth::id(),
            'description' => 'Remove VCT User'
        ]);
        $request->session()->flash('error', 'Account '.$user->name.' has been removed!');
        return redirect(route('adminBranch.user.index'));
    }

    public function restore(Request $request)
    {
        $user = User::withTrashed()->find($request->user_id);
        $user->restore();
        Log::create([
            'user_id' => Auth::id(),
            'description' => 'Restore VCT User'
        ]);
        $request->session()->flash('success', 'Account '.$user->name.' has been restored!');
        return redirect(route('adminBranch.user.index'));
    }
}
