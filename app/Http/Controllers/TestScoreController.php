<?php

namespace App\Http\Controllers;

use App\Models\Test_score;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DB;


class TestScoreController extends Controller
{


	/*View score per user*/ 

		public function view_per_user(Request $request)
		{
	         $user = auth()->setToken($request->bearerToken())->user();

	        if ($user == NULL) {

	            return response()->json(['message'=>'User not found!'], 400);
	        }

	       $test_score =  DB::table('test_scores')
            ->leftJoin('courses', 'courses.id', '=', 'test_scores.course_id')
            ->leftJoin('topics', 'topics.id', '=', 'test_scores.topic_id')
            ->leftJoin('users', 'users.id', '=', 'test_scores.user_id')

            ->select(
            	'test_scores.id',
            	'time',
            	'score',
            	'name',
            	'email',
            	'course',
            	'topic',
            	'type',
            	'test_scores.created_at'
            )
            ->orderBy('test_scores.id', 'desc')
            ->where('test_scores.user_id', $user->id)

            ->get();


	       // DB::select('
	       //      SELECT * From test_scores where user_id = ? ORDER BY id DESC ', [$user->id]);
	        
	    	    return response()->json(['test_score'=>$test_score, 'message'=>'Test scores fetched Successfully'], 200);
	        
		}

	/*View score per user*/ 


	/*View all data*/ 
		public function all_scores(Request $request)
		{
	        
	       $test_scores = DB::table('test_scores')
            ->leftJoin('courses', 'courses.id', '=', 'test_scores.course_id')
            ->leftJoin('topics', 'topics.id', '=', 'test_scores.topic_id')
            ->leftJoin('users', 'users.id', '=', 'test_scores.user_id')

            ->select('test_scores.id','time','score','name','email','course','topic','test_scores.created_at','test_scores.updated_at')
            ->orderBy('test_scores.id', 'desc')
            ->get();

        // $essay_dumps = Essay_dump::
    	return response()->json(['test_scores'=>$test_scores, 'message'=>'Test Scores fetched Successfully'], 200);
	        
		}

	/*View all Data*/ 


	/*Add data*/ 

		public function add_flash_card(Request $request){

			$user = auth()->setToken($request->bearerToken())->user();

	        if ($user == NULL) {

	            return response()->json(['message'=>'User not found!'], 400);
	        }

	        $validator = Validator::make($request->all(), [
	            'score' =>'required|string',
	            'topic_id' =>'required|string',
	            'course_id' =>'required|string',
	        ]);

	        if ($validator->fails()) {
	            return response()->json(["message"=> $validator->errors()], 422);
	        }
	        try {
	            
	            $test_score     = new Test_score();
	            $test_score->time     = $request->get('time');
	            $test_score->score     = $request->get('score');
	            $test_score->user_id     = $user->id;
	            $test_score->course_id     = $request->get('course_id');
	            $test_score->topic_id     = $request->get('topic_id');
	            $test_score->type     = 'flashcard';
	            $test_score->save();

	            return response()->json(['test_score'=>$test_score, 'message'=>'test score Created Successfully'], 201);

	        } catch (Exception $e) {

	            return response()->json(['message'=>'An error occurred', 'error'=>$e->message()], 422);
	            
	        }

	    }

	/*Add data*/ 




	/*Add data*/ 

		public function add_mcq(Request $request){

			$user = auth()->setToken($request->bearerToken())->user();

	        if ($user == NULL) {

	            return response()->json(['message'=>'User not found!'], 400);
	        }

	        $validator = Validator::make($request->all(), [
	            'time' =>'required|string',
	            'score' =>'required|string',
	            'topic_id' =>'required|string',
	            'course_id' =>'required|string',
	        ]);

	        if ($validator->fails()) {
	            return response()->json(["message"=> $validator->errors()], 422);
	        }
	        try {
	            
	            $test_score     		= new Test_score();
	            $test_score->time     	= $request->get('time');
	            $test_score->score     	= $request->get('score');
	            $test_score->user_id    = $user->id;
	            $test_score->course_id  = $request->get('course_id');
	            $test_score->topic_id   = $request->get('topic_id');
	            $test_score->type     	= 'mcq';
	            $test_score->save();

	            return response()->json(['test_score'=>$test_score, 'message'=>'test score Created Successfully'], 201);

	        } catch (Exception $e) {

	            return response()->json(['message'=>'An error occurred', 'error'=>$e->message()], 422);
	            
	        }

	    }

	/*Add data*/ 




	/*Add data*/ 

		public function add_test_score(Request $request){

			$user = auth()->setToken($request->bearerToken())->user();

	        if ($user == NULL) {

	            return response()->json(['message'=>'User not found!'], 400);
	        }

	        $validator = Validator::make($request->all(), [
	            'time' =>'required|string',
	            'score' =>'required|string',
	        ]);

	        if ($validator->fails()) {
	            return response()->json(["message"=> $validator->errors()], 422);
	        }
	        try {
	            
	            $test_score     = new Test_score();
	            $test_score->time     = $request->get('time');
	            $test_score->score     = $request->get('score');
	            $test_score->user_id     = $user->id;
	            $test_score->type     = 'test';
	            $test_score->save();

	            return response()->json(['test_score'=>$test_score, 'message'=>'test score Created Successfully'], 201);

	        } catch (Exception $e) {

	            return response()->json(['message'=>'An error occurred', 'error'=>$e->message()], 422);
	            
	        }

	    }

	/*Add data*/ 



	/*DELETE DATA*/ 
	    public function delete($id)
	    {
	    	$test_score = Test_score::find($id);
	        if ($test_score == NULL) {
	            return response()->json([
	            'message'=> 'An error occurred!'
	        ], 500);
	        }
	            $test_score->delete();

	        return response()->json([
	            'message'=> 'test score Deleted Successfully!'
	        ], 200); 
	    }
	/*DELETE DATA*/ 
    
}
