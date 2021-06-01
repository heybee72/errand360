<?php

namespace App\Http\Controllers;

use App\Models\Dress_code;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use DB;

class DressCodeController extends Controller
{
    /*View All data*/ 
	public function index()
	{
		 $dress_codes = DB::table('dress_codes')
           

            ->select('title','note','created_at','updated_at')
            ->orderBy('dress_codes.id', 'desc')
            ->get();

        // $essay_dumps = Essay_dump::
    	return response()->json(['dress_codes'=>$dress_codes, 'message'=>'Dress code Notes fetched Successfully'], 200);
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
            
            $dress_code  = new dress_code();
            $dress_code->title     = $request->get('title');
            $dress_code->note     = $request->get('note');
            $dress_code->save();

            return response()->json(['dress_code'=>$dress_code, 'message'=>'Dress code note Created Successfully'], 200);

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
            
            $dress_code  = dress_code::find($id);
            $dress_code->title     = $request->get('title');
            $dress_code->note     = $request->get('note');
            
            if ($dress_code->save()) {
                return response()->json([
                    'dress_code'=>$dress_code, 
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
        $dress_code = DB::table('dress_codes')
            ->select('title','note','created_at','updated_at')
            
            ->orderBy('dress_codes.id', 'desc')
            ->where('dress_codes.id', $id)
            ->get();
           
            return response()->json(['dress_code'=>$dress_code, 'message'=>'dress code note fetched Successfully'], 200);
    }

/*view single data*/ 



/*DELETE DATA*/ 
    public function delete($id)
    {
    	$dress_code = dress_code::find($id);
        if ($dress_code == NULL) {
            return response()->json([
            'message'=> 'An error occurred!'
        ], 500);
        }
            $dress_code->delete();

        return response()->json([
            'message'=> 'dress code note Deleted Successfully!'
        ], 200); 
    }
/*DELETE DATA*/ 
}
