<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\API\UserRegister;
use App\Http\Requests\API\UserLogin;
use App\User;
use App\Customer;
use Auth;

class UserController extends Controller
{
    public function register(UserRegister $request)
    {
        // insert all requests to new array
        $input = $request->all();

        // create a new user
        $user = User::create($input);

        // create a new customer
        $input['user_id'] = $user->id;
        $customer = Customer::create($input);
        
        // send response
        $user->Customer;
        $user['token'] =  $user->createToken('nApp')->accessToken;
        return response()->json([
            'success' => true,
            'message' => 'user registered',
            'data' => $user
        ]);
    }

    public function login(UserLogin $request)
    {
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            $user = Auth::user();
            $user['token'] =  $user->createToken('nApp')->accessToken;
            return response()->json([
                'success' => true,
                'message' => 'login success',
                'data' => $user
            ]);
        }
        else{
            return response()->json([
                'success' => false,
                'message' => 'login failed',
                'data' => null
            ], 401);
        }
    }

    public function detail()
    {
        $user = Auth::user();
        $user->Customer;
        
        return response()->json([
            'success' => true,
            'message' => 'get detail user',
            'data' => $user
        ]);
    }
}
