<?php

namespace App\Http\Controllers;

use App\Models\Mcq_general;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use DB;

class McqGeneralController extends Controller
{

	/*View All data*/ 
		public function index()
		{
			 $mcq_generals = DB::select('
	                SELECT * From mcq_generals LIMIT 100'
	            );

	        // $essay_dumps = Essay_dump::
	    	return response()->json(['mcq_generals'=>$mcq_generals, 'message'=>'MCQ fetched Successfully'], 200);
		}

	/*View all Data*/ 

	/*Add data*/ 

		public function add(Request $request){

	        $validator = Validator::make($request->all(), [
	            'question' 	=>'required|string',
	            'opt_a' 	=>'required|string',
	            'opt_b' 	=>'required|string',
	            'opt_c' 	=>'required|string',
	            'opt_c' 	=>'required|string',
	            'answer' 	=>'required|string',
	            'reason' 	=>'required|string',
	        ]);

	        if ($validator->fails()) {
	            return response()->json(["message"=> $validator->errors()], 422);
	        }
	        try {
	            
	            $mcq_general  = new Mcq_general();
	            $mcq_general->question     = $request->get('question');
	            $a = $mcq_general->opt_a     = $request->get('opt_a');
	            $b = $mcq_general->opt_b     = $request->get('opt_b');
	            $c = $mcq_general->opt_c     = $request->get('opt_c');
	            $d = $mcq_general->opt_d     = $request->get('opt_d');
	            $mcq_general->reason     = $request->get('reason');
	            if ($request->get('answer') == 'a') {
	            	$mcq_general->answer     = $a;
	            }elseif ($request->get('answer') == 'b') {
	            	$mcq_general->answer     = $b;
	            }elseif ($request->get('answer') == 'c') {
	            	$mcq_general->answer     = $c;
	            }else{
	            	$mcq_general->answer     = $d;
	            }

	            $mcq_general->save();

	            return response()->json(['mcq_general'=>$mcq_general, 'message'=>'MCQ Created Successfully'], 201);

	        } catch (Exception $e) {

	            return response()->json(['message'=>'An error occurred', 'error'=>$e->message()], 422);
	            
	        }

	    }

	/*Add data*/ 


	/*Update Data*/ 
	        
	    public function update(Request $request, $id){

	         $validator = Validator::make($request->all(), [
	            'question' 	=>'required|string',
	            'opt_a' 	=>'required|string',
	            'opt_b' 	=>'required',
	            'opt_c' 	=>'required',
	            'opt_c' 	=>'required',
	            'answer' 	=>'required',
	            'reason' 	=>'required',
	        ]); 

	        if ($validator->fails()) {
	            return response()->json(["message"=> $validator->errors()], 422);
	        }
	        try {
	            
	            $mcq_general  = Mcq_general::find($id);
	            $mcq_general->question     = $request->get('question');
	            $a = $mcq_general->opt_a     = $request->get('opt_a');
	            $b = $mcq_general->opt_b     = $request->get('opt_b');
	            $c = $mcq_general->opt_c     = $request->get('opt_c');
	            $d = $mcq_general->opt_d     = $request->get('opt_d');
	            $mcq_general->reason     = $request->get('reason');
	            
	             if ($request->get('answer') == 'a') {
	            	$mcq_general->answer     = $a;
	            }elseif ($request->get('answer') == 'b') {
	            	$mcq_general->answer     = $b;
	            }elseif ($request->get('answer') == 'c') {
	            	$mcq_general->answer     = $c;
	            }else{
	            	$mcq_general->answer     = $d;
	            }

	            if ($mcq_general->save()) {
	                return response()->json([
	                    'mcq_general'=>$mcq_general, 
	                    'message'=>'mcq updated Successfully'
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
	        $mcq_generals = DB::select('
	                SELECT * From mcq_generals WHERE id = ?', [$id]
	            );
	           
	            return response()->json(['mcq_generals'=>$mcq_generals, 'message'=>'Mcq Fetched fetched Successfully'], 200);
	    }

	/*view single data*/ 

	/*DELETE DATA*/ 
	    public function delete($id)
	    {
	    	$mcq_general = Mcq_general::find($id);
	        if ($mcq_general == NULL) {
	            return response()->json([
	            'message'=> 'An error occurred!'
	        ], 500);
	        }
	            $mcq_general->delete();

	        return response()->json([
	            'message'=> 'mcq Deleted Successfully!'
	        ], 200); 
	    }
	/*DELETE DATA*/ 




}
