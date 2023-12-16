<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        // $this->middleware(['jwt.auth', 'jwt.refresh']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    return response()->json(['result' => 'active'], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //call register method from auth if registration !admin
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
/***
 -------------------------------------
| Fetch Specific User Data 
-------------------------------------
***/
    public function show($id)
    {
    return view('view test'); 
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    
/***
 -------------------------------------
| Fetch User Data 
-------------------------------------
***/
    public function getUserData() {
        try {
            $user = Auth::user();
            $user = JWTAuth::parseToken()->authenticate();
            return response()->json(['data' => $user]);
        } catch (TokenExpiredException $e) {
            try {
                // Refresh token
                $refreshToken = JWTAuth::refresh(JWTAuth::getToken());
                return response()
                    ->json(['data' => JWTAuth::setToken($refreshToken)->toUser()])
                    ->header('Authorization', 'Bearer ' . $refreshToken);
            } catch (JWTException $e) {
                // Unauthorized error during refresh
                return response()->json(['error' => '2nd Unauthorized', 'message' => $e->getMessage()], 401);
            }
        } catch (JWTException $e) {
            // Unauthorized error
            return response()->json(['error' => '1st Unauthorized', 'message' => $e->getMessage()], 401);
        }
    } 
/***
 -------------------------------------
| Logs a signed in user out
-------------------------------------
***/
public function logout() {
        // fetch authd user & clear tokens
        // return 'logout works'; 
        $user = auth()->user();
        $token = JWTAuth::getToken();
        JWTAuth::setToken($token)->invalidate();    
        return response()->json(['message' => 'Successfully logged out']);
}

}
