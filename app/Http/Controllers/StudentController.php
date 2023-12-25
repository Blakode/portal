<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
    
        if ($user->role !== 'admin' && $user->role !== 'teacher') {
            return response()->json(['error' => 'Unauthorized. Only admins and teachers can access this information.'], 403);
        }
    
        $students = Student::all();
        return response()->json(['data' => $students], 200);
    
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
            'email' => 'nullable|email|unique:users',
            'avatar' => 'nullable',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 409);
        }

        $user = null;

        $student = Student::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'avatar' => $request->input('avatar'),
            'user_id' => $user ? $user->id : null,
            'password' => Hash::make($request->input('password')),
           
        ]);

        return response()->json(['message' => 'Student created successfully'], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function show(Student $student)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Student $student)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => [
                'nullable',
                'email',
                Rule::unique('students')->ignore($student->id, 'id'),
                Rule::unique('users')->ignore($student->user_id, 'id'),
            ],
            'avatar' => 'nullable',
            'password' => 'nullable|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 409);
        }

        if ($request->filled('password')) {
            $password = Hash::make($request->input('password'));
        } else {
            $password = $student->password;
        }

        $student->update([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'avatar' => $request->input('avatar'),
            'password' => Hash::make($request->input('password')),
            'user_id' => $request->input('user_id'), 
        ]);

        return response()->json(['message' => 'Student updated successfully'], 200);
        }
  

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function destroy(Student $student)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['error' => 'Only admins can delete students'], 403);
        }

        $student->delete();        
        return response()->json(['message' => 'Student deleted successfully'], 200);
    }

/***
 -------------------------------------
| link a student with a parent account
-------------------------------------
***/
    public function syncParent($parentId, $studentId) {

    $parent = User::findOrFail($parentId);

    if ($parent->role !== 'parent' && (Auth::user()->role == 'admin' || Auth::user()->role == 'teacher')) {
        return response()->json(['error' => 'Only users with the parent role can associate with students'], 403);
    }

    $student = Student::where('id', $studentId)->firstOrFail();
    $student->user_id = $parent->id;
    $student->save();
    return response()->json(['message' => 'Parent associated with student successfully'], 200);
    }

/***
 -------------------------------------
| Get all student associated with a parent
-------------------------------------
***/
    public function getStudentsForParent() {

    $loggedInUser = auth()->user();

    if ($loggedInUser && $loggedInUser->role === 'parent') {
        $students = $loggedInUser->students;
        return response()->json(['students' => $students], 200);
    }

    return response()->json(['error' => 'User is not a parent'], 403);
    }

/***
 -------------------------------------
| Get a parent and student associated to that parent
-------------------------------------
***/
    public  function getParentStudent($userId) {
        
    $user = User::findOrFail($userId);

    if ($user->role === 'parent') {
        $user->students;
        return response()->json(['user' => $user], 200);
    }

    return response()->json(['error' => 'Only users with the parent role can retrieve students.'], 403);
    }

}
