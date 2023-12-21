<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use Facade\FlareClient\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GradeController extends Controller
{
   /**
     * Fetch all grades 
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function index()
    {
       $grades = Grade::all(); 
       return response()->json(['data' => $grades], 200);
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
        ]); 

        if($validator->fails()){
            return response()->json(['error'=> $validator->errors()], 409);
        }

        $grade = Grade::create([
            'name' => $request->input('name'),
        ]); 
        return response()->json(['message' => 'grade created successfully'], 200); 
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $grade = Grade::findOrFail($id);
            return response()->json(['data' => $grade], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Grade not found', 'message' => $e->getMessage()], 404);
        }
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
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 409);
        }

        $grade = Grade::find($id);

        if (!$grade) {
            return response()->json(['error' => 'Grade not found'], 404);
        }

        $grade->name = $request->input('name');
        $grade->save();

        return response()->json(['message' => 'Grade updated successfully'], 200);
        }
 

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $grade = Grade::findOrFail($id);
            $grade->delete();
            return response()->json(['message' => 'Grade deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete grade', 'message' => $e->getMessage()], 500);
        }
     }
    
}
