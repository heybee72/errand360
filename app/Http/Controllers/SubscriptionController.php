<?php

namespace App\Http\Controllers;


use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DB;

class SubscriptionController extends Controller
{

	public function index()
	{
		$subscription = Subscription::all();
    	return response()->json(['subscription'=>$subscription, 'message'=>'subscription fetched Successfully'], 200);
	}


	public function add(Request $request){
    	$validator = Validator::make($request->all(), [
            'plan' =>'required',
            'amount' =>'required',
    		'description' =>'required',
    	]);

    	if ($validator->fails()) {
    		return response()->json(["message"=> $validator->errors()], 422);
    	}
    	try {
    		
        	$subscription             = new Subscription();
            $subscription->plan     = $request->get('plan');
            $subscription->amount     = $request->get('amount');
        	$subscription->description     = $request->get('description');
        	$subscription->save();

    		return response()->json(['subscription'=>$subscription, 'message'=>'subscription Created Successfully'], 201);

    	} catch (Exception $e) {

    		return response()->json(['message'=>'An error occurred', 'error'=>$e->message()], 422);
    		
    	}

    }


    public function update(Request $request, $id ){

        $validator = Validator::make($request->all(), [
            'plan' =>'required',
            'amount' =>'required',
            'description' =>'required',
        ]);

        if ($validator->fails()) {
            return response()->json(["message"=> $validator->errors()], 422);
        }
        try {
            
            $subscription             = Subscription::find($id);
            $subscription->plan     = $request->get('plan');
            $subscription->amount     = $request->get('amount');
            $subscription->description     = $request->get('description');
            
            if ($subscription->save()) {
                return response()->json([
                    'subscription'=>$subscription, 
                    'message'=>'subscription updated Successfully'
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

    public function view($id)
    {
        $subscription = Subscription::find($id);
        return response()->json(['subscription'=>$subscription, 'message'=>'subscription fetched Successfully'], 200);
    }


    /*--------------*/

    public function upgradePlan(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'user_plan' =>'required',
            'reference' =>'required',
        ]);
        if ($validator->fails()) {
            return response()->json(["message"=> $validator->errors()], 422);
        }else{

            try {
                
                $user = auth()->setToken($request->bearerToken())->user();
                // $the_user_plan = $user->user_plan;

                $reference    = $request->get('reference');

                $curl = curl_init();
              
                  curl_setopt_array($curl, array(
                    CURLOPT_URL => "https://api.paystack.co/transaction/verify/".$reference,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 200,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "GET",
                    CURLOPT_HTTPHEADER => array(
                      "Authorization: Bearer sk_test_1acf0349fa283dd5f8cb9b10a0bba86847152c00",
                      "Cache-Control: no-cache",
                    ),
                  ));
              
              $response = curl_exec($curl);
              $err = curl_error($curl);
              curl_close($curl);
              
              if ($err) {
                return "cURL Error #:" . $err;
              } else {
                
                $response = json_decode($response);

                if ($response->status == false) {
                    return response()->json([
                        'data'=>$response->message
                    ], 200);

                }elseif ($response->status == true) {

                    $user_model = User::find($user->id);
                    // return $user_model;
                    $user_model->user_plan = $request->get('user_plan');

                    if ($user_model->save()) {
                        return response()->json([
                            'data'=>$user_model, 
                            'message'=>'Subscription upgraded successfully'
                        ], 201);
                    }else{

                        return response()->json([
                            'message'=>'An error occured!'
                        ], 501);

                    }
                }else{

                    return response()->json([
                        'data'=>$response->message
                    ], 200);
                }

                
              }


        } catch (Exception $e) {
            return response()->json(['message'=>'An error occurred', 'error'=>$e->message()], 422);
        }

        }


    }



    /*--------------*/

    public function delete($id)
    {
    	$subscription = Subscription::find($id);
        if ($subscription == NULL) {
            return response()->json([
            'message'=> 'An error occurred!'
        ], 500);
        }
            $subscription->delete();

        return response()->json([
            'message'=> 'subscription Deleted Successfully!'
        ], 200); 
    }
    
}
