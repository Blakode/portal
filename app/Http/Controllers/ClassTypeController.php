<?php

namespace App\Http\Controllers;

use App\Models\ClassType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class ClassTypeController extends Controller
{
    /**
     * Fetch the classes resource provided user is admin
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
    
        if ($user->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized. Only admins and teachers can access this information.'], 403);
        }

        $classes = ClassType::all();
        return response()->json(['data' => $classes], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            Gate::authorize('admin-gate');

            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'grade_id' => 'required|exists:grades,id',
            ]);

            if ($validator->fails()) {
                throw ValidationException::withMessages($validator->errors()->toArray());
            }

            $classType = ClassType::create([
                'name' => $request->input('name'),
                'grade_id' => $request->input('grade_id'),
            ]);

            return response()->json(['message' => 'Class type created successfully'], 201);
        }
         catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 409);
        } 
        catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ClassType  $class
     * @return \Illuminate\Http\Response
     */
    public function show(ClassType $class)
    {
        try {
            Gate::authorize('admin-teacher-gate');            
            return response()->json(['data' => $class], 200);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

        /**
     * Display the specified resource with relation ship details 
     *
     * @param  \App\Models\ClassType  $class
     * @return \Illuminate\Http\Response
     */
    public function showDetails(ClassType $class)
    {
        try {
            Gate::authorize('admin-teacher-gate');
               
            $class->load('class_student', 'teachers', 'grade');
            return response()->json(['data' => $class], 200);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ClassType  $class
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ClassType $class)
    {
    try {
        Gate::authorize('admin-gate');

       $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'grade_id' => 'required|exists:grades,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $class->name = $request->input('name');
        $class->grade_id = $request->input('grade_id');
        $class->save();

        return response()->json(['message' => 'Class updated successfully'], 200);
    }
     catch (\Throwable $e) {
        return response()->json(['error' => 'An unexpected error occurred'], 500);
    }
}

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ClassType  $class
     * @return \Illuminate\Http\Response
     */
    public function destroy(ClassType $class)
    {
        try {
            Gate::authorize('admin-gate');
            $class->delete();

            return response()->json(['message' => 'Class deleted successfully'], 200);
        } 
        catch (\Throwable $e) {
            return response()->json(['error' => 'Failed to delete class'], 500);
        }
    }

/***
 -------------------------------------
| Only user with admin role would use associated method
-------------------------------------
***/
    public  function syncTeacher($teacherId, $classTypeId) {

        try {
            Gate::authorize('admin-teacher-gate');

            $teacher = User::findOrFail($teacherId);
            $classType = ClassType::findOrFail($classTypeId);
            $classType->teachers()->attach($teacher->id);

            return response()->json(['message' => 'Teacher associated with class successfully'], 200);
        } catch (\Throwable $e) {
            return response()->json(['An error occured while associating a teacher with the class' ,'details' => $e->getMessage()], 500);
        }
    
    }
}
