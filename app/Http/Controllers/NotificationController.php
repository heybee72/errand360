<?php

namespace App\Http\Controllers;


use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use DB;
// use JWTAuth;

class NotificationController extends Controller
{

/*View All data*/ 
	public function index(Request $request)
	{
         $user = auth()->setToken($request->bearerToken())->user();

        if ($user == NULL) {

            return response()->json(['message'=>'User not found!'], 400);
        }

        $notification = DB::select('
            SELECT * From notifications where user_id IS NULL or user_id = ? ORDER BY id DESC ', [$user->id]);

        if ($notification) {

       $notification = DB::select('
            SELECT * From notifications where user_id IS NULL or user_id = ? ORDER BY status ASC LIMIT 6', [$user->id]);
        
    	    return response()->json(['notification'=>$notification, 'message'=>'Notification fetched Successfully'], 200);
        }
	}

/*View all Data*/ 


public function update_notification_status(Request $request){

        $user = auth()->setToken($request->bearerToken())->user();

        if ($user == NULL) {

            return response()->json(['message'=>'User not found!'], 400);
        }else{

            if ($user->notification_status == 1) {
                
                $notification = DB::UPDATE('UPDATE users SET notification_status = ? WHERE id = ? ', [0, $user->id]);
            
                return response()->json(['notification'=>$notification, 'message'=>'Notification Turned Off!'], 200);
            }else{

                $notification = DB::UPDATE('UPDATE users SET notification_status = ? WHERE id = ? ', [1, $user->id]);
            
                return response()->json(['notification'=>$notification, 'message'=>'Notification Turned On!'], 200);
            }

        }
    }


/*read notifications*/ 
    public function read(Request $request, $id)
    {
         $user = auth()->setToken($request->bearerToken())->user();

        if ($user == NULL) {

            return response()->json(['message'=>'User not found!'], 400);
        }

       $notification = DB::select(
        'SELECT * From notifications 
            WHERE user_id IS NULL or user_id = ?  
            ORDER BY status DESC', [$user->id]
        );
       if ($notification) {
           $notification = DB::select(
            'UPDATE notifications 
                SET status = ? WHERE id = ? ', [1, $id]
            );
       }
        
        return response()->json(['notification'=>$notification, 'message'=>'Notification fetched Successfully'], 200);
    }

/*read notifications*/ 


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
            
            $notification     = new Notification();
            $notification->content     = $request->get('content');
            $notification->title     = $request->get('title');
            $notification->status     = 1;
            $notification->slug  = Str::slug($notification->title);
            $notification->save();

            return response()->json(['notification'=>$notification, 'message'=>'Notification Created Successfully'], 201);

        } catch (Exception $e) {

            return response()->json(['message'=>'An error occurred', 'error'=>$e->message()], 422);
            
        }

    }

/*Add data*/ 





// /*DELETE DATA*/ 
//     public function delete($id)
//     {
//     	$mcq_exam_year = Mcq_exam_year::find($id);
//         if ($mcq_exam_year == NULL) {
//             return response()->json([
//             'message'=> 'An error occurred!'
//         ], 500);
//         }
//             $mcq_exam_year->delete();

//         return response()->json([
//             'message'=> 'Notification Deleted Successfully!'
//         ], 200); 
//     }
// /*DELETE DATA*/ 
    
}
