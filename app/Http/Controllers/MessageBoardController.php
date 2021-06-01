<?php

namespace App\Http\Controllers;

use App\Services\JsonResponse;
use App\Models\MessageBoard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use DB;
//custom json response service

class MessageBoardController extends Controller
{
    public function addUserChat(Request $request){

    	$user = auth()->setToken($request->bearerToken())->user();

        if ($user == NULL)
            return response()->json( ['message'=>'User not found!'], 400);

        $validator = Validator::make($request->all(), [
            'message' => 'required',
            'type' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(["message"=> $validator->errors()], 422);
        }
        try {
        	$message_type = $request->get('type');
        	$messageBoard = new MessageBoard();


        	if ( $message_type == 'text' and !$request->hasFile('message')) {

	            $messageBoard->sender_id     = $user->id;
	            $messageBoard->receiver_id   = 0;
	            $messageBoard->message   = $request->get('message');
	            $messageBoard->type   = $message_type;

	            $messageBoard->save();

        	}else if($message_type == 'file' and $request->hasFile('message')){

                $uploadFolder = 'documents';

        		if ( $image = $request->file('message')) {


                    $image_uploaded_path = $image->store($uploadFolder, 'public');

                    $messageBoard->sender_id     = $user->id;
	            	$messageBoard->receiver_id   = 0;
		            $messageBoard->message       = Storage::url($image_uploaded_path);
		            $messageBoard->type          = $message_type;

		            $messageBoard->save();

        		}else{
        			return JsonResponse::show('An error occurred!', 500, false, );
                }

        	}else{
        		return JsonResponse::show('Unable to send message', 400, false, );
        	}

        	return JsonResponse::show('Message sent successfully', 201, true, $messageBoard);
            

        } catch (Exception $e) {

            return response()->json(['message'=>'An error occurred', 'error'=>$e->message()], 422);
            
        }

    }

    public function addAdminChat(Request $request){

    	// $admin = auth('admin-api')->setToken($request->bearerToken())->user();

     //    if ($admin == NULL)
     //        return response()->json( ['message'=>'User not found!'], 400);

        $validator = Validator::make($request->all(), [
            'message' => 'required',
            'receiver_id' => 'required',
            'type' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(["message"=> $validator->errors()], 422);
        }
        try {
        	$message_type = $request->get('type');
        	$messageBoard = new MessageBoard();

        	if ( $message_type == 'text' and !$request->hasFile('message')) {

	            $messageBoard->sender_id     = 0;
	            $messageBoard->receiver_id   = $request->get('receiver_id');
	            $messageBoard->message   = $request->get('message');
	            $messageBoard->type   = $message_type;

	            $messageBoard->save();

        	}else if($message_type == 'file' and $request->hasFile('message')){

                $uploadFolder = 'documents';

        		if ( $image = $request->file('message')) {


                    $image_uploaded_path = $image->store($uploadFolder, 'public');

                    $messageBoard->sender_id     = 0;
	            	$messageBoard->receiver_id   = $request->get('receiver_id');
		            $messageBoard->message       = Storage::url($image_uploaded_path);
		            $messageBoard->type          = $message_type;

		            $messageBoard->save();

        		}else{
        			return JsonResponse::show('An error occurred', 500, false, );
                }

        	}else{
        		return JsonResponse::show('Unable to send message', 400, false, );
        	}

        	return JsonResponse::show('Message sent successfully', 201, true, $messageBoard);
        	          

        } catch (Exception $e) {

            return response()->json(['message'=>'An error occurred', 'error'=>$e->message()], 422);
            
        }

    }

    public function userViewMessages(Request $request)
    {
        $user = auth()->setToken($request->bearerToken())->user();

        if ($user == NULL)
            return response()->json( ['message'=>'User not found!'], 400);

    	$data = DB::table('message_boards')
                ->whereRaw('sender_id = ? OR receiver_id = ?', [$user->id, $user->id])
                ->paginate(200);
        if (count($data) < 1) 
            return JsonResponse::show('No messages yet', 200, true);
        return JsonResponse::show('successfully fetched messages', 200, true, $data);
    }

    public function adminViewMessages(Request $request, $id)
    {
        // $user = auth()->setToken($request->bearerToken())->user();

        // if ($user == NULL)
        //     return response()->json( ['message'=>'User not found!'], 400);

        $data = DB::select('SELECT u.name, m.id, m.message, m.sender_id, m.receiver_id FROM message_boards m LEFT JOIN users u ON (m.sender_id = u.id OR m.receiver_id = u.id) WHERE (m.sender_id = ? AND m.receiver_id = "0") OR (m.sender_id = "0" AND m.receiver_id = ?)', [$id, $id]);
        if (count($data) < 1) 
            return JsonResponse::show('No messages yet', 200, true);
        return JsonResponse::show('successfully fetched messages', 200, true, $data);
    }


    public function adminListMessages(Request $request)
    {
        $messages = DB::select('
        SELECT t1.*
        FROM message_boards AS t1
        INNER JOIN
        (
            SELECT
                LEAST(sender_id, receiver_id) AS sender_id,
                GREATEST(sender_id, receiver_id) AS receiver_id,
                MAX(id) AS max_id
            FROM message_boards
            GROUP BY
                LEAST(sender_id, receiver_id),
                GREATEST(sender_id, receiver_id)
        ) AS t2
            ON LEAST(t1.sender_id, t1.receiver_id) = t2.sender_id AND
               GREATEST(t1.sender_id, t1.receiver_id) = t2.receiver_id AND
               t1.id = t2.max_id
            WHERE t1.sender_id = ? OR t1.receiver_id = ?
        ', [0, 0]);
        return JsonResponse::show('successfully fetched messages', 200, true, $messages);
    }
}
