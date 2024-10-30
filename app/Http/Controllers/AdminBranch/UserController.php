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
use App\Http\Requests\AdminBranch\UpdateWorkstationVCT;
use App\Mail\CS\ResetPassword;
use Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Crypt;
use Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::withTrashed()
                ->where('branch_id', Auth::user()->branch_id)
                ->whereIn('role', ['cs', 'spv'])
                ->get();

        return view('adminBranch.user.index', [
            'users' => $users
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if (!Auth::user()->Branch->BranchType->is_premium && count(Auth::user()->Branch->CS) > 0) {
            $request->session()->flash('warning', __('You only able insert one account'));
            return redirect(route('admin-branch.branch-configuration.user.index'));
        }
        if(count(Auth::user()->Branch->CS) >= Auth::user()->Branch->BranchConfiguration->max_services){
            $request->session()->flash('warning', __('The number of accounts exceeds your subscription limit'));
            return redirect(route('admin-branch.branch-configuration.user.index'));
        }
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
        if (!Auth::user()->Branch->BranchType->is_premium && count(Auth::user()->Branch->CS) > 0) {
            $request->session()->flash('error', __('You only able insert one account'));
            return redirect(route('admin-branch.branch-configuration.user.index'));
        }
        if(count(Auth::user()->Branch->CS) >= Auth::user()->Branch->BranchConfiguration->max_services){
            $request->session()->flash('warning', __('The number of accounts exceeds your subscription limit'));
            return redirect(route('admin-branch.branch-configuration.user.index'));
        }

        $input = $request->all();
        $input['branch_id'] = Auth::user()->branch_id;
        $input['role'] = 'cs';
        $input['name'] = "KY{$input['branch_id']}_".$request->username;
        $input['username'] = "KY{$input['branch_id']}_".$request->username;
        $input['is_password_changed'] = true;
        $user = User::create($input);
        WorkstationVct::create([
            'workstation_id' => $request->workstation_id,
            'vct_id' => $user->id
        ]);
        Log::create([
            'user_id' => Auth::id(),
            'description' => 'Create VCT User'
        ]);
        $request->session()->flash('success', __('module.created', ['module' => __('Account'), 'name' => $input['username']]));
        return redirect(route('admin-branch.branch-configuration.user.index'));
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

        if ($user->is_password_changed && !Hash::check($request->old_password, $user->password)) {
            $request->session()->flash('error', __('Please insert correct old password'));
            return redirect()->back();
        }
        $input = $request->all();
        $input['is_password_changed'] = true;
        if (!$request->password) {
            unset($input['password']);
        }
        $user->update($input);

        Log::create([
            'user_id' => Auth::id(),
            'description' => 'Update Password User'
        ]);
        $request->session()->flash('warning', __('module.updated', ['module' => __('Account'), 'name' => $user->username]));
        return redirect(route('admin-branch.branch-configuration.user.index'));
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
        $request->session()->flash('error', __('module.removed', ['module' => __('Account'), 'name' => $user->name]));
        return redirect(route('admin-branch.branch-configuration.user.index'));
    }

    public function editWorkstation(User $user)
    {
        $workstations = Workstation::whereHas('Department', function($query){
            return $query->whereBranchId(Auth::user()->branch_id);
        })->get();
        return view('adminBranch.user.editWorkstation')->withUser($user)->withWorkstations($workstations);
    }

    public function updateWorkstation(UpdateWorkstationVCT $request, User $user)
    {
        if ($user->branch_id != Auth::user()->branch_id) {
            return redirect(route('unauthorized'));
        }

        $input['name'] = "KY{$user['branch_id']}_".$request->username;
        $input['username'] = "KY{$user['branch_id']}_".$request->username;
        $input['role'] = $request->role ?? 'cs';
        $user->update($input);

        $existing_workstation = WorkstationVct::where('vct_id', $user->id)->first();
        if ($existing_workstation) {
            WorkstationVct::where('vct_id', $user->id)->update([
                'workstation_id' => $request->workstation_id
            ]);
        } else {
            WorkstationVct::create([
                'vct_id' => $user->id,
                'workstation_id' => $request->workstation_id
            ]);
        }

        Log::create([
            'user_id' => Auth::id(),
            'description' => 'Update VCT User'
        ]);
        $request->session()->flash('warning', __('module.updated', ['module' => __('Account'), 'name' => $input['username']]));
        return redirect(route('admin-branch.branch-configuration.user.index'));
    }

    public function restore(Request $request)
    {
        $user = User::withTrashed()->find($request->user_id);
        $user->restore();
        Log::create([
            'user_id' => Auth::id(),
            'description' => 'Restore VCT User'
        ]);
        $request->session()->flash('success', __('module.restored', ['module' => __('Account'), 'name' => $user->name]));
        return redirect(route('admin-branch.branch-configuration.user.index'));
    }

    public function resetPassword(Request $request, User $user)
    {
        if ($user->branch_id != Auth::user()->branch_id) {
            return redirect(route('unauthorized'));
        }
        Mail::to(Auth::user()->email)->send(new ResetPassword($user));
        $request->session()->flash('success', __('Please check your email to reset the password'));
        return redirect(route('admin-branch.branch-configuration.user.index'));
    }

    public function reset($user_id)
    {
        $user = User::findOrFail(Crypt::decrypt($user_id));

        return view('auth.passwords.resetCS', [
            'token' => $user_id,
            'user' => $user
        ]);
    }

    public function updatePassword(Request $request, $user_id)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'password' => [
                'required',
                'confirmed',
                'min:8',             // must be at least 8 characters in length
                'regex:/[a-z]/',      // must contain at least one lowercase letter
                'regex:/[A-Z]/',      // must contain at least one uppercase letter
                'regex:/[0-9]/',      // must contain at least one digit
            ]
        ]);

        if ($validator->fails()) {
            $request->session()->flash('error', __('Please check password rules'));
            return redirect(route('admin-branch.branch-configuration.user.reset', $user_id));
        }

        $user = User::findOrFail(Crypt::decrypt($user_id));
        $user->update([
            'password' => $request->password
        ]);
        $request->session()->flash('success', __('module.updated', ['module' => __('Password'), 'name' => $user->name]));
        if (Auth::user()) {
            return redirect(route('admin-branch.branch-configuration.user.index'));
        }
        return redirect(route('login'));
    }
}
