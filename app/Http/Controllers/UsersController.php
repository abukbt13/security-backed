<?php

namespace App\Http\Controllers;

use App\Models\Inquire;
use App\Models\Offence;
use App\Models\User;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UsersController extends Controller
{
    public function register(Request $request)
    {
        $rules = [
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'phone' => 'required',
        ];
        $data = request()->all();
        $valid = Validator::make($data, $rules);
        if (count($valid->errors())){
            return response([
                'status' => 'failed',
                'error' => $valid->errors()
            ]);
        }
//        storelog('New user registration', $user->email,'Linux OS');
        $email = request('email');
        $password = hash('sha256', $request->password);
        $user = new User();
        $user->email = $email;

        $phone = request('phone');
        // Remove leading '0' from phone number
        $phone = substr($phone, 0, 1) == '0' ? substr($phone, 1) : $phone;

        // Add the country code
        $user->phone = "+254" . $phone;

        $user->password = $password;
        $user->save();
        if (Auth::attempt(['email' => $email, 'password' => $password]))
        {
            $token = $user->createToken('token')->plainTextToken;
            return response([
                'status'=>'success',
                'token'=>$token,
                'user'=>$user
            ]);
        }
        else{
            return response([
                'status'=>'failed',
                'user'=>"Credentials does not match"
            ]);
        }

    }
    public function login(Request $request)
    {
        $data = request()->all();
        $rules = [
            'email' => 'required',
            'password' => 'required'
        ];
        $valid = Validator::make($data, $rules);
        if (count($valid->errors())) {
            return response([
                'status' => 'failed',
                'errors' => $valid->errors()
            ],422);
        }
        $email = request('email');

        $password = hash('sha256', $request->password);

        $user = User::where('email', $email)->get()->first();
//        $to=$user->phone;
        if (Auth::attempt(['email' => $email, 'password' => $password])) {
            $token = $user->createToken('token')->plainTextToken;


            return response([
                'status' => 'success',
                'token' => $token,
                'user' => $user,
            ]);
        }
            else{
                return response([
                    'status' => 'failed',
                    'message' => 'Enter correct details'
                ]);
            }


    }

    public function auth(){
        if (Auth::check()) {
            return response()->json(['authenticated' => true]);
        } else {
            return response()->json(['authenticated' => false]);
        }
    }
    public function verify(Request $request,$id){
        $user = User::where('id', $id)
            ->where('otp', $request->otp)
            ->first();

        if($user){
            return response([
                'status'=>'success',
                'user'=>$user,
            ]);
        }
        else{
            return response([
                'status'=>'failed',
                'message'=>'Enter correct credentials !',
            ]);
        }

    }
    public function show(){
        $users=User::all();
        return response([
            'status'=>'success',
            'users'=>$users,
        ]);
    }
    public function forget_pass(){
        $rules = [
            'email' => 'required',
        ];
        $data = request()->all();
        $valid = Validator::make($data, $rules);
        if (count($valid->errors())){
            return response([
                'status' => 'failed',
                'error' => $valid->errors()
            ]);
        }
        $email=$data['email'];
        $user = User::where('email',$email)->first();
        if ($user){
            $otp = rand(999,10000);
           $user->otp = $otp;
           $user->update();

           $to=$user->phone;

            $curl = curl_init();
            $message ="Use OTP : $otp to proceed  logging into the system";
            $data = array(
                'api_token' => env('API_TOKEN'),
                'from' => env('SENDER_NAME'),
                'to' => $to,
                'message' => $message
            );

            curl_setopt_array($curl, array(
                CURLOPT_URL => env('CURLOPT_URL'),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => http_build_query($data),
            ));

            $response = curl_exec($curl);
            curl_close($curl);
            $data = json_decode($response, true);

            $status = $data['status'];
            if($status == 'success'){
                return response([
                    'status'=>'success'
                ]);
            }
            else{
                return response([
                    'failed'=>'Try again later'
                ]);
            }

        }



        else{
            return response([
                'status'=>'User not found'
            ]);
        }

    }
    public function reset_password(){
        $rules = [
            'email' => 'required',
            'otp' => 'required',
        ];
        $data = request()->all();
        $valid = Validator::make($data, $rules);
        if (count($valid->errors())){
            return response([
                'status' => 'failed',
                'error' => $valid->errors()
            ]);
        }
        $email=$data['email'];
        $otp=$data['otp'];

        $user = User::where('email',$email)->where('otp',$otp)->first();
        if ($user){
                return response([
                    'status'=>'success',
                    'message' =>'Success you can change your password'
                ]);
        }
        else{
            return response([
                'status'=>'failed',
                'message' =>'Enter correct details '
            ]);
        }



    }
    public function finish_reset(){
        $rules = [
            'email' => 'required',
            'otp' => 'required',
            'password' => [
                'required',
                'min:8', // Enforce minimum password length of 6 characters
                'regex:/[A-Z]+/', // Ensure at least one uppercase letter
                'regex:/[!@#$%^&*()_+:\-=\[\]{};"\\|,.<>\/?]+/', // Ensure at least one symbol (excluding common delimiters)
            ],
        ];
        $data = request()->all();
        $valid = Validator::make($data, $rules);
        if (count($valid->errors())){
            return response([
                'status' => 'failed',
                'message' =>'Ensure you enter correct details',
                'error' => $valid->errors()
            ]);
        }
        $email=$data['email'];
        $otp=$data['otp'];
        $password=$data['password'];
        $hah_password = hash('sha256',$password);
        $user = User::where('email',$email)->where('otp',$otp)->first();
        if ($user){

            $user->password = $hah_password;
            $user->update();
                return response([
                    'status'=>'success',
                    'message' =>'Password changed successfully'
                ]);
        }
        else{
            return response([
                'status'=>'failed',
                'message' =>'Ensure correct details are entered'
            ]);
        }



    }

    public function inquire(Request $request){
        $rules = [
            'name' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'message' => 'required',
        ];
        $data = request()->all();
        $valid = Validator::make($data, $rules);
        if (count($valid->errors())){
            return response([
                'status' => 'failed',
                'error' => $valid->errors()
            ]);
        }

        $inquire = new Inquire();
        $inquire->name = $data['name'];
        $inquire->email = $data['email'];
        $inquire->message = $data['message'];
        $inquire->phone = $data['phone'];
        $inquire->save();

        return response([
            'status' => 'success',
            'message' => 'Feedback saved successfully will contact you sooner'
        ]);
    }

}
