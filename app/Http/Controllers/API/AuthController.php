<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name'=>'required',
            'email'=>'required|email|unique:users',
            'password'=>'required|min:8',
            'role'=>'required|in:admin,doctor,patient'
        ]);


        $user=User::create([

            'name'=>$request->name,

            'email'=>$request->email,

            'password'=>Hash::make(
                $request->password
            ),

            'role'=>$request->role

        ]);


        $user->sendEmailVerificationNotification();


        $token=$user
            ->createToken('auth_token')
            ->plainTextToken;

        return response()->json([

            'message'=>'Register success',

            'token'=>$token,

            'user'=>$user

        ],201);

    }

     public function login(Request $request)
    {

        if(
            !Auth::attempt([
                'email'=>$request->email,
                'password'=>$request->password
            ])
        ){

            return response()->json([
                'message'=>'Unauthorized'
            ],401);

        }


        $user=Auth::user();


        $token=$user
            ->createToken('auth_token')
            ->plainTextToken;


        return response()->json([

            'message'=>'Login success',

            'token'=>$token,

            'user'=>$user

        ]);
    }

     public function logout(Request $request)
    {

        $request
            ->user()
            ->currentAccessToken()
            ->delete();


        return response()->json([

            'message'=>'Logout success'

        ]);

    }
}
