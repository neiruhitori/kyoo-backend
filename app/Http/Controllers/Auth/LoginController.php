<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Log;
use Auth;
use App\User;
use App\Models\CounterActivity;
use Illuminate\Support\Carbon;
use App\BranchType;
use App\Models\CsActiveMenus;

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
        if (!auth()->attempt($login)) {
            // on failed
            return redirect()->route('login')->with(['error' => __('Authenticate Failed')]);
        }

        $loggedUser = Auth::user();
        if ($loggedUser->role !== 'admin_kyoo') {
            $licenseExpirationDate = $loggedUser->Branch->license_expiration_date;
            $isExpired = Carbon::parse($licenseExpirationDate);
            $premium = $loggedUser->Branch->BranchType->is_premium;
                if(!$premium){
                    session(['just_logged_in' => true]);
                }
            if ($licenseExpirationDate && $isExpired->isPast()) {
                // on failed expired account for non admin kyoo
                $this->guard()->logout();
                return redirect()->route('login')->with(['error' => __('Trial Period Has Ended')]);
            }
        }

        // on success
        Log::create([
            'user_id' => Auth::id(),
            'description' => 'Login Success'
        ]);

        if ($loggedUser->WorkstationVct) {
            $this->updateVctActivity();
        }

        User::find(Auth::id())->update([
            'last_login' => date('Y-m-d H:i:s')
        ]);

        $createdAt = Carbon::parse($loggedUser->created_at);
        $fourMonthsAgo = Carbon::now()->subMonths(4);
        
        if (($loggedUser->Branch && $loggedUser->Branch->country == 'Indonesia') || $loggedUser->role == 'admin_kyoo') {
            session()->put('locale', 'id');
        } else {
            session()->put('locale', 'en'); // Default ke EN jika Branch tidak ada
        }
        session()->save();

        if ($loggedUser->role == 'admin_branch' && $createdAt->lessThan($fourMonthsAgo)) {
            return redirect()->route('admin-branch.dashboard');
        } elseif($loggedUser->role == 'admin_branch') {
            return redirect()->route('admin-branch.product-guide.queue-configuration');
        }

        $branchID = Auth::user()->branch_id;
        $active_menus = CsActiveMenus::where('branch_id', $branchID)->where('feature_id', 4)->first();

        if(Auth::user()->Branch && Auth::user()->Branch->BranchType->is_direct_queue && ($loggedUser->role == 'cs' || $loggedUser->role == 'spv') && $active_menus) {
            return redirect()->route('cs.workstation');
        }
        
        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        $this->updateActivityDuration();

        User::find(Auth::id())->update([
            'last_login' => null
        ]);

        $this->guard()->logout();

        $request->session()->invalidate();
        $request->session()->flush();

        $request->session()->regenerateToken();

        if ($response = $this->loggedOut($request)) {
            return $response;
        }

        return $request->wantsJson()
            ? new JsonResponse([], 204)
            : redirect('/');
    }

    private function updateVctActivity()
    {
        $activity = CounterActivity::where([
            'date' => date('Y-m-d'),
            'workstation_id' => Auth::user()->WorkstationVct->workstation_id,
            'vct_id' => Auth::id()
        ])->first();

        $operationDuration = env('SESSION_LIFETIME') * 60;

        if ($activity) {
            $operationDuration += $activity->operation_duration;
        }

        CounterActivity::updateOrCreate([
            'date' => date('Y-m-d'),
            'workstation_id' => Auth::user()->WorkstationVct->workstation_id,
            'vct_id' => Auth::id()
        ], [
            'operation_duration' => $operationDuration,
            'last_login' => date('Y-m-d H:i:s')
        ]);
    }

    private function updateActivityDuration()
    {
        if (!Auth::user()->WorkstationVct) {
            return;
        }

        $activity = CounterActivity::where([
            'date' => date('Y-m-d'),
            'workstation_id' => Auth::user()->WorkstationVct->workstation_id,
            'vct_id' => Auth::id()
        ])->first();

        if (!$activity) {
            return;
        }

        $activity->last_login = null;
        $diff = Carbon::now()->diffInSeconds(Carbon::parse($activity->last_login));

        if ($diff < env('SESSION_LIFETIME') * 60) {
            $activity->operation_duration -= env('SESSION_LIFETIME') * 60 - $diff;
        }
        $activity->save();
    }
}
