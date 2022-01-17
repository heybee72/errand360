<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Company;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\Section;

// use JWTAuth;

class AdminAuthController extends Controller
{
    public function __construct(){
    	$this->middleware('admin-api:api', ['except' => ['login','register'] ]);
    }



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

            if (! $token = auth('admin-api')->attempt($validator->validated())) {
                    return response()->json(['error' => 'invalid login credentials'], 400);
                }

            $token_validity = 8760 * 60;

            auth('admin-api')->factory()->setTTL($token_validity);

            if (!$token = auth('admin-api')->attempt($validator->validated())) {

                return response()->json([
                    'status'  => false,
                    'message' => 'Invalid Login Details'
                ], 401);
            }
    	   return $this->respondWithToken($token, "Admin logged in Successfully!");	
                 
        }catch(\Exception $e){

            return response()->json([
                'message' => 'An Error Occured']);
        }

    }
/*-----./End of login API------*/ 

/*-----Start of registration API------*/ 
public function register(Request $request){
    $validator = Validator::make($request->all(), [
        'name'        =>'required|string|between:2,100',
        'email'       =>'required|email|unique:admins',
        'password'    => 'required|min:6'
    ]);

    if ($validator->fails()) {
        return response()->json(["message"=> $validator->errors()], 422);
    }

    $admin               = new Admin;
    $admin->name         = $request->get('name');
    $admin->email        = $request->get('email');
    $plainPassword      = $request->get('password');
    $admin->password     = Hash::make($plainPassword);
    $admin->save();

    return response()->json(['admin'=>$admin, 'message'=>'Admin Created Successfully'], 200);
}
/*-----./End of registration API------*/ 

/*--------change password api--------*/ 
public function changePassword(Request $request)
{
    $userid = auth('admin-api')->setToken($request->bearerToken())->user()->id;
    $pp = auth('admin-api')->setToken($request->bearerToken())->user()->password;

    

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
                Admin::where('id', $userid)->update(['password' => Hash::make($input['new_password'])]);
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








/*-------Profile---------*/ 
    public function profile(){
        
    	return response()->json([
            'user'=>auth('admin-api')->user(), 
            'message'=> 'Admin found Successfully!'
        ], 200); 
    }
/*--------./Profile--------*/ 



/*--------Logout--------*/ 

    public function logout(){
    	
    	auth('admin-api')->logout();

    	return response()->json([
            'message'=>'Admin logged out Successfully'
        ],200);
    }
/*-------./Logout---------*/ 











/*-----Refresh API------*/ 
    public function refresh(){

    	return $this->respondWithToken(
            auth('admin-api')->refresh(), 
            "Admin Token refreshed Successfully"
        );
    }
/*-----./Refresh API------*/ 

    protected function respondWithToken($token, $message){
    	return response()->json([
    		'token'           => $token,
    		'token_type'      =>'bearer',
    		'token_validity'  => auth('admin-api')->factory()->getTTL() * 8760 * 60,
            'message'         =>$message
    	]);
    }


    
}
