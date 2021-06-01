<?php

namespace App\Http\Controllers;

use App\Models\Cac_form;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use DB;

class CacFormController extends Controller
{
         /*View All data*/ 
	public function index()
	{
		 $cac_forms = DB::table('cac_forms')
           

            ->select('title','content','created_at','updated_at')
            ->orderBy('cac_forms.id', 'desc')
            ->get();

        // $essay_dumps = Essay_dump::
    	return response()->json(['cac_forms'=>$cac_forms, 'message'=>'cac_form fetched Successfully'], 200);
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

             $uploadFolder = 'cac_forms';

              if ($image = $request->file('content')) {

                 $image_uploaded_path = $image->store($uploadFolder, 'public');

                 $cac_form             = new cac_form();
                 $cac_form->title     = $request->get('title');
                 $cac_form->content     = env('APP_URL', 'https://api.mulloy.co').'/mulloy/public'.Storage::url($image_uploaded_path);

                    $cac_form->save();

                    return response()->json(['cac_form'=>$cac_form, 'message'=>'CAC Form Created Successfully'], 200);
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
            
            $cac_form  = cac_form::find($id);

             $uploadFolder = 'cac_forms';

              if ($image = $request->file('content')) {

                 $cac_form->title     = $request->get('title');
                 $cac_form->content     = env('APP_URL', 'https://api.mulloy.co').'/mulloy/public'.Storage::url($image_uploaded_path);

                if ($cac_form->save()) {
                    return response()->json([
                        'cac_form'=>$cac_form, 
                        'message'=>'cac_form updated Successfully'
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
        $cac_form = DB::table('cac_forms')
            ->select('title','content','created_at','updated_at')
            
            ->orderBy('cac_forms.id', 'desc')
            ->where('cac_forms.id', $id)
            ->get();
           
            return response()->json(['cac_form'=>$cac_form, 'message'=>'cac_form fetched Successfully'], 200);
    }

/*view single data*/ 



/*DELETE DATA*/ 
    public function delete($id)
    {
    	$cac_form = cac_form::find($id);
        if ($cac_form == NULL) {
            return response()->json([
            'message'=> 'An error occurred!'
        ], 500);
        }
            $cac_form->delete();

        return response()->json([
            'message'=> 'cac_form Deleted Successfully!'
        ], 200); 
    }
/*DELETE DATA*/ 
}
