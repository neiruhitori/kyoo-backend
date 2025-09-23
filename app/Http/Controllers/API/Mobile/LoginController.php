<?php

namespace App\Http\Controllers\API\Mobile;

use App\Models\UserMobile;
use App\Models\PasswordOTP;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\RegistrationUser;
use App\Mail\RegistrationUserMail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use App\Mail\ForgotPasswordMobileMail;
use Illuminate\Support\Facades\Storage;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $user = UserMobile::with('Regency')->where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Username or password is incorrect',
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
            'regency'        => [ 
                                'id' => $user->Regency ? $user->Regency->id : null, 
                                'name' => $user->Regency ? $user->Regency->name : null
                                ],
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
            'password' => 'required|string|min:8',
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
                'client_id' => 'mobile_app'. Str::random(16),
                'email_verified_at' => now(),
                'regency' => null
            ]);
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
            'regency'        => [ 
                                'id' => $user->Regency ? $user->Regency->id : null, 
                                'name' => $user->Regency ? $user->Regency->name : null, 
                                ],
            'photo'          => $user->photo,
        ];
        $token = $user->createToken('authToken')->accessToken;

        return response()->json([
            'data'  => $data,
            'access_token' => $token,
            'token_type' => 'Bearer',
            'status_code' => 200,
        ]);
    }

    public function updateProfile(Request $request)
    {
         $request->validate([
            'name'     => 'nullable|string|max:255',
            'photo'    => 'nullable|image',
        ],[
            'required'      => ':attribute is required',
        ]);

        $user = UserMobile::with('Regency')->find(Auth::user()->id);

        $data = $request->except('photo');

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
            'regency'        =>  [ 
                                'id' => $user->Regency->id, 
                                'name' => $user->Regency->name
                                ],
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

    public function passwordVerif(Request $request)
    { 
        $request->validate([
            'password' => 'required'
        ],[
            'required' => 'Password is required'
        ]);

        $user = Auth::user();

        if(Hash::check($request->password, $user->password)){
            return response()->json([
                'message' => 'Password matched!',
                'matched' => true
            ]);
        }else{
            return response()->json([
                'message' => 'Password not matched!',
                'matched' => false
            ], 422);
        }
        
    }

    public function updatePassword(Request $request)
    { 
        $request->validate([
            'password' => 'required|min:8'
        ]);

        $user = UserMobile::with('Regency')->find(Auth::user()->id);
        $data = $request->all();
        $data['password'] = Hash::make($request->password);
        
        $user->update($data);

        $updated = [
            'id'             => $user->id,
            'name'           => $user->name,
            'email'          => $user->email,
            'phone'          => $user->phone,
            'google_id'      => $user->google_id,
            'remember_token' => $user->remember_token,
            'country'        => $user->country,
            'client_id'      => $user->client_id,
            'regency'        => [ 
                                'id' => $user->Regency->id, 
                                'name' => $user->Regency->name
                                ],
            'photo'          => $user->photo,
        ];

        return response()->json([
            'message' => 'Password Updated!',
            'data' => $updated
        ]);
        
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
                'email' => 'required|email'
        ],[
                'email.email' => 'The given data is not email',
                'email.required' => 'Email is required'
        ]);

        $user = UserMobile::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'message' => 'The selected email does not exist in our records.',
            ], 404);
        }

        if ($user->google_id) {
            return response()->json([
                'message' => 'This email is registered with Google account. Please login using Google.',
            ], 400);
        }

       $otp = random_int(100000,999999);
       $expired = now()->addMinutes(30);

       $verif = PasswordOTP::updateOrCreate(
                    [ 'email' => $request->email ],
                    [
                        'otp' => $otp,
                        'expires_at' => $expired
                    ]
                );

        //kirim mail
        Mail::to($request->email)->locale('en')->send(new ForgotPasswordMobileMail($verif));

        return response()->json([
            'message' => 'Email with OTP has been sent to your email.',
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email'    => 'required|email|exists:users_mobile,email',
            'otp'      => 'required|string',
            'password' => 'required|string|min:8',
        ],[
            'required' => ':attribute is required',
            'email.email' => 'The given data is not email',
        ]);

        $resetOTP = PasswordOTP::where('email', $request->email)->where('otp', $request->otp)->first();

        if (!$resetOTP) {
            return response()->json(['message' => 'Invalid OTP or email'], 400);
        }
        if (now()->greaterThan($resetOTP->expires_at)) {
            return response()->json(['message' => 'OTP has expired'], 400);
        }

        $user = UserMobile::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        $resetOTP->delete();

        return response()->json([
            'message' => 'Password reset successful.'
        ]);
    }
}
