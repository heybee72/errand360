<?php

namespace App\Http\Controllers;

use App\Models\Mcq_per_topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use DB;

class McqPerTopicController extends Controller
{

	/*View All data*/ 
		public function index()
		{
			 $mcq_per_topics = DB::table('mcq_per_topics')
	            ->leftJoin('courses', 'courses.id', '=', 'mcq_per_topics.course_id')
	            ->leftJoin('topics', 'topics.id', '=', 'mcq_per_topics.topic_id')

	            ->select('mcq_per_topics.id','question','opt_a','opt_b','opt_c','opt_d','answer','reason','course','topic','mcq_per_topics.created_at','mcq_per_topics.updated_at')
	            ->orderBy('mcq_per_topics.id', 'desc')
	            ->get();

	        // $essay_dumps = Essay_dump::
	    	return response()->json(['mcq_per_topics'=>$mcq_per_topics, 'message'=>'mcq per topic fetched Successfully'], 200);
		}

	/*View all Data*/ 

	/*Add data*/ 

		public function add(Request $request){

	        $validator = Validator::make($request->all(), [
	            'question' 	=>'required|string',
	            'opt_a' 	=>'required|string',
	            'opt_b' 	=>'required',
	            'opt_c' 	=>'required',
	            'opt_c' 	=>'required',
	            'answer' 	=>'required',
	            'reason' 	=>'required',
	            'topic_id' 	=>'required',
	            'course_id' =>'required',
	        ]);

	        if ($validator->fails()) {
	            return response()->json(["message"=> $validator->errors()], 422);
	        }
	        try {
	            
	            $mcq_per_topic  = new Mcq_per_topic();

	            $mcq_per_topic->question     = $request->get('question');
	            $a = $mcq_per_topic->opt_a     = $request->get('opt_a');
	            $b = $mcq_per_topic->opt_b     = $request->get('opt_b');
	            $c = $mcq_per_topic->opt_c     = $request->get('opt_c');
	            $d = $mcq_per_topic->opt_d     = $request->get('opt_d');
	            $mcq_per_topic->reason     = $request->get('reason');
	            $mcq_per_topic->topic_id     = $request->get('topic_id');
	            $mcq_per_topic->course_id     = $request->get('course_id');

	            if ($request->get('answer') == 'a') {
	            	$mcq_per_topic->answer     = $a;
	            }elseif ($request->get('answer') == 'b') {
	            	$mcq_per_topic->answer     = $b;
	            }elseif ($request->get('answer') == 'c') {
	            	$mcq_per_topic->answer     = $c;
	            }else{
	            	$mcq_per_topic->answer     = $d;
	            }
	            $mcq_per_topic->save();

	            return response()->json(['mcq_per_topic'=>$mcq_per_topic, 'message'=>'mcq per topic Created Successfully'], 201);

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
	            'topic_id' 	=>'required',
	            'course_id' =>'required',
	        ]);

	        if ($validator->fails()) {
	            return response()->json(["message"=> $validator->errors()], 422);
	        }
	        try {
	            
	            $mcq_per_topic  = Mcq_per_topic::find($id);
	            $mcq_per_topic->question     = $request->get('question');
	            $a = $mcq_per_topic->opt_a     = $request->get('opt_a');
	            $b = $mcq_per_topic->opt_b     = $request->get('opt_b');
	            $c = $mcq_per_topic->opt_c     = $request->get('opt_c');
	            $d = $mcq_per_topic->opt_d     = $request->get('opt_d');
	            $mcq_per_topic->reason     = $request->get('reason');
	            $mcq_per_topic->topic_id     = $request->get('topic_id');
	            $mcq_per_topic->course_id     = $request->get('course_id');
	            
	             if ($request->get('answer') == 'a') {
	            	$mcq_per_topic->answer     = $a;
	            }elseif ($request->get('answer') == 'b') {
	            	$mcq_per_topic->answer     = $b;
	            }elseif ($request->get('answer') == 'c') {
	            	$mcq_per_topic->answer     = $c;
	            }else{
	            	$mcq_per_topic->answer     = $d;
	            }
	            
	            if ($mcq_per_topic->save()) {
	                return response()->json([
	                    'mcq_per_topic'=>$mcq_per_topic, 
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


	/*view single data by topic*/ 

	    public function view_by_topic(Request $request, $course_id, $topic_id)
	    {
	         $user = auth()->setToken($request->bearerToken())->user();

	            if ($user == NULL) {
	                return response()->json(['message' => 'user not found']);
	            }

	        $mcq_per_topic = DB::select('
	                SELECT * From mcq_per_topics where topic_id = ?  AND course_id = ?
	                ORDER BY id ASC LIMIT 100', [$topic_id, $course_id]
	            );
	           
	            return response()->json(['mcq_per_topic'=>$mcq_per_topic, 'message'=>'mcq per topic fetched Successfully'], 200);
	    }

	/*view single data by topic*/ 

	/*view single data*/ 

	    public function view($id)
	    {
	        $mcq_per_topic = DB::table('mcq_per_topics')
	            ->leftJoin('courses', 'courses.id', '=', 'mcq_per_topics.course_id')
	            ->leftJoin('topics', 'topics.id', '=', 'mcq_per_topics.topic_id')

	            ->select('mcq_per_topics.id','question','opt_a','opt_b','opt_c','opt_d','answer','reason','course','topic','mcq_per_topics.created_at','mcq_per_topics.updated_at')

	            ->where('mcq_per_topic.id', $id)
	            ->get();
	           
	            return response()->json(['mcq_per_topic'=>$mcq_per_topic, 'message'=>'Mcq Fetched fetched Successfully'], 200);
	    }

	/*view single data*/ 

	/*DELETE DATA*/ 
	    public function delete($id)
	    {
	    	$mcq_per_topic = Mcq_per_topic::find($id);
	        if ($mcq_per_topic == NULL) {
	            return response()->json([
	            'message'=> 'An error occurred!'
	        ], 500);
	        }
	            $mcq_per_topic->delete();

	        return response()->json([
	            'message'=> 'mcq Deleted Successfully!'
	        ], 200); 
	    }
	/*DELETE DATA*/ 




}
