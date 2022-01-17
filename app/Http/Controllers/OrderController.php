<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\CancelledRide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function createNewOrder(Request $request){

        $user_id = auth('customer-api')->setToken($request->bearerToken())->user();

        $validator = Validator::make($request->all(), [
            'parcel' =>'string|required',
            'starting_point' =>'string|required',
            'destination' =>'string|required',
        ]);

        if ($validator->fails()) {
            return response()->json(["message"=> $validator->errors()], 422);
        }
        

    	try {    		
        	$data             = new Order();
        	$data->user_id     = $user_id->id;
        	$data->parcel     = $request->get('parcel');
        	$data->starting_point     = $request->get('starting_point');
        	$data->destination     = $request->get('destination');
        	$data->requesting_rider     = true;
        	$data->save();

    		return response()->json(['data'=>$data, 'message'=>'data Created Successfully'], 200);

    	} catch (\Exception $e) {
    		return response()->json(['message'=>'An error occurred', 'error'=>$e->getMessage()], 422);    		
    	}
    }


    public function addDriverToOrder(Request $request){

        $rider = auth('rider-api')->setToken($request->bearerToken())->user();

        try {
           
            $add = DB::SELECT('SELECT * FROM orders  where requesting_rider = ?  ORDER BY id DESC', ['1']);

            $get_data = count($add);

            if($get_data > 0){

                 DB::update('update orders set rider_id = ?, requesting_rider = ?, ride_accepted = ? where id = ? ', [$rider->id,false, true, $add[0]->id]);

               
              
            }else{
                return "No Order Found";
            }

            $order = DB::table('orders')           
            ->leftJoin('customers', 'customers.id', '=', 'orders.user_id')
            ->leftJoin('riders', 'riders.id', '=', 'orders.rider_id')
            ->select('orders.id',
            'customers.name as customer',
            'riders.name as rider',
            'parcel',
            'ride_accepted',
            'starting_point',
            'destination',
            'orders.created_at',
            'orders.updated_at'
            )
            ->orderBy('orders.id', 'desc')->limit(1)
            ->get();


                return response()->json([
                    'order'=>$order, 
                    'message'=>'Driver Accepted Ride Successfully'
                ], 200);

        } catch (\Exception $e) {

            return response()->json(['message'=>'An error occurred', 'error'=>$e->getMessage()], 422);
            
        }

    }

    public function driverRejectRide(Request $request){

        $rider = auth('rider-api')->setToken($request->bearerToken())->user();

        try {
           
            $add = DB::SELECT('SELECT * FROM orders  where requesting_rider = ?  ORDER BY id DESC', ['1']);

            $get_data = count($add);

            if($get_data > 0){

                // update rejected order table

                $ride  = new CancelledRide();
            $ride->order_id     = $add[0]->id;
            $ride->rider_id     = $rider->id;
            $ride->reason     = $request->get('reason');
            $ride->save();

            }else{
                return "No Order Found";
            }

            $order = DB::table('cancelled_rides')           
            ->leftJoin('orders', 'orders.id', '=', 'cancelled_rides.order_id')
            ->leftJoin('riders', 'riders.id', '=', 'cancelled_rides.rider_id')
            ->select('cancelled_rides.id',
            
            'riders.name as rider',
            'parcel',
            'starting_point',
            'destination',
            'reason',
            'cancelled_rides.created_at',
            'cancelled_rides.updated_at'
            )
            ->orderBy('cancelled_rides.id', 'desc')->limit(1)
            ->get();


                return response()->json([
                    'order'=>$order, 
                    'message'=>'Driver Rejected this Ride'
                ], 200);

        } catch (\Exception $e) {

            return response()->json(['message'=>'An error occurred', 'error'=>$e->getMessage()], 422);
            
        }

    }


    public function riderDeliverOrder(Request $request){

        $rider = auth('rider-api')->setToken($request->bearerToken())->user();

        try {
           
            $add = DB::SELECT('SELECT * FROM orders  where ride_accepted = ? AND rider_id = ? ORDER BY id DESC LIMIT 1', ['1', $rider->id]);

            $get_data = count($add);

            if($get_data > 0){

                 DB::update('update orders set rider_id = ?, delivered = ?, ride_accepted = ? where id = ? ', [$rider->id,true, false, $add[0]->id]);
              
            }else{
                return "No Order Found";
            }

            $order = DB::table('orders')           
            ->leftJoin('customers', 'customers.id', '=', 'orders.user_id')
            ->leftJoin('riders', 'riders.id', '=', 'orders.rider_id')
            ->select('orders.id',
            'customers.name as customer',
            'riders.name as rider',
            'parcel',
            'delivered',
            'starting_point',
            'destination',
            'orders.created_at',
            'orders.updated_at'
            )
            ->orderBy('orders.id', 'desc')->limit(1)
            ->get();

                return response()->json([
                    'order'=>$order, 
                    'message'=>'Driver Accepted Ride Successfully'
                ], 200);

        } catch (\Exception $e) {

            return response()->json(['message'=>'An error occurred', 'error'=>$e->getMessage()], 422);
            
        }

    }








}
