<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use DB;

use Illuminate\Support\Facades\Password;
// use Illuminate\Support\Facades\Response;

class AuthController extends Controller
{
// public $arr;
    public function __construct(){
    	$this->middleware('auth:api', ['except' => [ 'login', 'register','forgot_password'] ]);
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

            $token_validity = 24 * 60;

            $this->guard()->factory()->setTTL($token_validity);

            if (!$token = $this->guard()->attempt($validator->validated())) {

                return response()->json([
                    'status'  => false,
                    'message' => 'Invalid Login Details'
                ], 401);
            }
            $user = DB::table('users')
            ->select('id', 'name', 'email', 'user_plan', 'notification_status', 'ref_code', 'profile_image', 'campus', 'role')
            ->where('users.email', $request->get('email'))
            ->get();        

            return response()->json([
                'status' => true,
            	'token' => $token,
                'message' => 'User logged in Successfully!',
                'token_validity'  => $this->guard()->factory()->getTTL() * 60,
                'user' => $user[0]
            ]);
    	   // return $this->respondWithToken($token, "User logged in Successfully!");	

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
    		'email'       =>'required|email|unique:users',
    		'password'    => 'required|min:6'
    	]);

    	if ($validator->fails()) {
    		return response()->json(["message"=> $validator->errors()], 422);
    	}

        $user               = new User();
        $user->name         = $request->get('name');
        $user->email        = $request->get('email');
        $plainPassword      = $request->get('password');
        $user->password     = Hash::make($plainPassword);
        $user->ref_code     = Str::random(8);
        $user->api_token    = Str::random(60);
        $user->user_ip      = request()->ip();
        $user->save();

    	return response()->json(['user'=>$user, 'message'=>'User Created Successfully'], 200);
    }
/*-----./End of registration API------*/ 


/*-------profile api-------*/ 
    public function profile(){
        if (null === $this->guard()->user()) {
            return response()->json([
                'message'=> 'User not found!'
            ], 401); 
        }
    	return response()->json([
            'user'=>$this->guard()->user(), 
            'message'=> 'User found Successfully!'
        ], 200); 
    }
/*------./user profile api---------*/ 


/*-------Update profile image api-------*/ 
    public function profile_image_update(Request $request){

        $user = auth()->setToken($request->bearerToken())->user();

       

        if ($user == NULL) {
            return response()->json([
                'message'=> 'User not found!'
            ], 401); 

        }else{

            $validator = Validator::make($request->all(), [
                'profile_image' =>'required|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json(["message"=> $validator->errors()], 422);
            }

            $uploadFolder = 'profiles';

            if ($image = $request->file('profile_image')) {
                 $found = User::find($user->id);
                    $image_uploaded_path = $image->store($uploadFolder, 'public');
                    $found->profile_image     = env('APP_URL', 'http://127.0.0.1:8000').Storage::url($image_uploaded_path);

                $found->save();

                return response()->json([
                    'user'=>$this->guard()->user(), 
                    'message'=> 'User profile updated Successfully!'
                ], 200);     
            }else{
                return response()->json(['message'=>'An error occurred!'], 500);
            }
        }
        
    }
/*------./Updateprofile image api---------*/ 


/*-------Update profile image api-------*/ 
    public function update(Request $request){

        $user = auth()->setToken($request->bearerToken())->user();

        if ($user == NULL) {
            return response()->json([
                'message'=> 'User not found!'
            ], 401); 

        }else{

            $validator = Validator::make($request->all(), [
                'name' =>'required',
                'campus' =>'required',
            ]);

            if ($validator->fails()) {
                return response()->json(["message"=> $validator->errors()], 422);
            }
                $found = User::find($user->id);
                $found->name = $request->name;
                $found->campus = $request->campus;
                $found->save();

                return response()->json([
                    'user'=>$this->guard()->user(), 
                    'message'=> 'User profile updated Successfully!'
                ], 200);     
        }
        
    }
/*------./Updateprofile image api---------*/ 


/*-------logout api-----*/ 
    public function logout(){
    	
    	$this->guard()->logout();

    	return response()->json(['message'=>'User logged out Successfully']);
    }
/*--------logout api---------*/ 

/*-----Refresh API------*/ 
    public function refresh(){
    	return $this->respondWithToken($this->guard()->refresh(), "Token refreshed Successfully");
    }
/*-----./Refresh API------*/ 

/*------Delete user Api------*/ 
    public function delete($id){
            
            $user = User::find($id);
            $user->delete();

        return response()->json([
            'message'=> 'User Deleted Successfully!'
        ], 200); 
    }
/*------Delete user Api------*/ 


/*--------change password api--------*/ 
    public function change_password(Request $request)
    {
        $input = $request->all();
        $userid = $this->guard()->user()->id;
        $rules = array(
            'old_password' => 'required',
            'new_password' => 'required|min:6',
            'confirm_password' => 'required|same:new_password',
        );
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            $arr = array("status" => 400, "message" => $validator->errors()->first(), "data" => array());
        } else {
            try {
                if ((Hash::check(request('old_password'), Auth::user()->password)) == false) {
                    $arr = array("status" => 400, "message" => "Check your old password.", "data" => array());
                } else if ((Hash::check(request('new_password'), Auth::user()->password)) == true) {
                    $arr = array("status" => 400, "message" => "Please enter a password which is not similar then current password.", "data" => array());
                } else {
                    User::where('id', $userid)->update(['password' => Hash::make($input['new_password'])]);
                    $arr = array("status" => 200, "message" => "Password updated successfully.", "data" => array());
                }
            } catch (\Exception $ex) {
                if (isset($ex->errorInfo[2])) {
                    $msg = $ex->errorInfo[2];
                } else {
                    $msg = $ex->getMessage();
                }
                $arr = array("status" => 400, "message" => $msg, "data" => array());
            }
        }
        return \Response::json($arr);
    }
/*--------change password api--------*/ 


/*----Forgot password----*/ 
    public function forgot_password()
    {   

        $credentials = request()->validate(['email'=>'required|email']);

        Password::sendResetLink($credentials);

        return response()->json([
            'message'=> 'password send to your email Successfully!'
        ], 200); 

    }

/*----./Forgot password----*/ 



    protected function respondWithToken($token, $message){
    	return response()->json([
    		'token'           => $token,
    		'token_type'      =>'bearer',
    		'token_validity'  => $this->guard()->factory()->getTTL() * 60,
            'message'         =>$message
    	]);
    }

    protected function guard(){
    	return Auth::guard();
    }
}
