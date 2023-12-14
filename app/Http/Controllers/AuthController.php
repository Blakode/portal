<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{

    /***
     -------------------------------------
    | All Users registration method 
    -------------------------------------
    ***/
    public function register(Request $request) {

        // validation
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'role' => "required|in:student,parent,teacher,admin",
            'number' => 'sometimes|integer',
            'password' =>  'required|string|min:6'
        ]);

        if($validator->fails()){
            return response()->json(['error' => $validator->errors()], 400);
        }

        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'role' => $request->input('string'), 
            'number' => $request->input('number'), 
            'password' => Hash::make($request->input('password')), 
        ]);

        // Set the expiration time to 1 hour (60 minutes)
        $expirationTime = now()->addMinutes(30);

        // Create a claim for the expiration time
        $customClaims = ['exp' => $expirationTime->timestamp];

        //JTW Token
        $token = JWTAuth::fromUser($user,  $customClaims);

        return response()->json(['user' => $user, 'token' => $token]);
    }

    /***
     -------------------------------------
    | All Users login method 
    -------------------------------------
    ***/
    public function login(Request $request) {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email', 
            'password' => 'required|string|min:6'
        ]);

        $credentials = $request->only(['email', 'password']);

       try {
            if (! $token = JWTAuth::attempt($credentials, ['exp' => now()->addHours(48)->timestamp])) {
                return response()->json(['error' => 'Invalid credentials'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'Could not create token'], 500);
        }

        return response()->json(compact('token'));
    }

    public function logout() {
        
    }


}
