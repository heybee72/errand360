<?php

namespace App\Http\Controllers;


use App\Models\Lecture_note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use DB;
// use JWTAuth;

class Lecture_noteController extends Controller
{

/*View All data*/ 
	public function index()
	{
		 $lecture_notes = DB::table('lecture_notes')
            ->leftJoin('courses', 'courses.id', '=', 'lecture_notes.course_id')
            ->leftJoin('topics', 'topics.id', '=', 'lecture_notes.topic_id')

            ->select('lecture_notes.id','lecture_note_title','lecture_note','course','topic','lecture_notes.created_at','lecture_notes.updated_at')
            ->orderBy('lecture_notes.id', 'desc')
            ->get();

        // $essay_dumps = Essay_dump::
    	return response()->json(['lecture_notes'=>$lecture_notes, 'message'=>'Lecture Notes fetched Successfully'], 200);
	}

/*View all Data*/ 


/*Add data*/ 

	public function add(Request $request){

        $validator = Validator::make($request->all(), [
            'lecture_note_title' =>'required|string',
            'lecture_note' =>'required|string',
            'course_id' =>'required',
            'topic_id' =>'required',
        ]);

        if ($validator->fails()) {
            return response()->json(["message"=> $validator->errors()], 422);
        }
        try {
            
            $lecture_note  = new Lecture_note();
            $lecture_note->lecture_note_title     = $request->get('lecture_note_title');
            $lecture_note->lecture_note     = $request->get('lecture_note');
            $lecture_note->course_id     = $request->get('course_id');
            $lecture_note->topic_id     = $request->get('topic_id');
            $lecture_note->save();

            return response()->json(['lecture_note'=>$lecture_note, 'message'=>'lecture note Created Successfully'], 201);

        } catch (Exception $e) {

            return response()->json(['message'=>'An error occurred', 'error'=>$e->message()], 422);
            
        }

    }

/*Add data*/ 

/*Update Data*/ 
        
    public function update(Request $request, $id){

        $validator = Validator::make($request->all(), [
            'lecture_note_title' =>'required|string',
            'lecture_note' =>'required|string',
            'course_id' =>'required',
            'topic_id' =>'required',
        ]);

        if ($validator->fails()) {
            return response()->json(["message"=> $validator->errors()], 422);
        }
        try {
            
            $lecture_note  = Lecture_note::find($id);
            $lecture_note->lecture_note_title     = $request->get('lecture_note_title');
            $lecture_note->lecture_note     = $request->get('lecture_note');
            $lecture_note->course_id     = $request->get('course_id');
            $lecture_note->topic_id     = $request->get('topic_id');
            
            if ($lecture_note->save()) {
                return response()->json([
                    'lecture_note'=>$lecture_note, 
                    'message'=>'Lecture notes updated Successfully'
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
        $lecture_note = DB::table('lecture_notes')
            ->leftJoin('courses', 'courses.id', '=', 'lecture_notes.course_id')
            ->leftJoin('topics', 'topics.id', '=', 'lecture_notes.topic_id')

            ->select('lecture_notes.id','lecture_note_title','lecture_note','course','topic',
                'lecture_notes.created_at','lecture_notes.updated_at')
            ->orderBy('lecture_notes.id', 'desc')
            ->where('lecture_notes.id', $id)
            ->get();
           
            return response()->json(['lecture_note'=>$lecture_note, 'message'=>'Lecture notes fetched Successfully'], 200);
    }

/*view single data*/ 

/*view single data by topic*/ 

    public function view_by_topic(Request $request, $course_id, $topic_id)
    {
         $user = auth()->setToken($request->bearerToken())->user();

            if ($user == NULL) {
                return response()->json(['message' => 'user not found']);
            }

        $lecture_note = DB::select('
                SELECT * From lecture_notes where topic_id = ?  AND course_id = ?
                ORDER BY id ASC ', [$topic_id, $course_id]
            );
           
            return response()->json(['lecture_note'=>$lecture_note, 'message'=>'Lecture notes fetched Successfully'], 200);
    }

/*view single data by topic*/ 

/*DELETE DATA*/ 
    public function delete($id)
    {
    	$lecture_note = Lecture_note::find($id);
        if ($lecture_note == NULL) {
            return response()->json([
            'message'=> 'An error occurred!'
        ], 500);
        }
            $lecture_note->delete();

        return response()->json([
            'message'=> 'Lecture notes Deleted Successfully!'
        ], 200); 
    }
/*DELETE DATA*/ 
    
}
