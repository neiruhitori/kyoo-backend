<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        switch (Auth::user()->role) {
            case 'admin_kyoo':
                return redirect(route('admin.home'));
                break;
            case 'admin_branch':
                return redirect(route('admin-branch.dashboard'));
                break;
            case ('cs' || 'spv'):
                return redirect(route('cs.home'));
                break;
            case 'admin_corporate':
                return redirect(route('adminCorporate.home'));
                break;
            case 'device':
                return redirect(route('device.home'));
                break;
            default:
                return redirect(route('unauthorized'));
                break;
        }
    }
}
