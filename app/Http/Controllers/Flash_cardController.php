<?php

namespace App\Http\Controllers;


use App\Models\Flash_card;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use DB;
// use JWTAuth;

class Flash_cardController extends Controller
{

/*View All data*/ 
	public function index()
	{
		 $flash_cards = DB::table('flash_cards')
            ->leftJoin('courses', 'courses.id', '=', 'flash_cards.course_id')
            ->leftJoin('topics', 'topics.id', '=', 'flash_cards.topic_id')

            ->select('flash_cards.id','flash_card_question','flash_card_answer','course','topic','flash_cards.created_at','flash_cards.updated_at')
            ->orderBy('flash_cards.id', 'desc')
            ->get();

        // $essay_dumps = Essay_dump::
    	return response()->json(['flash_cards'=>$flash_cards, 'message'=>'flash card fetched Successfully'], 200);
	}

/*View all Data*/ 


/*Add data*/ 

	public function add(Request $request){

        $validator = Validator::make($request->all(), [
            'flash_card_question' =>'required|string',
            'flash_card_answer' =>'required|string',
            'course_id' =>'required',
            'topic_id' =>'required',
        ]);

        if ($validator->fails()) {
            return response()->json(["message"=> $validator->errors()], 422);
        }
        try {
            
            $flash_card  = new Flash_card();
            $flash_card->flash_card_question     = $request->get('flash_card_question');
            $flash_card->flash_card_answer     = $request->get('flash_card_answer');
            $flash_card->course_id     = $request->get('course_id');
            $flash_card->topic_id     = $request->get('topic_id');
            $flash_card->save();

            return response()->json(['flash_card'=>$flash_card, 'message'=>'Flash Card Created Successfully'], 201);

        } catch (Exception $e) {

            return response()->json(['message'=>'An error occurred', 'error'=>$e->message()], 422);
            
        }

    }

/*Add data*/ 

/*Update Data*/ 
        
    public function update(Request $request, $id){

        $validator = Validator::make($request->all(), [
            'flash_card_question' =>'required|string',
            'flash_card_answer' =>'required|string',
            'course_id' =>'required',
            'topic_id' =>'required',
        ]);

        if ($validator->fails()) {
            return response()->json(["message"=> $validator->errors()], 422);
        }
        try {
            
            $flash_card = Flash_card::find($id);
            $flash_card->flash_card_question     = $request->get('flash_card_question');
            $flash_card->flash_card_answer     = $request->get('flash_card_answer');
            $flash_card->course_id     = $request->get('course_id');
            $flash_card->topic_id     = $request->get('topic_id');
            
            if ($flash_card->save()) {
                return response()->json([
                    'flash_card'=>$flash_card, 
                    'message'=>'Flash card updated Successfully'
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
        $flash_card = DB::table('flash_cards')
            ->leftJoin('courses', 'courses.id', '=', 'flash_cards.course_id')
            ->leftJoin('topics', 'topics.id', '=', 'flash_cards.topic_id')

            ->select('flash_cards.id','flash_card_question','flash_card_answer','course','topic','flash_cards.created_at','flash_cards.updated_at')
            ->orderBy('flash_cards.id', 'desc')
            ->where('flash_cards.id', $id)
            ->get();
           
            return response()->json(['flash_card'=>$flash_card, 'message'=>'flash card fetched Successfully'], 200);
    }

/*view single data*/ 

public function view_flashcards_by_topic (Request $request, $course_id, $topic_id){

    $user = auth()->setToken($request->bearerToken())->user();

        if ($user == NULL) {
            return response()->json(['message' => 'user not found']);
        }

    $flash_cards = DB::select('
        SELECT * From flash_cards where topic_id = ?  AND course_id = ?
        ORDER BY id ASC ', [$topic_id, $course_id]
    );
    return response()->json(['flash_cards'=>$flash_cards, 'message'=>'flash card fetched Successfully'], 200);
}


/*DELETE DATA*/ 
    public function delete($id)
    {
    	$flash_card = Flash_card::find($id);
        if ($flash_card == NULL) {
            return response()->json([
            'message'=> 'An error occurred!'
        ], 500);
        }
            $flash_card->delete();

        return response()->json([
            'message'=> 'flash card Deleted Successfully!'
        ], 200); 
    }
/*DELETE DATA*/ 
    
}
