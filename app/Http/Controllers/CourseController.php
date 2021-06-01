<?php

namespace App\Http\Controllers;


use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DB;

class CourseController extends Controller
{

	public function index()
	{
		$courses = Course::all();
    	return response()->json(['courses'=>$courses, 'message'=>'Course fetched Successfully'], 200);
	}


	public function add(Request $request){
    	$validator = Validator::make($request->all(), [
    		'course' =>'required|string',
    	]);

    	if ($validator->fails()) {
    		return response()->json(["message"=> $validator->errors()], 422);
    	}
    	try {
    		
        	$course             = new Course();
        	$course->course     = $request->get('course');
        	$course->save();

    		return response()->json(['course'=>$course, 'message'=>'Course Created Successfully'], 201);

    	} catch (Exception $e) {

    		return response()->json(['message'=>'An error occurred', 'error'=>$e->message()], 422);
    		
    	}

    }


    public function update(Request $request, $id ){

        $validator = Validator::make($request->all(), [
            'course' =>'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(["message"=> $validator->errors()], 422);
        }
        try {
            
            $course = Course::find($id);
            $course->course = $request->course;
            
            if ($course->save()) {
                return response()->json([
                    'course'=>$course, 
                    'message'=>'Course updated Successfully'
                ], 201);
            }else{

                return response()->json([
                    'message'=>'An error occured!'
                ], 501);

            }

        } catch (Exception $e) {

            return response()->json(['message'=>'An error occurred', 'error'=>$e->message()], 422);
            
        }

    }

    public function view($id)
    {
        $course = Course::find($id);
        return response()->json(['course'=>$course, 'message'=>'Course fetched Successfully'], 200);
    }


    public function delete($id)
    {
    	$course = Course::find($id);
        if ($course == NULL) {
            return response()->json([
            'message'=> 'An error occurred!'
        ], 500);
        }
            $course->delete();

        return response()->json([
            'message'=> 'Course Deleted Successfully!'
        ], 200); 
    }
    
}
