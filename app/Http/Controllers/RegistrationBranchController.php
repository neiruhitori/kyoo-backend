<?php

namespace App\Http\Controllers;

use App\RegistrationBranch;
use Illuminate\Http\Request;
use App\Http\Requests\StoreRegistrationBranch;
use Illuminate\Support\Facades\Crypt;
use App\Mail\RegistrationBranchMail;
use Illuminate\Support\Facades\Mail;
use Laravel\Socialite\Facades\Socialite;
use App\User;
use App\Customer;

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

    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToProvider()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback()
    {
        $user = Socialite::driver('google')->user();

        return [
            'id' => $user->getId(),
            'name' => $user->getName(), 
            'token' => $user->token
        ];
    }
}
