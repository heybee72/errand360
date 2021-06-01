<?php

namespace App\Http\Controllers;


use App\Models\Essay_dump;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use DB;
// use JWTAuth;

class Essay_dumpController extends Controller
{

	public function index()
	{
		$essay_dumps = DB::table('essay_dumps')
            ->Join('users', 'users.id', '=', 'essay_dumps.user_id')
            ->leftJoin('mcq_exam_years', 'mcq_exam_years.id', '=', 'essay_dumps.exam_year_id')
            ->select('essay_dumps.id','name','essay_dumps.email','document','description','mcq_exam_years.exam_year','essay_dumps.created_at','essay_dumps.updated_at')
            ->orderBy('essay_dumps.id', 'desc')
            ->get();

        // $essay_dumps = Essay_dump::
    	return response()->json(['essay_dumps'=>$essay_dumps, 'message'=>'Essay dump fetched Successfully'], 200);
	}

	public function add(Request $request){
        // $user = JWTAuth::toUser();
        $user = auth()->setToken($request->bearerToken())->user();

        if ($user == NULL) {

            return response()->json(['message'=>'User not found!'], 400);
        }
        elseif ($user->user_plan != 'premium') {

            return response()->json(['message'=>'Only premium subscribers can upload essay'], 500);

        }else{

        	$validator = Validator::make($request->all(), [
                'email' =>'required|string|email',
                'exam_year_id' =>'required',
                'description' =>'required',
                'document' =>'required|mimes:doc,docx,pdf,txt,ppt,pptx,png,jpg|max:2048',
        	]);

        	if ($validator->fails()) {
        		return response()->json(["message"=> $validator->errors()], 422);
        	}
        	try {
                $uploadFolder = 'documents';
                 
                if ($image = $request->file('document')) {

                    $image_uploaded_path = $image->store($uploadFolder, 'public');

                	$essay_dump             = new Essay_dump();
                    $essay_dump->email     = $request->get('email');
                    $essay_dump->exam_year_id     = $request->get('exam_year_id');
                    $essay_dump->user_id     = $user->id;
                    $essay_dump->description     = $request->get('description');
                    $essay_dump->document     = env('APP_URL', 'http://127.0.0.1:8000').Storage::url($image_uploaded_path);

                    // return '<img src ="'.$essay_dump->document.'" >';
                	$essay_dump->save();


            		return response()->json(['essay_dump'=>$essay_dump, 'message'=>'Essay dump Created Successfully'], 201);
                }else{
                    return response()->json(['message'=>'An error occurred!'], 500);
                }

        	} catch (Exception $e) {

        		return response()->json(['message'=>'An error occurred', 'error'=>$e->message()], 422);
        		
        	}
        }
    }



    public function view($id)
    {
        $essay_dump = DB::table('essay_dumps')
            ->Join('users', 'users.id', '=', 'essay_dumps.user_id')
            ->leftJoin('mcq_exam_years', 'mcq_exam_years.id', '=', 'essay_dumps.exam_year_id')
            ->select('essay_dumps.id','name','essay_dumps.email','document','description','mcq_exam_years.exam_year','essay_dumps.created_at','essay_dumps.updated_at')
            ->orderBy('essay_dumps.id', 'desc')
            ->where('essay_dumps.id', $id)
            ->get();
        return response()->json(['essay_dump'=>$essay_dump, 'message'=>'essay dump fetched Successfully'], 200);
    }


    public function delete($id)
    {
    	$essay_dump = Essay_dump::find($id);
    
        if ($essay_dump == NULL) {
            return response()->json([
            'message'=> 'An error occurred!'
        ], 500);
        }else{
            $image_path = public_path().'/'.$essay_dump->document;
            if (unlink($image_path)) {
                $essay_dump->delete();
            }else{
                $essay_dump->delete();

            }

            return response()->json([
                'message'=> 'essay dump Deleted Successfully!'
            ], 200); 
        }
    }
    
}
