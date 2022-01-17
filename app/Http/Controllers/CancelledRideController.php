<?php

namespace App\Http\Controllers;

use App\Models\CancelledRide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;


class CancelledRideController extends Controller
{
    public function index(Request $request){
        try {
            
            $data = CancelledRide::orderBy('id', 'Desc')->get();
            
                return response()->json([
                    'data'=>$data, 
                    'message'=>'Cancelled Fetched Successfully'
                ], 200);

        } catch (\Exception $e) {

            return response()->json(['message'=>'An error occurred', 'error'=>$e->getMessage()], 422);
            
        }
    }

     public function delete(Request $request, $id)
    {
        try {
            if($id == null){
                return response()->json(['message'=>'Please provide an id'], 422);
            }

            $id = CancelledRide::find($id);
            $id ->delete();

            return response()->json([
                'message'=> 'data Deleted Successfully!'
            ], 200); 

        } catch (\Exception $e) {

            return response()->json(['message'=>'An error occurred', 'error'=>$e->getMessage()], 422);
            
        }
    }

    
}
