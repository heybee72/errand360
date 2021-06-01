<?php

namespace App\Http\Controllers;

use App\Models\Statute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use DB;

class StatuteController extends Controller
{
    /*View All data*/ 
	public function index()
	{
		 $statutes = DB::table('statutes')
           

            ->select('title','content','created_at','updated_at')
            ->orderBy('statutes.id', 'desc')
            ->get();

        // $essay_dumps = Essay_dump::
    	return response()->json(['statutes'=>$statutes, 'message'=>'Statute fetched Successfully'], 200);
	}

/*View all Data*/ 


/*Add data*/ 

	public function add(Request $request){

        $validator = Validator::make($request->all(), [
            'title' =>'required|string',
             'content' =>'required|mimes:doc,docx,pdf,txt,ppt,pptx,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(["message"=> $validator->errors()], 422);
        }
        try {

             $uploadFolder = 'statutes';

              if ($image = $request->file('content')) {

                 $image_uploaded_path = $image->store($uploadFolder, 'public');

                 $statute             = new statute();
                 $statute->title     = $request->get('title');
                 $statute->content     = env('APP_URL', 'https://api.mulloy.co').'/mulloy/public'.Storage::url($image_uploaded_path);

                    $statute->save();

                    return response()->json(['statute'=>$statute, 'message'=>'Statute Created Successfully'], 200);
              }
              else{
                    return response()->json(['message'=>'An error occurred!'], 500);
                }

        } catch (Exception $e) {

            return response()->json(['message'=>'An error occurred', 'error'=>$e->message()], 422);
            
        }

    }

/*Add data*/ 

/*Update Data*/ 
        
    public function update(Request $request, $id){

        $validator = Validator::make($request->all(), [
            'title' =>'required|string',
            'content' =>'required|mimes:doc,docx,pdf,txt,ppt,pptx,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(["message"=> $validator->errors()], 422);
        }
        try {
            
            $statute  = statute::find($id);

             $uploadFolder = 'statutes';

              if ($image = $request->file('content')) {

                 $statute->title     = $request->get('title');
                 $statute->content     = env('APP_URL', 'https://api.mulloy.co').'/mulloy/public'.Storage::url($image_uploaded_path);

                if ($statute->save()) {
                    return response()->json([
                        'statute'=>$statute, 
                        'message'=>'Statute updated Successfully'
                    ], 201);
                }else{

                    return response()->json([
                        'message'=>'An error occured!'
                    ], 501);

                }
                
              }

        } catch (Exception $e) {

            return response()->json(['message'=>'An error occurred', 'error'=>$e->message()], 422);
            
        }

    }


/*Update Data*/ 


/*view single data*/ 

    public function view($id)
    {
        $statute = DB::table('statutes')
            ->select('title','content','created_at','updated_at')
            
            ->orderBy('statutes.id', 'desc')
            ->where('statutes.id', $id)
            ->get();
           
            return response()->json(['statute'=>$statute, 'message'=>'Statute fetched Successfully'], 200);
    }

/*view single data*/ 



/*DELETE DATA*/ 
    public function delete($id)
    {
    	$statute = statute::find($id);
        if ($statute == NULL) {
            return response()->json([
            'message'=> 'An error occurred!'
        ], 500);
        }
            $statute->delete();

        return response()->json([
            'message'=> 'Statute Deleted Successfully!'
        ], 200); 
    }
/*DELETE DATA*/  
}
