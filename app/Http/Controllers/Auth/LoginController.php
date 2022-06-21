<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Log;
use Auth;
use App\User;

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
            Log::create([
                'user_id' => Auth::id(),
                'description' => 'Login Success'
            ]);

            if (Auth::user()->role == 'admin_branch' && !Auth::user()->last_login) {
                User::find(Auth::id())->update([
                    'last_login' => date('Y-m-d H:i:s')
                ]);
                
                return redirect()->route('admin-branch.product-guide.queue-configuration');
            }
            
            return redirect()->route('dashboard');

        }
        // on failed
        return redirect()->route('login')->with(['error' => __('Authenticate Failed')]);
    }
}
