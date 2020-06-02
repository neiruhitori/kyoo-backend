<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\API\UserRegister;
use App\Http\Requests\API\UserLogin;
use App\Http\Requests\API\UpdateUser;
use App\Http\Requests\API\UpdateUserPassword;
use App\Http\Requests\API\UpdateUserAvatar;
use App\User;
use App\Customer;
use App\ChangeEmail;
use App\Mail\UserChangeEmail;
use Auth;
use Hash;
use Storage;
use Mail;
use Crypt;

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

    public function update(UpdateUser $request)
    {
        $user = User::find(Auth::id());
        $input = $request->all();

        if ($request->email != $user->email) {
            $input['email'] = $user->email;
            $changeEmail = ChangeEmail::whereUserId($user->id)->first();
            if ($changeEmail) {
                $changeEmail->update([
                    'email' => $request->email
                ]);
            } else {
                $changeEmail = ChangeEmail::create([
                    'user_id' => $user->id,
                    'email' => $request->email
                ]);
            }
            Mail::to($request->email)->send(new UserChangeEmail($changeEmail));
        }

        $user->update($input);
        $user->Customer->update($request->all());
        $user->Customer;
        return response()->json([
            'success' => true,
            'message' => 'update user',
            'data' => $user
        ]);
    }

    public function updatePassword(UpdateUserPassword $request)
    {
        $user = User::find(Auth::id());
        if (Hash::check($request->old_password, $user->password)) {
            $user->password = $request->new_password;
            $user->save();
            return response()->json([
                'success' => true,
                'message' => 'update user password',
                'data' => $user
            ]);
        }   
        return response()->json([
            'success' => false,
            'message' => 'failed to update user password',
            'data' => null
        ]);
    }

    public function updateAvatar(UpdateUserAvatar $request)
    {
        $input = $request->all();
        $input['photo'] = Storage::disk('public')->put('customers', $request->photo);
        $user = User::find(Auth::id());
        $user->Customer->update($input);

        return response()->json([
            'success' => true,
            'message' => 'update user avatar',
            'data' => $user
        ]);
    }

    public function changeEmail($id)
    {
        $id = Crypt::decrypt($id);
        $changeEmail = ChangeEmail::findOrFail($id);
        $changeEmail->User->email = $changeEmail->email;
        $changeEmail->User->save();

        $changeEmail->delete();
        return view('afterChangeEmail');
    }
}
