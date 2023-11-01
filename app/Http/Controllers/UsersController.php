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

        $user = new User();
        $user->email = $data['email'];
        $user->password = Hash::make($request->password);
        $user->save();
        if (Auth::attempt(['email' => $data['email'], 'password' => $data ['password']])) {
            $token = $user->createToken('token')->plainTextToken;
            return response([
                'status'=>'success',
                'token'=>$token,
                'user'=>$user

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
        $password = request('password');
        $user = User::where('email', $email)->get()->first();

        if (Auth::attempt(['email' => $email, 'password' => $password])) {
            $token = $user->createToken('token')->plainTextToken;
            return response([
                'status' => 'success',
                'token' => $token,
                'user' => $user
            ]);
        }
        else{
            return response([
                'status' => 'failed',
                'message' => 'Enter correct details',
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
    public function show(){
        $users=User::all();
        return response([
            'status'=>'success',
            'users'=>$users,
        ]);
    }

}
