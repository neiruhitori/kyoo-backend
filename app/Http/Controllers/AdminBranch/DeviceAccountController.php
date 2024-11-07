<?php

namespace App\Http\Controllers\AdminBranch;

use Mail;
use Crypt;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use App\Log;
use App\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\AdminBranch\StoreDeviceUser;
use App\Http\Requests\AdminBranch\UpdateDeviceUser;
use App\Mail\Device\ResetPassword;

class DeviceAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::withTrashed()->where([
            'branch_id' => Auth::user()->branch_id,
            'role' => 'device'
        ])->get();

        return view('adminBranch.deviceAccount.index', [
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
        // update for next if we need limit device user 
        // if (!Auth::user()->Branch->BranchType->is_premium && count(Auth::user()->Branch->CS) > 0)

        if (!Auth::user()->Branch->BranchType->is_premium) {
            $request->session()->flash('warning', __('You need to upgrade to premium user'));
            return redirect(route('admin-branch.branch-configuration.device-account.index'));
        }
        if(count(Auth::user()->Branch->Device) >= 1){
            $request->session()->flash('error', __('Maximum Device Account has exceeded'));
            return redirect(route('admin-branch.branch-configuration.device-account.index'));
        }
        return view('adminBranch.deviceAccount.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreDeviceUser $request)
    {
        // update for next if we need limit device user 
        // if (!Auth::user()->Branch->BranchType->is_premium && count(Auth::user()->Branch->CS) > 0)

        if (!Auth::user()->Branch->BranchType->is_premium) {
            $request->session()->flash('error', __('You need to upgrade to premium user'));
            return redirect(route('admin-branch.branch-configuration.device-account.index'));
        }

        $input = $request->all();
        $input['branch_id'] = Auth::user()->branch_id;
        $input['role'] = 'device';
        $input['name'] = "DEV{$input['branch_id']}_".$request->username;
        $input['username'] = "DEV{$input['branch_id']}_".$request->username;
        $input['is_password_changed'] = true;
        $user = User::create($input);
        
        Log::create([
            'user_id' => Auth::id(),
            'description' => 'Create Device User'
        ]);
        $request->session()->flash('success', __('module.created', ['module' => __('Account'), 'name' => $input['username']]));
        return redirect(route('admin-branch.branch-configuration.device-account.index'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        return view('adminBranch.deviceAccount.edit')->withUser($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateDeviceUser $request, User $user)
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
        $input['name'] = "DEV{$user['branch_id']}_".$request->username;
        $input['username'] = "DEV{$user['branch_id']}_".$request->username;
        $input['is_password_changed'] = true;
        if (!$request->password) {
            unset($input['password']);
        }
        $user->update($input);

        Log::create([
            'user_id' => Auth::id(),
            'description' => 'Update Device User'
        ]);

        $request->session()->flash('warning', __('module.updated', ['module' => __('Account'), 'name' => $input['username']]));
        return redirect(route('admin-branch.branch-configuration.device-account.index'));
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
            'description' => 'Remove Device User'
        ]);
        $request->session()->flash('error', __('module.removed', ['module' => __('Account'), 'name' => $user->name]));
        return redirect(route('admin-branch.branch-configuration.device-account.index'));
    }

    public function restore(Request $request)
    {
        $user = User::withTrashed()->find($request->user_id);
        $user->restore();
        Log::create([
            'user_id' => Auth::id(),
            'description' => 'Restore Device User'
        ]);
        $request->session()->flash('success', __('module.restored', ['module' => __('Account'), 'name' => $user->name]));
        return redirect(route('admin-branch.branch-configuration.device-account.index'));
    }

    public function resetPassword(Request $request, User $user)
    {
        if ($user->branch_id != Auth::user()->branch_id) {
            return redirect(route('unauthorized'));
        }
        Mail::to(Auth::user()->email)->send(new ResetPassword($user));
        $request->session()->flash('success', __('Please check your email to reset the password'));
        return redirect(route('admin-branch.branch-configuration.device-account.index'));
    }

    public function reset($user_id)
    {
        $user = User::findOrFail(Crypt::decrypt($user_id));

        return view('auth.passwords.resetDevice', [
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
            return redirect(route('admin-branch.branch-configuration.device-account.reset', $user_id));
        }

        $user = User::findOrFail(Crypt::decrypt($user_id));
        $user->update([
            'password' => $request->password
        ]);
        $request->session()->flash('success', __('module.updated', ['module' => __('Password'), 'name' => $user->name]));
        if (Auth::user()) {
            return redirect(route('admin-branch.branch-configuration.device-account.index'));
        }
        return redirect(route('login'));
    }
}
