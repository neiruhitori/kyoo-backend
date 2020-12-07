<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\RegistrationBranch;
use App\Branch;
use App\BranchConfiguration;
use App\User;
use App\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Auth;

class RegistrationBranchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $branches = RegistrationBranch::all();
        return view('admin.registrationBranch.index')->withBranches($branches);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\RegistrationBranch  $registrationBranch
     * @return \Illuminate\Http\Response
     */
    public function show(RegistrationBranch $registrationBranch)
    {
        return view('admin.registrationBranch.show')->withBranch($registrationBranch);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\RegistrationBranch  $registrationBranch
     * @return \Illuminate\Http\Response
     */
    public function edit(RegistrationBranch $registrationBranch)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\RegistrationBranch  $registrationBranch
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, RegistrationBranch $registrationBranch)
    {
        $input = $registrationBranch->toArray();
        switch ($input['queue_type']) {
            case 'direct_queue':
                $input['branch_type_id'] = 2;
                break;
            case 'appointment_queue':
                $input['branch_type_id'] = 1;
                break;
        }
        // duplicate to branches table
        $input['mobile_phone'] = $registrationBranch->phone;
        $branch = Branch::create($input);
        // create branch configuration
        BranchConfiguration::create([
            'branch_id' => $branch->id
        ]);

        // duplicate to users table
        $input['password'] = Crypt::decryptString($registrationBranch->makeVisible('attribute')->password);
        $input['role'] = 'admin_branch';
        $input['branch_id'] = $branch->id;
        $user = User::create($input);

        // remove registration branch
        $registrationBranch->delete();

        $request->session()->flash('succces', 'Branch '.$registrationBranch->name.' has been approved!');
        return redirect(route('admin.branch.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\RegistrationBranch  $registrationBranch
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, RegistrationBranch $registrationBranch)
    {
        $registrationBranch->delete();
        Log::create([
            'user_id' => Auth::id(),
            'description' => 'Remove Branch Registration'
        ]);
        $request->session()->flash('error', 'Branch '.$registrationBranch->name.' has been rejected!');
        return redirect(route('admin.branch.index'));
    }
}
