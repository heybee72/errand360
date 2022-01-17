<?php

namespace App\Http\Controllers;

use App\Models\Rider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
// use DB;
use Illuminate\Support\Facades\DB;
use App\Models\Store;
use Illuminate\Support\Facades\Http;

class RiderController extends Controller
{
    public function __construct(){
    	$this->middleware('rider-api:api', ['except' => ['login','register'] ]);
    }


// NOTE:: this works


/*-----Start of login API------*/ 
public function login(Request $request){

    try{
        $validator = Validator::make($request->all(),[
            'email'    => 'required|email',
            'password' => 'required|string|min:6'
        ]);
        

        if ($validator->fails()) {

            return response()->json($validator->errors(), 400);
        }

        if (! $token = auth('rider-api')->attempt($validator->validated())) {
                return response()->json(['error' => 'invalid login credentials'], 400);
            }

        $token_validity = 8760 * 60;

        auth('rider-api')->factory()->setTTL($token_validity);

        if (!$token = auth('rider-api')->attempt($validator->validated())) {

            return response()->json([
                'status'  => false,
                'message' => 'Invalid Login Details'
            ], 401);
        }

        $user = DB::table('riders')
        ->select('*')
        ->where('riders.email', $request->get('email'))
        ->get(); 
        $manage = (object)$token;
        $obj_merged = (object) array_merge((array) $manage, (array) $user[0]);
        // return gettype($manage);

        return response()->json([
            'status' => true,
            'message' => 'logged in Successfully!',
            'token' => $token,
            'token_validity'  => auth('rider-api')->factory()->getTTL() * 8760
        ]);
    //    return $this->respondWithToken($token, "logged in Successfully!");	
             
    }catch(\Exception $e){

        return response()->json([
            'message' => 'An Error Occured', 'error' => $e->getMessage()]);
    }

}
/*-----./End of login API------*/ 


/*-----Start of registration API------*/ 
public function register(Request $request){
    $validator = Validator::make($request->all(), [
        'name'        =>'required|string|between:2,100',
        'email'       =>'required|email|unique:customers',
        'password'    => 'required|min:6'
    ]);

    if ($validator->fails()) {
        return response()->json(["message"=> $validator->errors()], 422);
    }

    $user               = new Rider();
    $user->name         = $request->get('name');
    $user->email        = $request->get('email');
    $user->phone        = $request->get('phone');
    $user->location        = $request->get('location');
    $user->status        = true;
    $plainPassword      = $request->get('password');
    $user->password     = Hash::make($plainPassword);
    $user->api_token    = Str::random(60);
    $user->save();

    return response()->json(['rider'=>$user, 'message'=>'Rider Created Successfully'], 200);
}
/*-----./End of registration API------*/ 


/*-------Profile---------*/ 
    public function profile(){
        
    	return response()->json([
            'user'=>auth('rider-api')->user(), 
            'message'=> 'Rider found Successfully!'
        ], 200); 
    }
/*--------./Profile--------*/ 






/*--------change password api--------*/ 
public function changePassword(Request $request)
{
    $userid = auth('rider-api')->setToken($request->bearerToken())->user()->id;
    $pp = auth('rider-api')->setToken($request->bearerToken())->user()->password;

    

    // return $userid;
    $input = $request->all();
    $rules = array(
        'old_password' => 'required',
        'new_password' => 'required|min:6',
        'confirm_password' => 'required|same:new_password',
    );
    $validator = Validator::make($input, $rules);
    if ($validator->fails()) {
        $arr = array("status" => 401, "message" => $validator->errors()->first(), "data" => array());
    } else {

        try {
            if ((Hash::check(request('old_password'), $pp)) == false) {
                $arr = array("status" => 400, "message" => "Check your old password.", "data" => array());
            } else if ((Hash::check(request('new_password'), $pp)) == true) {
                $arr = array("status" => 401, "message" => "Please enter a password which is not similar then current password.", "data" => array());
            } else {
                Rider::where('id', $userid)->update(['password' => Hash::make($input['new_password'])]);
                $arr = array("status" => 200, "message" => "Password updated successfully.", "data" => array());
            }
        } catch (\Exception $ex) {
            if (isset($ex->errorInfo[2])) {
                $msg = $ex->errorInfo[2];
            } else {
                $msg = $ex->getMessage();
            }
            $arr = array("status" => 400, "message" => $msg);
        }
    }
    return \Response::json($arr);
}
/*--------change password api--------*/ 




/*--------Logout--------*/ 

    public function logout(){
    	
    	auth('rider-api')->logout();

    	return response()->json([
            'message'=>'Logged out Successfully'
        ],200);
    }
/*-------./Logout---------*/ 







/*-----Refresh API------*/ 
public function refresh(){

    return $this->respondWithToken(
        auth('rider-api')->refresh(), 
        "Token refreshed Successfully"
    );
}
/*-----./Refresh API------*/ 

    protected function respondWithToken($token, $message){
    	return response()->json([
    		'token'           => $token,
    		'token_type'      =>'bearer',
    		'token_validity'  => auth('rider-api')->factory()->getTTL() * 8760,
            'message'         =>$message
    	]);
    }




    
}
