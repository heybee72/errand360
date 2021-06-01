<?php

namespace App\Http\Controllers;

use App\Models\Subscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DB;

class SubscriberController extends Controller
{
    public function add(Request $request){

    	$validator = Validator::make($request->all(), [
            'email'   =>'required|email|unique:subscribers',
    	]);

    	if ($validator->fails()) {
    		return response()->json(["message"=> $validator->errors()], 422);
    	}
    	try {
    		
        	$subscriber             = new subscriber();
            $subscriber->email     = $request->get('email');
        	$subscriber->save();

    		return response()->json(['subscriber'=>$subscriber, 'message'=>'Congratulations! subscriber Created Successfully', 'status' => true ], 201);

    	} catch (Exception $e) {

    		return response()->json(['message'=>'An error occurred', 'error'=>$e->message()], 422);
    		
    	}

    }
}
