<?php

namespace App\Http\Controllers;

use App\Models\Court_case;
use App\Models\Inquire;
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
            'name' => 'required',
            'password' => [
                'required',
                'min:6', // Enforce minimum password length of 6 characters
                'regex:/[A-Z]+/', // Ensure at least one uppercase letter
                'regex:/[!@#$%^&*()_+:\-=\[\]{};"\\|,.<>\/?]+/', // Ensure at least one symbol (excluding common delimiters)
                ],
                'phone' => 'required|digits:10', // Validate phone to be 10 characters
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
        $user_phone = ltrim($data['phone'], '0');
        $user->phone = '+254' . $user_phone;
        $user->name = $data['name'];
        $user->password = hash('sha256', $request->password);
        $user->save();
        storelog('New user registration', $user,'Linux OS');

        return response([
            'status'=>'success',
            'message'=>"User created successfully",
            'user'=>$user
        ]);
    }
    public function edit(Request $request,$id)
    {

        $rules = [
            'email' => 'required',
            'name' => 'required',
            'phone' => 'required|digits:9', // Validate phone to be 10 characters
        ];

        $data = request()->all();
        $valid = Validator::make($data, $rules);
        if (count($valid->errors())){
            return response([
                'status' => 'failed',
                'error' => $valid->errors()
            ]);
        }

        $user = User::find($id);
        $user->email = $data['email'];
        $user_phone = ltrim($data['phone'], '0');
        $user->phone = '+254' . $user_phone;
        $user->name = $data['name'];

        $user->Update();
        storelog('Update operation done', $user,'Linux OS');

        return response([
            'status'=>'success',
            'message'=>"User Updated successfully",
            'user'=>$user
        ]);
    }
    public function show_admin(Request $request)
    {
        $admin=User::where('role','admin')->get();
        return response([
            'status'=>'success',
            'users'=>$admin,
        ]);

    }
    public function show_cases(Request $request)
    {
        $cases=Court_case::all();
        return response([
            'status'=>'success',
            'cases'=>$cases,
        ]);

    }
    public function show_inquiries(Request $request)
    {
        $inquiries=Inquire::all();
        return response([
            'status'=>'success',
            'inquiries'=>$inquiries,
        ]);

    }
}
