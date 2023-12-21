<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Facades\JWTAuth;


class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['jwt.auth']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized. Only admins can access this information.'], 403);
        }

        $users = User::all();
        return response()->json(['data' => $users], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'required|in:student,parent,teacher"',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 409);
        }

        // Create user without checking for the 'admin' role
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'role' => $request->input('role'),
        ]);

        return response()->json(['message' => 'User created successfully'], 200);
 
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
        
    }

    /**
     * Update update a loggedin User
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {        
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'email' => 'required|email|unique:users,email,' . Auth::id(),
                'password' => 'nullable|min:6',
                'role' => 'required|in:admin,parent,teacher',
            ]);
        
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 409);
            }
        
            $user = User::find(Auth::user()->id);
            // Update user information
            $user->name = $request->input('name');
            $user->email = $request->input('email');           
            $user->role = $request->input('role');
            $user->save(); 
        
            return response()->json(['message' => 'User updated successfully'], 200);
    }
    
/***
 -------------------------------------
| Fetch User Data 
-------------------------------------
***/
    public function getUserData()
    {
        try {
            $user = Auth::user();
            $user = JWTAuth::parseToken()->authenticate();
            return response()->json(['data' => $user]);
        } 
        catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            // Handle token invalidation (blacklist)
            return response()->json(['error' => 'Unauthorized', 'message' => $e->getMessage()], 401);
        } catch (TokenExpiredException $e) {
            try {
                // Refresh token
                $refreshToken = JWTAuth::refresh(JWTAuth::getToken());
                JWTAuth::setToken($refreshToken);
                request()->headers->set('Authorization', 'Bearer ' . $refreshToken);
                $newToken = JWTAuth::setToken($refreshToken)->toUser();
                return response()
                    ->json(['data' => $newToken])
                    ->header('Authorization', 'Bearer ' . $refreshToken);
            } catch (JWTException $e) {
                // Unauthorized error during refresh
                return response()->json(['error' => 'Unauthorized', 'message' => $e->getMessage()], 401);
            }
        } catch (JWTException $e) {
            // Unauthorized error
            return response()->json(['error' => 'Unauthorized', 'message' => $e->getMessage()], 401);
        }

    }

/***
 -------------------------------------
| Logs a signed in user out
-------------------------------------
***/
public function logout() {
       
        try {
            $user = auth()->user();
            $token = JWTAuth::getToken();
            JWTAuth::setToken($token)->invalidate();    
            return response()
            ->json(['message' => 'Successfully logged out']);
        } catch (\Exception $e) {
                return response()->json(['error' => 'Logout failed'], 500);
        }
}

/***
 -------------------------------------
| Deletes the seleted user account 
-------------------------------------
***/
public  function destroy($id) 
    {
    if (Auth::user()->role !== 'admin') {
        return response()->json(['error' => 'Unauthorized. Only admins can delete accounts.'], 403);
    }
    
    $user = User::find($id);
    if (!$user) {
        return response()->json(['error' => 'User not found.'], 404);
    }
    
    if ($user->id === Auth::id()) {
        return response()->json(['error' => 'Admins cannot delete their own account.'], 403);
    }

    $user->delete();
    return response()->json(['message' => 'User deleted successfully'], 200);
        }

/***
 -------------------------------------
| Update the secondary account with the right permission
-------------------------------------
***/
public function updateUser(Request $request) {
    
}

}
