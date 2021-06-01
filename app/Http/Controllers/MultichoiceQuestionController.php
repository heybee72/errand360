<?php

namespace App\Http\Controllers;

use App\Models\Multichoice_question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use DB;

class MultichoiceQuestionController extends Controller
{
      /*Add data*/ 

	public function add(Request $request){

        $validator = Validator::make($request->all(), [
            'year_id' =>'required',
            'course_id' =>'required',
            'content' =>'required',
        ]);

        if ($validator->fails()) {
            return response()->json(["message"=> $validator->errors()], 422);
        }
        try {
            
            $multichoice_question     = new multichoice_question();
            $multichoice_question->year_id     = $request->get('year_id');
            $multichoice_question->course_id     = $request->get('course_id');
            $multichoice_question->content     = $request->get('content');
            $multichoice_question->save();

            return response()->json(['multichoice_question'=>$multichoice_question, 'message'=>'multichoice question Created Successfully'], 201);

        } catch (Exception $e) {

            return response()->json(['message'=>'An error occurred', 'error'=>$e->message()], 422);
            
        }

    }

/*Add data*/ 


/*Update Data*/ 
        
    public function update(Request $request, $id){


        $validator = Validator::make($request->all(), [
            'year_id' =>'required',
            'course_id' =>'required',
            'content' =>'required',
        ]);

        if ($validator->fails()) {
            return response()->json(["message"=> $validator->errors()], 422);
        }
        try {
            
            $multichoice_question  = multichoice_question::find($id);
            $multichoice_question->year_id     = $request->get('year_id');
            $multichoice_question->course_id     = $request->get('course_id');
            $multichoice_question->content     = $request->get('content');
            
            if ($multichoice_question->save()) {
                return response()->json([
                    'multichoice_question'=>$multichoice_question, 
                    'message'=>'multichoice question updated Successfully'
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

    /*View All data*/ 
		public function index()
		{
			 $multichoice_questions = DB::table('multichoice_questions')

	            ->leftJoin('courses', 'courses.id', '=', 'multichoice_questions.course_id')
	            ->leftJoin('mcq_exam_years', 'mcq_exam_years.id', '=', 'multichoice_questions.year_id')

	            ->select(
	            	'multichoice_questions.id',
	            	'course',
	            	'exam_year',
	            	'content',
	            	
	            	'multichoice_questions.created_at',
	            	'multichoice_questions.updated_at'
	            )
	            ->orderBy('multichoice_questions.id', 'desc')
	            ->get();

	        // $essay_dumps = Essay_dump::
	    	return response()->json(['multichoice_questions'=>$multichoice_questions, 'message'=>'multichoice question fetched Successfully'], 200);
		}

	/*View all Data*/ 


	
/*view single data*/ 

    public function view($course_id, $year_id)
    {
        $multichoice_question = DB::table('multichoice_questions')
            ->leftJoin('courses', 'courses.id', '=', 'multichoice_questions.course_id')
	            ->leftJoin('mcq_exam_years', 'mcq_exam_years.id', '=', 'multichoice_questions.year_id')

            ->select(
	            	'multichoice_questions.id',
	            	'course',
	            	'exam_year',
	            	'content',
	            	
	            	'multichoice_questions.created_at',
	            	'multichoice_questions.updated_at'
	         )
            ->orderBy('multichoice_questions.id', 'desc')
            // ->where('.id', $id)
            ->where('multichoice_questions.year_id', '=', $year_id)
            ->where('multichoice_questions.course_id', '=', $course_id)
            ->get();
           
            return response()->json(['multichoice_question'=>$multichoice_question, 'message'=>'multichoice question fetched Successfully'], 200);
    }

/*view single data*/ 




	public function delete($id)
    {
    	$multichoice_question = multichoice_question::find($id);
        if ($multichoice_question == NULL) {
            return response()->json([
            'message'=> 'An error occurred!'
        ], 500);
        }
            $multichoice_question->delete();

        return response()->json([
            'message'=> 'multichoice question Deleted Successfully!'
        ], 200); 
    }
	

}

