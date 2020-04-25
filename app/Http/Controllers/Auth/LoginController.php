<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|string', //Validate email/username input
            'password' => 'required|string|min:8',
        ]);

        // checking type email / username
        $loginType = filter_var($request->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
    
        // save credential on new array
        $login = [
            $loginType => $request->email,
            'password' => $request->password
        ];
    
        // do login
        if (auth()->attempt($login)) {
            // on success
            return redirect()->route('home');
        }
        // on failed
        return redirect()->route('login')->with(['error' => 'Authenticate Failed']);
    }
}
