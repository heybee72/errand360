<?php

namespace App\Http\Controllers;

use App\Models\Disciplinary_action;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use DB;

class DisciplinaryActionController extends Controller
{
    /*View All data*/ 
	public function index()
	{
		 $disciplinary_actions = DB::table('disciplinary_actions')
           

            ->select('title','note','created_at','updated_at')
            ->orderBy('disciplinary_actions.id', 'desc')
            ->get();

        // $essay_dumps = Essay_dump::
    	return response()->json(['disciplinary_actions'=>$disciplinary_actions, 'message'=>'Dress code Notes fetched Successfully'], 200);
	}

/*View all Data*/ 


/*Add data*/ 

	public function add(Request $request){

        $validator = Validator::make($request->all(), [
            'title' =>'required|string',
            'note' =>'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(["message"=> $validator->errors()], 422);
        }
        try {
            
            $disciplinary_actions  = new disciplinary_action();
            $disciplinary_actions->title     = $request->get('title');
            $disciplinary_actions->note     = $request->get('note');
            $disciplinary_actions->save();

            return response()->json(['disciplinary_actions'=>$disciplinary_actions, 'message'=>'Dress code note Created Successfully'], 200);

        } catch (Exception $e) {

            return response()->json(['message'=>'An error occurred', 'error'=>$e->message()], 422);
            
        }

    }

/*Add data*/ 

/*Update Data*/ 
        
    public function update(Request $request, $id){

        $validator = Validator::make($request->all(), [
            'title' =>'required|string',
            'note' =>'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(["message"=> $validator->errors()], 422);
        }
        try {
            
            $disciplinary_actions  = disciplinary_action::find($id);
            $disciplinary_actions->title     = $request->get('title');
            $disciplinary_actions->note     = $request->get('note');
            
            if ($disciplinary_actions->save()) {
                return response()->json([
                    'disciplinary_actions'=>$disciplinary_actions, 
                    'message'=>'Dress code notes updated Successfully'
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
        $disciplinary_actions = DB::table('disciplinary_actions')
            ->select('title','note','created_at','updated_at')
            
            ->orderBy('disciplinary_actions.id', 'desc')
            ->where('disciplinary_actions.id', $id)
            ->get();
           
            return response()->json(['disciplinary_actions'=>$disciplinary_actions, 'message'=>'dress code note fetched Successfully'], 200);
    }

/*view single data*/ 



/*DELETE DATA*/ 
    public function delete($id)
    {
    	$disciplinary_actions = disciplinary_action::find($id);
        if ($disciplinary_actions == NULL) {
            return response()->json([
            'message'=> 'An error occurred!'
        ], 500);
        }
            $disciplinary_actions->delete();

        return response()->json([
            'message'=> 'dress code note Deleted Successfully!'
        ], 200); 
    }
/*DELETE DATA*/ 
}
