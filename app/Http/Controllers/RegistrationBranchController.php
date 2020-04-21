<?php

namespace App\Http\Controllers;

use App\RegistrationBranch;
use Illuminate\Http\Request;
use App\Http\Requests\StoreRegistrationBranch;
use Illuminate\Support\Facades\Crypt;
use App\Mail\RegistrationBranchMail;
use Illuminate\Support\Facades\Mail;

class RegistrationBranchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
    public function store(StoreRegistrationBranch $request)
    {
        $input = $request->all();
        $input['password'] = Crypt::encryptString($request->password);
        $branch = RegistrationBranch::create($input);

        // sending email
        Mail::to($branch->email)->send(new RegistrationBranchMail($branch));
        return redirect(route('registrationBranch.afterRegister'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\RegistrationBranch  $registrationBranch
     * @return \Illuminate\Http\Response
     */
    public function show(RegistrationBranch $registrationBranch)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\RegistrationBranch  $registrationBranch
     * @return \Illuminate\Http\Response
     */
    public function edit(RegistrationBranch $registrationBranch)
    {
        $registrationBranch->is_email_verified = 1;
        $registrationBranch->save();
        return redirect(route('registrationBranch.afterVerified'));
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\RegistrationBranch  $registrationBranch
     * @return \Illuminate\Http\Response
     */
    public function destroy(RegistrationBranch $registrationBranch)
    {
        //
    }

    public function afterRegister()
    {
        return view('afterRegister');
    }

    public function afterVerified()
    {
        return view('afterVerified');
    }
}
