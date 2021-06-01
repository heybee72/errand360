<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
// use JWTAuth;

class AdminAuthController extends Controller
{
    public function __construct(){
    	$this->middleware('jwt.verify:api', ['except' => ['login','register'] ]);
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

            $token_validity = 24 * 60;

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


/*--------Delete--------*/ 

     public function delete($id){
            
            $admin = Admin::find($id);
            $admin->delete();

        return response()->json([
            'message'=> 'Admin Deleted Successfully!'
        ], 200); 
     }
/*--------./Delete--------*/ 


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
    		'token_validity'  => auth('admin-api')->factory()->getTTL() * 24 * 60,
            'message'         =>$message
    	]);
    }

    
}
