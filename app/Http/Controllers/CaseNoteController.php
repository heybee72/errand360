<?php

namespace App\Http\Controllers;

use App\Models\Case_note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use DB;

class CaseNoteController extends Controller
{
    /*View All data*/ 
	public function index()
	{
		 $case_notes = DB::table('case_notes')
           

            ->select('title','content','created_at','updated_at')
            ->orderBy('case_notes.id', 'desc')
            ->get();

        // $essay_dumps = Essay_dump::
    	return response()->json(['case_notes'=>$case_notes, 'message'=>'case notes fetched Successfully'], 200);
	}

/*View all Data*/ 


/*Add data*/ 

	public function add(Request $request){

        $validator = Validator::make($request->all(), [
            'title' =>'required|string',
            'content' =>'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(["message"=> $validator->errors()], 422);
        }
        try {
            
            $case_note  = new case_note();
            $case_note->title     = $request->get('title');
            $case_note->content     = $request->get('content');
            $case_note->save();

            return response()->json(['case_note'=>$case_note, 'message'=>'case note Created Successfully'], 200);

        } catch (Exception $e) {

            return response()->json(['message'=>'An error occurred', 'error'=>$e->message()], 422);
            
        }

    }

/*Add data*/ 

/*Update Data*/ 
        
    public function update(Request $request, $id){

        $validator = Validator::make($request->all(), [
            'title' =>'required|string',
            'content' =>'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(["message"=> $validator->errors()], 422);
        }
        try {
            
            $case_note  = case_note::find($id);
            $case_note->title     = $request->get('title');
            $case_note->content     = $request->get('content');
            
            if ($case_note->save()) {
                return response()->json([
                    'case_note'=>$case_note, 
                    'message'=>'case note updated Successfully'
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
        $case_note = DB::table('case_notes')
            ->select('title','content','created_at','updated_at')
            
            ->orderBy('case_notes.id', 'desc')
            ->where('case_notes.id', $id)
            ->get();
           
            return response()->json(['case_note'=>$case_note, 'message'=>'case note fetched Successfully'], 200);
    }

/*view single data*/ 



/*DELETE DATA*/ 
    public function delete($id)
    {
    	$case_note = case_note::find($id);
        if ($case_note == NULL) {
            return response()->json([
            'message'=> 'An error occurred!'
        ], 500);
        }
            $case_note->delete();

        return response()->json([
            'message'=> 'case note Deleted Successfully!'
        ], 200); 
    }
/*DELETE DATA*/ 
}
