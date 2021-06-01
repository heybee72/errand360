<?php

namespace App\Http\Controllers;

use App\Models\Essay_study_question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use DB;

class EssayStudyQuestionController extends Controller
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
            
            $essay_study_question     = new essay_study_question();
            $essay_study_question->year_id     = $request->get('year_id');
            $essay_study_question->course_id     = $request->get('course_id');
            $essay_study_question->content     = $request->get('content');
            $essay_study_question->save();

            return response()->json(['essay_study_question'=>$essay_study_question, 'message'=>'Essay study question Created Successfully'], 201);

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
            
            $essay_study_question  = essay_study_question::find($id);
            $essay_study_question->year_id     = $request->get('year_id');
            $essay_study_question->course_id     = $request->get('course_id');
            $essay_study_question->content     = $request->get('content');
            
            if ($essay_study_question->save()) {
                return response()->json([
                    'essay_study_question'=>$essay_study_question, 
                    'message'=>'Essay study question updated Successfully'
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
			 $essay_study_questions = DB::table('essay_study_questions')

	            ->leftJoin('courses', 'courses.id', '=', 'essay_study_questions.course_id')
	            ->leftJoin('mcq_exam_years', 'mcq_exam_years.id', '=', 'essay_study_questions.year_id')

	            ->select(
	            	'essay_study_questions.id',
	            	'course',
	            	'exam_year',
	            	'content',
	            	
	            	'essay_study_questions.created_at',
	            	'essay_study_questions.updated_at'
	            )
	            ->orderBy('essay_study_questions.id', 'desc')
	            ->get();

	        // $essay_dumps = Essay_dump::
	    	return response()->json(['essay_study_questions'=>$essay_study_questions, 'message'=>'Essay study question fetched Successfully'], 200);
		}

	/*View all Data*/ 


	
/*view single data*/ 

    public function view($course_id, $year_id)
    {
        $essay_study_question = DB::table('essay_study_questions')
            ->leftJoin('courses', 'courses.id', '=', 'essay_study_questions.course_id')
	            ->leftJoin('mcq_exam_years', 'mcq_exam_years.id', '=', 'essay_study_questions.year_id')

            ->select(
	            	'essay_study_questions.id',
	            	'course',
	            	'exam_year',
	            	'content',
	            	'essay_study_questions.created_at',
	            	'essay_study_questions.updated_at'
	         )
            ->orderBy('essay_study_questions.id', 'desc')
            ->where('essay_study_questions.year_id', '=', $year_id)
            ->where('essay_study_questions.course_id', '=', $course_id)
            ->get();
           
            return response()->json(['essay_study_question'=>$essay_study_question, 'message'=>'Essay study question fetched Successfully'], 200);
    }

/*view single data*/ 




	public function delete($id)
    {
    	$essay_study_question = essay_study_question::find($id);
        if ($essay_study_question == NULL) {
            return response()->json([
            'message'=> 'An error occurred!'
        ], 500);
        }
            $essay_study_question->delete();

        return response()->json([
            'message'=> 'Essay study question Deleted Successfully!'
        ], 200); 
    }
	




}
