<?php

namespace App\Http\Controllers;


use App\Models\Mcq_exam_year;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use DB;
// use JWTAuth;

class Mcq_exam_yearController extends Controller
{

/*View All data*/ 
	public function index()
	{
        $mcq_exam_year = Mcq_exam_year::all();
		 

        // $essay_dumps = Essay_dump::
    	return response()->json(['mcq_exam_year'=>$mcq_exam_year, 'message'=>'Mcqs exam Year fetched Successfully'], 200);
	}

/*View all Data*/ 


/*Add data*/ 

	public function add(Request $request){

        $validator = Validator::make($request->all(), [
            'exam_year' =>'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(["message"=> $validator->errors()], 422);
        }
        try {
            
            $mcq_exam_year     = new Mcq_exam_year();
            $mcq_exam_year->exam_year     = $request->get('exam_year');
            $mcq_exam_year->save();

            return response()->json(['mcq_exam_year'=>$mcq_exam_year, 'message'=>'mcq exam year Created Successfully'], 201);

        } catch (Exception $e) {

            return response()->json(['message'=>'An error occurred', 'error'=>$e->message()], 422);
            
        }

    }

/*Add data*/ 


/*Update Data*/ 
        
    public function update(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'exam_year' =>'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(["message"=> $validator->errors()], 422);
        }
        try {
            
            $mcq_exam_years = Mcq_exam_year::find($id);
            $mcq_exam_years->exam_year = $request->exam_year;
            
            if ($mcq_exam_years->save()) {
                return response()->json([
                    'mcq_exam_years'=>$mcq_exam_years, 
                    'message'=>'mcq exam years updated Successfully'
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


/*Update Data*/ 


/*view single data*/ 

    public function view($id)
    {
        $mcq_exam_year = Mcq_exam_year::find($id);
           
            return response()->json(['mcq_exam_year'=>$mcq_exam_year, 'message'=>'Mcq exam year fetched Successfully'], 200);
    }

/*view single data*/ 


/*DELETE DATA*/ 
    public function delete($id)
    {
    	$mcq_exam_year = Mcq_exam_year::find($id);
        if ($mcq_exam_year == NULL) {
            return response()->json([
            'message'=> 'An error occurred!'
        ], 500);
        }
            $mcq_exam_year->delete();

        return response()->json([
            'message'=> 'mcq exam year Deleted Successfully!'
        ], 200); 
    }
/*DELETE DATA*/ 
    
}
