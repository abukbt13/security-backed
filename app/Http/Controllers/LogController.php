<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\Random_log;
use App\Models\Secret_Key;
use Illuminate\Http\Request;

class LogController extends Controller
{

    /**
     * Display the specified resource.
     */
    public function show()
    {
        $log=Log::all();
        return response([
            'status'=>'success',
            'data'=>$log,
        ]);
    } public function show_keys()
    {
        function dencryptdata($data, $key_to_use) {
            $encryption_key = base64_encode($key_to_use);
            list($encrypted_data, $iv) = array_pad(explode('::', base64_decode($data), 2), 2, null);
            return openssl_decrypt($encrypted_data, 'aes-256-cbc', $encryption_key, 0, $iv);
        }
        $secret = "@topsecurity@123secured";

        $secret_keys = Secret_Key::join('court_cases', 'court_cases.key', '=', 'secret__keys.id')->join('random_logs', 'random_logs.c_id', '=', 'court_cases.id')->join('users', 'users.id', '=', 'random_logs.u_id')->get();
        foreach ($secret_keys as $case) {
            $u_id= $case->u_id;
            $u_key= $case->u_key;
            $email= $case->email;
            $description= dencryptdata($case->description,$secret);
            $case_name =$case->case_name;
            $decrypted_keys[] = [
                'u_id' => $u_id,
                'u_key' => $u_key,
                'email' => $email,
                'case_name' => $case_name,
                'description' => $description,
            ];
        }
        return response([
            'status'=>'success',
            'secret_keys'=>$decrypted_keys,
        ]);
    }


}
