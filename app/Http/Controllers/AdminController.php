<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    public function create(Request $request)
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

        $user = new User();
        $user->email = $data['email'];
        $user->password = hash('sha256', $request->password);
        $user->save();
        storelog('New user registration', $user,'Linux OS');

        return response([
            'status'=>'success',
            'message'=>"User created successfully",
            'user'=>$user
        ]);
    }
    public function show_admin(Request $request)
    {
        $admin=User::all();
        return response([
            'status'=>'success',
            'users'=>$admin,
        ]);

    }
}
