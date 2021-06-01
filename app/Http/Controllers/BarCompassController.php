<?php

namespace App\Http\Controllers;

use App\Models\Bar_compass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use DB;

class BarCompassController extends Controller
{
    /*View All data*/ 
	public function index()
	{
		 $bar_compasses = DB::table('bar_compasses')
           

            ->select('title','content','created_at','updated_at')
            ->orderBy('bar_compasses.id', 'desc')
            ->get();

        // $essay_dumps = Essay_dump::
    	return response()->json(['bar_compasses'=>$bar_compasses, 'message'=>'bar compass fetched Successfully'], 200);
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
            
            $bar_compass  = new bar_compass();
            $bar_compass->title     = $request->get('title');
            $bar_compass->content     = $request->get('content');
            $bar_compass->save();

            return response()->json(['bar_compass'=>$bar_compass, 'message'=>'bar_compass Created Successfully'], 200);

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
            
            $bar_compass  = bar_compass::find($id);
            $bar_compass->title     = $request->get('title');
            $bar_compass->content     = $request->get('content');
            
            if ($bar_compass->save()) {
                return response()->json([
                    'bar_compass'=>$bar_compass, 
                    'message'=>'bar compass updated Successfully'
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
        $bar_compass = DB::table('bar_compasses')
            ->select('title','content','created_at','updated_at')
            
            ->orderBy('bar_compasses.id', 'desc')
            ->where('bar_compasses.id', $id)
            ->get();
           
            return response()->json(['bar_compass'=>$bar_compass, 'message'=>'bar compass fetched Successfully'], 200);
    }

/*view single data*/ 



/*DELETE DATA*/ 
    public function delete($id)
    {
    	$bar_compass = bar_compass::find($id);
        if ($bar_compass == NULL) {
            return response()->json([
            'message'=> 'An error occurred!'
        ], 500);
        }
            $bar_compass->delete();

        return response()->json([
            'message'=> 'bar compass Deleted Successfully!'
        ], 200); 
    }
/*DELETE DATA*/ 
}
