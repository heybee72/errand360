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

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;
 
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

            $token_validity = 8760 * 60;

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
                'profile_image' =>'required|mimes:jpeg,png,jpg|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json(["message"=> $validator->errors()], 422);
            }

            $uploadFolder = 'profiles';

            if ($image = $request->file('profile_image')) {
                 $found = User::find($user->id);
                    $image_uploaded_path = $image->store($uploadFolder, 'public');
                    $found->profile_image     = env('APP_URL', 'https://api.mulloy.co').'/mulloy/public'.Storage::url($image_uploaded_path);

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
    public function forgot_password(Request $request)
    {   

        $email = $request->email;

        $getUser = DB::table('users')
            ->select('*')
            ->where('email', '=', $email)
            ->get();

        $count = count($getUser);

        if($count == 1){

            $rand = mt_rand(11111111,99999999);

            $subject = "Password Updated Successfully!";

                $message =  ' 
                    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                    <html xmlns="http://www.w3.org/1999/xhtml">
                    <head>
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
                    <meta name="color-scheme" content="light">
                    <meta name="supported-color-schemes" content="light">
                    <style>
                    @media  only screen and (max-width: 600px) {
                    .inner-body {
                    width: 100% !important;
                    }
                    
                    .footer {
                    width: 100% !important;
                    }
                    }
                    
                    @media  only screen and (max-width: 500px) {
                    .button {
                    width: 100% !important;
                    }
                    }
                    </style>
                    </head>
                    <body style="box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, '.'Segoe UI'.',Roboto, Helvetica, Arial, sans-serif, '.'Apple Color Emoji'.', '.'Segoe UI Emoji'.', '.'Segoe UI Symbol'.'; position: relative; -webkit-text-size-adjust: none; background-color: #ffffff; color: #718096; height: 100%; line-height: 1.4; margin: 0; padding: 0; width: 100% !important;">
                    
                    <table class="wrapper" width="100%" cellpadding="0" cellspacing="0" role="presentation" style="box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, '.'Segoe UI'.', Roboto, Helvetica, Arial, sans-serif, '.'Apple Color Emoji'.', '.'Segoe UI Emoji'.', '.'Segoe UI Symbol'.'; position: relative; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%; background-color: #edf2f7; margin: 0; padding: 0; width: 100%;">
                    <tr>
                    
                    
                    <td align="center" style="box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, '.'Segoe UI'.', Roboto, Helvetica, Arial, sans-serif, '.'Apple Color Emoji'.', '.'Segoe UI Emoji'.', '.'Segoe UI Symbol'.'; position: relative;">
                    <table class="content" width="100%" cellpadding="0" cellspacing="0" role="presentation" style="box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, '.'Segoe UI'.', Roboto, Helvetica, Arial, sans-serif, '.'Apple Color Emoji'.', '.'Segoe UI Emoji'.', '.'Segoe UI Symbol'.'; position: relative; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%; margin: 0; padding: 0; width: 100%;">
                    <tr>
                    <td class="header" style="box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, '.'Segoe UI'.', Roboto, Helvetica, Arial, sans-serif, '.'Apple Color Emoji'.', '.'Segoe UI Emoji'.', '.'Segoe UI Symbol'.'; position: relative; padding: 25px 0; text-align: center;">
                    <a href="http://localhost" style="box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, '.'Segoe UI'.', Roboto, Helvetica, Arial, sans-serif, '.'Apple Color Emoji'.', '.'Segoe UI Emoji'.', '.'Segoe UI Symbol'.'; position: relative; color: #3d4852; font-size: 19px; font-weight: bold; text-decoration: none; display: inline-block;">
                    Mulloy
                    </a>
                    </td>
                    </tr>
                    
                    <!-- Email Body -->
                    <tr>
                    <td class="body" width="100%" cellpadding="0" cellspacing="0" style="box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, '.'Segoe UI'.', Roboto, Helvetica, Arial, sans-serif, '.'Apple Color Emoji'.', '.'Segoe UI Emoji'.', '.'Segoe UI Symbol'.'; position: relative; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%; background-color: #edf2f7; border-bottom: 1px solid #edf2f7; border-top: 1px solid #edf2f7; margin: 0; padding: 0; width: 100%;">
                    <table class="inner-body" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation" style="box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, '.'Segoe UI'.', Roboto, Helvetica, Arial, sans-serif, '.'Apple Color Emoji'.', '.'Segoe UI Emoji'.', '.'Segoe UI Symbol'.'; position: relative; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 570px; background-color: #ffffff; border-color: #e8e5ef; border-radius: 2px; border-width: 1px; box-shadow: 0 2px 0 rgba(0, 0, 150, 0.025), 2px 4px 0 rgba(0, 0, 150, 0.015); margin: 0 auto; padding: 0; width: 570px;">
                    <!-- Body content -->
                    <tr>
                    <td class="content-cell" style="box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, '.'Segoe UI'.', Roboto, Helvetica, Arial, sans-serif, '.'Apple Color Emoji'.', '.'Segoe UI Emoji'.', '.'Segoe UI Symbol'.'; position: relative; max-width: 100vw; padding: 32px;">
                    <h1 style="box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, '.'Segoe UI'.', Roboto, Helvetica, Arial, sans-serif, '.'Apple Color Emoji'.', '.'Segoe UI Emoji'.', '.'Segoe UI Symbol'.'; position: relative; color: #3d4852; font-size: 18px; font-weight: bold; margin-top: 0; text-align: left;">Hello!</h1>
                    <p style="box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, '.'Segoe UI'.', Roboto, Helvetica, Arial, sans-serif, '.'Apple Color Emoji'.', '.'Segoe UI Emoji'.', '.'Segoe UI Symbol'.'; position: relative; font-size: 16px; line-height: 1.5em; margin-top: 0; text-align: left;">
                    
                        Here is Your New Password: Kindly Proceed to login
                    
                    
                    </p>
                    <table class="action" align="center" width="100%" cellpadding="0" cellspacing="0" role="presentation" style="box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, '.'Segoe UI'.', Roboto, Helvetica, Arial, sans-serif, '.'Apple Color Emoji'.', '.'Segoe UI Emoji'.', '.'Segoe UI Symbol'.'; position: relative; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%; margin: 30px auto; padding: 0; text-align: center; width: 100%;">
                    <tr>
                    <td align="center" style="box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, '.'Segoe UI'.', Roboto, Helvetica, Arial, sans-serif, '.'Apple Color Emoji'.', '.'Segoe UI Emoji'.', '.'Segoe UI Symbol'.'; position: relative;">
                    <table width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, '.'Segoe UI'.', Roboto, Helvetica, Arial, sans-serif, '.'Apple Color Emoji'.', '.'Segoe UI Emoji'.', '.'Segoe UI Symbol'.'; position: relative;">
                    <tr>
                    <td align="center" style="box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, '.'Segoe UI'.', Roboto, Helvetica, Arial, sans-serif, '.'Apple Color Emoji'.', '.'Segoe UI Emoji'.', '.'Segoe UI Symbol'.'; position: relative;">
                    <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, '.'Segoe UI'.', Roboto, Helvetica, Arial, sans-serif, '.'Apple Color Emoji'.', '.'Segoe UI Emoji'.', '.'Segoe UI Symbol'.'; position: relative;">
                    <tr>
                    <td style="box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, '.'Segoe UI'.', Roboto, Helvetica, Arial, sans-serif, '.'Apple Color Emoji'.', '.'Segoe UI Emoji'.', '.'Segoe UI Symbol'.'; position: relative;">
                    
                    
                    <button class="button button-primary"  style="box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, '.'Segoe UI'.', Roboto, Helvetica, Arial, sans-serif, '.'Apple Color Emoji'.', '.'Segoe UI Emoji'.', '.'Segoe UI Symbol'.'; position: relative; -webkit-text-size-adjust: none; border-radius: 4px; color: #fff; display: inline-block; overflow: hidden; text-decoration: none; background-color: #2d3748; border-bottom: 8px solid #2d3748; border-left: 18px solid #2d3748; border-right: 18px solid #2d3748; border-top: 8px solid #2d3748;">

                    '.$rand.'
                    
                    </button>
                    
                    
                    </td>
                    </tr>
                    </table>
                    </td>
                    </tr>
                    </table>
                    </td>
                    </tr>
                    </table>
                    <p style="box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, '.'Segoe UI'.', Roboto, Helvetica, Arial, sans-serif, '.'Apple Color Emoji'.', '.'Segoe UI Emoji'.', '.'Segoe UI Symbol'.'; position: relative; font-size: 16px; line-height: 1.5em; margin-top: 0; text-align: left;">
                    
                        
                    </p>
                    <p style="box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, '.'Segoe UI'.', Roboto, Helvetica, Arial, sans-serif, '.'Apple Color Emoji'.', '.'Segoe UI Emoji'.', '.'Segoe UI Symbol'.'; position: relative; font-size: 16px; line-height: 1.5em; margin-top: 0; text-align: left;">Regards,<br>
                    Mulloy</p>
                    
                    
                    <table class="subcopy" width="100%" cellpadding="0" cellspacing="0" role="presentation" style="box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, '.'Segoe UI'.', Roboto, Helvetica, Arial, sans-serif, '.'Apple Color Emoji'.', '.'Segoe UI Emoji'.', '.'Segoe UI Symbol'.'; position: relative; border-top: 1px solid #e8e5ef; margin-top: 25px; padding-top: 25px;">
                    <tr>
                    <td style="box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, '.'Segoe UI'.', Roboto, Helvetica, Arial, sans-serif, '.'Apple Color Emoji'.', '.'Segoe UI Emoji'.', '.'Segoe UI Symbol'.'; position: relative;">
                    <p style="box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, '.'Segoe UI'.', Roboto, Helvetica, Arial, sans-serif, '.'Apple Color Emoji'.', '.'Segoe UI Emoji'.', '.'Segoe UI Symbol'.'; position: relative; line-height: 1.5em; margin-top: 0; text-align: left; font-size: 14px;">If you\'re having trouble Updating your password Kindly Contact the Admin Immediately <span class="break-all" style="box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, '.'Segoe UI'.', Roboto, Helvetica, Arial, sans-serif, '.'Apple Color Emoji'.', '.'Segoe UI Emoji'.', '.'Segoe UI Symbol'.'; position: relative; word-break: break-all;"><a href="http://127.0.0.1:8000/api/verify-email/33/e7a10d289ae328de91477d8c2d54409e68f719ed?expires=1624972479&amp;signature=5d4bf706e9fa4007e7aea3891961e25828cdd23467962d51a1d38c755bf0cf44" style="box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, '.'Segoe UI'.', Roboto, Helvetica, Arial, sans-serif, '.'Apple Color Emoji'.', '.'Segoe UI Emoji'.', '.'Segoe UI Symbol'.'; position: relative; color: #3869d4;">
                    
                    </td>
                    </tr>
                    </table>
                    </td>
                    </tr>
                    </table>
                    </td>
                    </tr>
                    
                    <tr>
                    <td style="box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, '.'Segoe UI'.', Roboto, Helvetica, Arial, sans-serif, '.'Apple Color Emoji'.', '.'Segoe UI Emoji'.', '.'Segoe UI Symbol'.'; position: relative;">
                    <table class="footer" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation" style="box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, '.'Segoe UI'.', Roboto, Helvetica, Arial, sans-serif, '.'Apple Color Emoji'.', '.'Segoe UI Emoji'.', '.'Segoe UI Symbol'.'; position: relative; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 570px; margin: 0 auto; padding: 0; text-align: center; width: 570px;">
                    <tr>
                    <td class="content-cell" align="center" style="box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, '.'Segoe UI'.', Roboto, Helvetica, Arial, sans-serif, '.'Apple Color Emoji'.', '.'Segoe UI Emoji'.', '.'Segoe UI Symbol'.'; position: relative; max-width: 100vw; padding: 32px;">
                    <p style="box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, '.'Segoe UI'.', Roboto, Helvetica, Arial, sans-serif, '.'Apple Color Emoji'.', '.'Segoe UI Emoji'.', '.'Segoe UI Symbol'.'; position: relative; line-height: 1.5em; margin-top: 0; color: #b0adc5; font-size: 12px; text-align: center;">© 2021 Mulloy. All rights reserved.</p>
                    
                    
                    
                    
                    </td>
                    </tr>
                    </table>
                    </td>
                    </tr>
                    </table>
                    </td>
                    </tr>
                    </table>
                    </body>
                    </html>
                    
                    
                    
                    
                    
                ';
            
            require base_path("vendor/autoload.php");
            $mail = new PHPMailer(true);     // Passing `true` enables exceptions

            try {

                // Email server settings
                $mail->SMTPDebug = 0;
                // $mail->isSMTP();
                $mail->Host = 'mulloy.co';             //  smtp host
                $mail->SMTPAuth = true;
                $mail->Username = 'admin@mulloy.co';   //  sender username
                $mail->Password = '$500@Mulloy';       // sender password
                $mail->SMTPSecure = 'tls';                  // encryption - ssl/tls
                $mail->Port = 465;                          // port - 587/465

                $mail->setFrom('admin@mulloy.co', 'Mulloy');
                $mail->addAddress($email);

                $mail->isHTML(true);                // Set email content format to HTML

                $mail->Subject = $subject;
                $mail->Body    = $message;

                // $mail->AltBody = plain text version of email body;

                if( !$mail->send() ) {
                    return response()->json([
                        'message'=>'Email not sent',
                        'error'=> $mail->ErrorInfo
                    ], 500); 

                }
                
                else {

                    // TODO:: update user password here

                    $new_pass = Hash::make($rand);

                    $affected = DB::table('users')
                    ->where('email', $email)
                    ->update(['password' => $new_pass]);

                    return response()->json(['message'=>'an Email has been sent.'], 200); 

                }

            } catch (\Exception $e) {
                return response()->json(['message'=>'Message could not be sent.','error'=> $mail->ErrorInfo], 422);
            }

        }


    }

/*----./Forgot password----*/ 



     protected function respondWithToken($token, $message){
    	return response()->json([
    		'token'           => $token,
    		'token_type'      =>'bearer',
    		'token_validity'  => auth('admin-api')->factory()->getTTL()  * 60,
            'message'         =>$message
    	]);
    }
    
    protected function guard(){
    	return Auth::guard();
    }
}
