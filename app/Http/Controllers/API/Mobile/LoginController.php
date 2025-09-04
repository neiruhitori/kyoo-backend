<?php

namespace App\Http\Controllers\API\Mobile;

use App\Models\UserMobile;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\RegistrationUser;
use App\Mail\RegistrationUserMail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $user = UserMobile::with('Regency')->where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Data pengguna atau password tidak sesuai.',
                'status_code' => 400
            ], 400);
        }
        $data = [
            'id'             => $user->id,
            'name'           => $user->name,
            'email'          => $user->email,
            'phone'          => $user->phone,
            'google_id'      => $user->google_id,
            'remember_token' => $user->remember_token,
            'country'        => $user->country,
            'client_id'      => $user->client_id,
            'regency'        => $user->Regency,
            'photo'          => $user->photo,
        ];

        return response()->json([
            'data' => $data,
            'access_token' => $user->createToken('authToken')->accessToken,
            'token_type' => 'Bearer',
            'status_code' => 200,
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:registration_user_mobile',
            'password' => 'required|string',
            'phone'    => 'nullable|string|max:20',
        ],[
            'required'      => ':attribute is required',
            'email.unique' => 'User with this email already exists',
            'email.email' => 'Email format is not correct',
        ]);

        $input = $request->all();

        $input['password'] = Hash::make($request->password);
        $user = RegistrationUser::create($input);
        // sending email
        Mail::to($user->email)
        ->locale('en')
        ->send(new RegistrationUserMail($user));

        return response()->json([
            'message' => 'Registration Success, check your email to verification',
        ]);
    }

    public function verification(RegistrationUser $registrationUser)
    {   
        $input = $registrationUser->makeVisible(['password'])->toArray();
        $input['email_verified_at'] = date('Y-m-d H:i:s');
        $input['client_id'] = 'mobile_app' . Str::random(16);
        $user = UserMobile::create($input);

        $registrationUser->is_email_verified = true;
        $registrationUser->save();
        $registrationUser->delete();

        return redirect(route('userMobile.afterVerified'));
    }

    public function afterVerified()
    {
        return view('afterRegisterUser');
    }
    public function loginWithGoogle(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        $response = Http::get('https://oauth2.googleapis.com/tokeninfo', [
            'id_token' => $request->token,
        ]);

        if ($response->failed()) {
            return response()->json(['message' => 'Invalid Google token'], 401);
        }

        $data = $response->json();

        if ($data['aud'] !== env('GOOGLE_CLIENT_ID')) {
            return response()->json(['message' => 'Invalid client ID'], 401);
        }

            $user = UserMobile::where('email', $data['email'])->first();
        if (!$user) {
            $user = UserMobile::create([
                'name' => $data['name'] ?? $data['email'],
                'email' => $data['email'],
                'password' => Hash::make(Str::random(16)),
                'google_id' => $data['sub'],
                'email_verified_at' => now(),
            ]);
        }

        $token = $user->createToken('authToken')->accessToken;

        return response()->json([
            'user'  => $user,
            'token' => $token,
        ]);
    }

    public function updateProfile(Request $request)
    {
         $request->validate([
            'name'     => 'nullable|string|max:255',
            'password' => 'nullable|string',
            'photo'    => 'nullable|image',
        ],[
            'required'      => ':attribute is required',
        ]);

        $user = UserMobile::with('Regency')->find(Auth::user()->id);

        $data = $request->except('photo');

        if (!empty($request->password)) {
            $data['password'] = Hash::make($request->password);
        } else {
            unset($data['password']);
        }

        if (empty($request->name)) {
            unset($data['name']);
        }

        if ($request->hasFile('photo')) {
            if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                Storage::disk('public')->delete($user->photo);
            }
            $data['photo'] = $request->file('photo')->store('user_mobile', 'public');
        }
        $user->update($data);

        $updatedData = [
            'id'             => $user->id,
            'name'           => $user->name,
            'email'          => $user->email,
            'phone'          => $user->phone,
            'google_id'      => $user->google_id,
            'remember_token' => $user->remember_token,
            'country'        => $user->country,
            'client_id'      => $user->client_id,
            'regency'        => $user->Regency,
            'photo'          => $user->photo,
        ];

        return response()->json([
            'message' => 'Profile Changed!',
            'data' => $updatedData
        ]);
    }

    public function logout(Request $request)
    {
        $user = Auth::user()->token();
        $user->revoke();

        return response()->json([
            'message' => 'Successfully Logout!'
        ]);
    }

    public function detail (){
        $check = Auth::check();
        if(!$check){
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }

         return response()->json([
                'message' => 'Detail Fetched!',
                'data' => Auth::user()
            ]);
    }
}
