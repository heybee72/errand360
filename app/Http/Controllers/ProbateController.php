<?php

namespace App\Http\Controllers;

use App\Models\Probate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use DB;

class ProbateController extends Controller
{
     /*View All data*/ 
	public function index()
	{
		 $probates = DB::table('probates')
           

            ->select('title','content','created_at','updated_at')
            ->orderBy('probates.id', 'desc')
            ->get();

        // $essay_dumps = Essay_dump::
    	return response()->json(['probates'=>$probates, 'message'=>'probate fetched Successfully'], 200);
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

                 $uploadFolder = 'probates';

                  if ($image = $request->file('content')) {

                     $image_uploaded_path = $image->store($uploadFolder, 'public');

                     $probate             = new probate();
                     $probate->title     = $request->get('title');
                     $probate->content     = env('APP_URL', 'https://api.mulloy.co').'/mulloy/public'.Storage::url($image_uploaded_path);

                        $probate->save();

                        return response()->json(['probate'=>$probate, 'message'=>'probate Created Successfully'], 200);
                  }
                  else{
                        return response()->json(['message'=>'An error occurred!'], 500);
                    }

            }catch (Exception $e) {

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
            
            $probate  = probate::find($id);

             $uploadFolder = 'probates';

              if ($image = $request->file('content')) {

                 $probate->title     = $request->get('title');
                 $probate->content     = env('APP_URL', 'https://api.mulloy.co').'/mulloy/public'.Storage::url($image_uploaded_path);

                if ($probate->save()) {
                    return response()->json([
                        'probate'=>$probate, 
                        'message'=>'probate updated Successfully'
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
        $probate = DB::table('probates')
            ->select('title','content','created_at','updated_at')
            
            ->orderBy('probates.id', 'desc')
            ->where('probates.id', $id)
            ->get();
           
            return response()->json(['probate'=>$probate, 'message'=>'probate fetched Successfully'], 200);
    }

/*view single data*/ 



/*DELETE DATA*/ 
    public function delete($id)
    {
    	$probate = probate::find($id);
        if ($probate == NULL) {
            return response()->json([
            'message'=> 'An error occurred!'
        ], 500);
        }
            $probate->delete();

        return response()->json([
            'message'=> 'probate Deleted Successfully!'
        ], 200); 
    }
/*DELETE DATA*/ 



}
