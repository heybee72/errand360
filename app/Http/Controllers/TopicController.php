<?php

namespace App\Http\Controllers;


use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use DB;
// use JWTAuth;

class TopicController extends Controller
{

/*View All data*/ 
	public function index()
	{
        

		 $topics = DB::table('topics')
            ->leftJoin('courses', 'courses.id', '=', 'topics.course_id')
            ->select('course','topic','topic_type','paid','topics.created_at','topics.updated_at')
            ->orderBy('topics.id', 'desc')
            ->get();

        // $essay_dumps = Essay_dump::
    	return response()->json(['topics'=>$topics, 'message'=>'topics fetched Successfully'], 200);
	}

/*View all Data*/ 


/*View Course data*/ 
    public function topic_type(Request $request, $topic_type, $id)
    {
        $user = auth()->setToken($request->bearerToken())->user();

        if ($user == NULL) {
            return response()->json(['message' => 'user not found']);
        }

        if ($user->user_plan == 'free') {

           $topics = DB::select('
                SELECT * From topics where course_id = ? 
                AND topic_type = ? AND paid= ?
                ORDER BY id DESC ', [$id, $topic_type, 'free']
            );
            return response()->json(['topics'=>$topics, 'message'=>'topics fetched Successfully!!'], 200);

        }else{
            $topics = DB::select('
                SELECT * From topics where course_id = ? 
                AND topic_type = ? 
                ORDER BY id DESC ', [$id, $topic_type]
            );
            return response()->json(['topics'=>$topics, 'message'=>'topics fetched Successfully!!'], 200);
        }
    }

/*View Course data*/ 


/*View paid Course data*/ 
    public function topic_paid_only($topic_type, $id)
    {
        $user = auth()->setToken($request->bearerToken())->user();

        if ($user == NULL) {
            return response()->json(['message' => 'user not found']);
        }

            if ($user->user_plan == 'free') {
            $topics = DB::select('
                SELECT * From topics where course_id = ? 
                AND topic_type = ? AND paid= ?
                ORDER BY id DESC ', [$id, $topic_type, 'paid']
            );
            return response()->json(['topics'=>$topics, 'message'=>'topics fetched Successfully!!'], 200);
        }
    }

/*View paid Course data*/ 





 
/*Add data*/ 

	public function add(Request $request){

        $validator = Validator::make($request->all(), [
            'topic' =>'required|string',
            'course_id' =>'required',
            'topic_type' =>'required',
            'paid' =>'required',

        ]);

        if ($validator->fails()) {
            return response()->json(["message"=> $validator->errors()], 422);
        }
        try {
            
            $topic  = new topic();
            $topic->topic     = $request->get('topic');
            $topic->course_id     = $request->get('course_id');
            $topic->topic_type     = $request->get('topic_type');
            $topic->paid     = $request->get('paid');
            $topic->save();

            return response()->json(['topic'=>$topic, 'message'=>'Topic Created Successfully'], 201);

        } catch (Exception $e) {

            return response()->json(['message'=>'An error occurred', 'error'=>$e->message()], 422);
            
        }

    }

/*Add data*/ 

/*Update Data*/ 
        
    public function update(Request $request, $id){

         $validator = Validator::make($request->all(), [
            'topic' =>'required|string',
            'course_id' =>'required',
            'topic_type' =>'required|string',
            'paid' =>'required',
        ]);

        if ($validator->fails()) {
            return response()->json(["message"=> $validator->errors()], 422);
        }
        try {
            
            $topic  = Topic::find($id);
            $topic->topic     = $request->get('topic');
            $topic->course_id     = $request->get('course_id');
            $topic->topic_type     = $request->get('topic_type');
            $topic->paid     = $request->get('paid');
            
            if ($topic->save()) {
                return response()->json([
                    'topics'=>$topic, 
                    'message'=>'topic updated Successfully'
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
        $topics = DB::table('topics')
            ->leftJoin('courses', 'courses.id', '=', 'topics.course_id')

            ->select('course','topic','topics.created_at','topics.updated_at')
            ->orderBy('topics.id', 'desc')
            ->where('topics.id', $id)
            ->get();
           
            return response()->json(['topics'=>$topics, 'message'=>'topic fetched Successfully'], 200);
    }

/*view single data*/ 


/*DELETE DATA*/ 
    public function delete($id)
    {
    	$topics = topic::find($id);
        if ($topics == NULL) {
            return response()->json([
            'message'=> 'An error occurred!'
        ], 500);
        }
            $topics->delete();

        return response()->json([
            'message'=> 'topic Deleted Successfully!'
        ], 200); 
    }
/*DELETE DATA*/ 
    
}
