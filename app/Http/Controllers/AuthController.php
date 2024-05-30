<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use HttpResponses;

    public function login(LoginUserRequest $request)
    {
        $request->validated($request->all());

        if(!Auth::attempt($request->only(['email', 'password']))) {
            return $this->error('', 'Credentials do not match', 401);
        }

        $user = User::where('email', $request->email)->first();

        return $this->success([
            'user' => $user,
            'token' => $user->createToken('API Token')->plainTextToken
        ], 'Login Successful',);
    }


    public function register(StoreUserRequest $request)
    {
        $request->validated();

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            return $this->success([
                'user' => $user,
            ], 'Account created successfully');
        } catch (\Exception $e) {
            return $this->error([], 'Registration failed', 500);
        }
    }

    public function logout(){
       Auth::user()->currentAccessToken()->delete();
    //    Auth::user()->currentAccessToken()->delete();

       return $this->success([
        'message' => 'You have successfully been logged out'
       ],'');
    }

}
