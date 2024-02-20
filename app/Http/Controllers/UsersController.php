<?php

namespace App\Http\Controllers;

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
        $to=$user->phone;
        if (Auth::attempt(['email' => $email, 'password' => $password])) {
            $token = $user->createToken('token')->plainTextToken;

//
//            $otp = rand(999,10000);
//           $user->otp = $otp;
//           $user->update();
//
//            $curl = curl_init();
//            $message ="Use OTP : $otp to proceed  logging into the system";
//            $data = array(
//                'api_token' => 'BjBz8xAii6Tb7c8C4xhTBrUJkl91cSYD3Kt3n3AtQy56LtBczsVE5b3IFORUIqMVrhnjMXfRM2XdYDbgfcA2FQ',
//                'from' => 'SHARA',
//                'to' => $to,
//                'message' => $message
//            );
//
//            curl_setopt_array($curl, array(
//                CURLOPT_URL => 'https://app.sharasms.co.ke/api/sms/send',
//                CURLOPT_RETURNTRANSFER => true,
//                CURLOPT_ENCODING => '',
//                CURLOPT_MAXREDIRS => 10,
//                CURLOPT_TIMEOUT => 0,
//                CURLOPT_FOLLOWLOCATION => true,
//                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//                CURLOPT_CUSTOMREQUEST => 'POST',
//                CURLOPT_POSTFIELDS => http_build_query($data),
//            ));
//            curl_close($curl);
//            $response = curl_exec($curl);
//            $data = json_decode($response, true);
//
//            $status = $data['status'];
//

            return response([
                'status' => 'success',
                'token' => $token,
                'user' => $user,
//                'id' => $user->id,
            ]);
        }
            else{
                return response([
                    'status' => 'Failed',
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

}
