<?php

namespace App\Http\Controllers;

use App\Models\Code_of_conduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use DB;

class CodeOfConductController extends Controller
{
    /*View All data*/ 
	public function index()
	{
		 $code_of_conducts = DB::table('code_of_conducts')
           

            ->select('title','note','created_at','updated_at')
            ->orderBy('code_of_conducts.id', 'desc')
            ->get();

        // $essay_dumps = Essay_dump::
    	return response()->json(['code_of_conducts'=>$code_of_conducts, 'message'=>'Dress code Notes fetched Successfully'], 200);
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
            
            $code_of_conducts  = new code_of_conduct();
            $code_of_conducts->title     = $request->get('title');
            $code_of_conducts->note     = $request->get('note');
            $code_of_conducts->save();

            return response()->json(['code_of_conducts'=>$code_of_conducts, 'message'=>'Dress code note Created Successfully'], 200);

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
            
            $code_of_conducts  = code_of_conduct::find($id);
            $code_of_conducts->title     = $request->get('title');
            $code_of_conducts->note     = $request->get('note');
            
            if ($code_of_conducts->save()) {
                return response()->json([
                    'code_of_conducts'=>$code_of_conducts, 
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
        $code_of_conducts = DB::table('code_of_conducts')
            ->select('title','note','created_at','updated_at')
            
            ->orderBy('code_of_conducts.id', 'desc')
            ->where('code_of_conducts.id', $id)
            ->get();
           
            return response()->json(['code_of_conduct'=>$code_of_conducts, 'message'=>'dress code note fetched Successfully'], 200);
    }

/*view single data*/ 



/*DELETE DATA*/ 
    public function delete($id)
    {
    	$code_of_conducts = code_of_conduct::find($id);
        if ($code_of_conducts == NULL) {
            return response()->json([
            'message'=> 'An error occurred!'
        ], 500);
        }
            $code_of_conducts->delete();

        return response()->json([
            'message'=> 'dress code note Deleted Successfully!'
        ], 200); 
    }
/*DELETE DATA*/ 
}
