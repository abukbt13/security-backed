<?php

namespace App\Http\Controllers;

use App\Models\Court_case;
use App\Models\Secret_Key;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CourtCaseController extends Controller
{
    public function create(Request $request)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'key' => 'required',
            'plaintiff_id' => 'required',
            'plaintiff_name' => 'required',
            'defendant_id' => 'required',
            'defendant_name' => 'required',
            'case_name' => 'required',
            'type_of_case' => 'required',
            'description' => 'required',
        ]);

        // Check validation failure and return errors if validation fails
        if ($validator->fails()) {
            return response([
                'status' => 'failed',
                'errors' => $validator->errors()
            ], 422);
        }
        function encryptdata($data,$key){
            $encryption_key= base64_encode($key);
            $iv=openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
            $encrypted = openssl_encrypt($data,'aes-256-cbc',$encryption_key,0,$iv);
            return base64_encode($encrypted . '::' . $iv);
        }

        $user_id = Auth::user()->id;
        $key=$request['key'];
        $secret ="@topsecurity@123secured";
        $secret_key=new Secret_Key();
        $secret_key->user_id= $user_id;
        $secret_key->key= $request->key;
        $secret_key->save();

        $court_case = new Court_case();

        // Assign values from the request to the model's properties
        $court_case->key = $secret_key->id;

        $court_case->plaintiff_id = encryptdata($request->plaintiff_id,$secret);
        $court_case->plaintiff_name = encryptdata($request->plaintiff_name,$secret);
        $court_case->defendant_id = encryptdata($request->defendant_id,$secret);
        $court_case->defendant_name = encryptdata($request->defendant_name,$secret);
        $court_case->case_name = encryptdata($request->case_name,$secret);
        $court_case->description =  encryptdata($request->description,$key);
        $court_case->type_of_case = encryptdata($request->type_of_case,$secret);
        $court_case->user_id = $user_id;


        // Save the model to the database
        $court_case->save();

        // Assuming that storelog and storerandom_log are custom functions
        storelog($court_case['type_of_case'], 'alteration', 'MacOs');
        storerandom_log($court_case->id, $user_id, $request->key);

        return response([
            'status' => 'success',
            'data' => $court_case,
        ]);
    }

    public function update(Request $request, $case_id)
    {
        $validator = Validator::make($request->all(), [
                'key' => 'required',
                'description' => 'required',
             ]);

        // Check validation failure and return errors if validation fails
        if ($validator->fails()) {
            return response([
                'status' => 'failed',
                'errors' => $validator->errors()
            ], 422);
        }
        $auth_user = Auth::user()->id;

        function encryptdata($data,$key){
            $encryption_key= base64_encode($key);
            $iv=openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
            $encrypted = openssl_encrypt($data,'aes-256-cbc',$encryption_key,0,$iv);
            return base64_encode($encrypted . '::' . $iv);
        }

        $case_update = Court_case::where('user_id', $auth_user)
            ->where('id', $case_id)
            ->first();

        $user_key=$request['key'];
        $description=$request['description'];
        $case_update->description =  encryptdata($description,$user_key);

        $case_update->update();

        return response([
            'status' => 'success',
            'cases' => $case_update,
        ]);

    }
    public function show()
    {
        function dencryptdata($data, $key_to_use) {
            $encryption_key = base64_encode($key_to_use);
            list($encrypted_data, $iv) = array_pad(explode('::', base64_decode($data), 2), 2, null);
            return openssl_decrypt($encrypted_data, 'aes-256-cbc', $encryption_key, 0, $iv);
        }

        $secret = "@topsecurity@123secured";
        $auth_user = Auth::user()->id;
        $actual_user_id = dencryptdata($auth_user, $secret);
        // Fetch all rows from the Court_case table
        $cases = Court_case::where('user_id', $auth_user)->where('status','active')->get();

        // Decrypt each column in each row
        $decrypted_cases = [];
        foreach ($cases as $case) {
            $id = $case->id;
            $key = $case->key;
            $defendant_name = dencryptdata($case->defendant_name, $secret);
            $plaintiff_id  = dencryptdata($case->plaintiff_id, $secret);
            $plaintiff_name = dencryptdata($case->plaintiff_name, $secret);
            $case_name  = dencryptdata($case-> case_name , $secret);
            $status = $case->status;
            $type_of_case = dencryptdata($case->type_of_case, $secret);
            $user_id = dencryptdata($case->user_id, $secret);

            // Add the decrypted data to the result array
            $decrypted_cases[] = [
                'id' => $id,
                'key' => $key,
                'defendant_name' => $defendant_name,
                'plaintiff_id' => $plaintiff_id,
                'plaintiff_name' => $plaintiff_name,
                'case_name' => $case_name,
                'status' => $status,
                'type_of_case' => $type_of_case,
                'user_id' => $user_id,
                // Add more columns as needed
            ];
        }

        return response([
            'status' => 'success',
            'cases' => $decrypted_cases,
        ]);
    }

    public function show_deactivated()
    {
        function dencryptdata($data, $key_to_use) {
            $encryption_key = base64_encode($key_to_use);
            list($encrypted_data, $iv) = array_pad(explode('::', base64_decode($data), 2), 2, null);
            return openssl_decrypt($encrypted_data, 'aes-256-cbc', $encryption_key, 0, $iv);
        }

        $secret = "@topsecurity@123secured";
        $auth_user = Auth::user()->id;
        $actual_user_id = dencryptdata($auth_user, $secret);
        // Fetch all rows from the Court_case table
        $cases = Court_case::where('user_id', $auth_user)->where('status','deactivated')->get();

        // Decrypt each column in each row
        $decrypted_cases = [];
        foreach ($cases as $case) {
            $id = $case->id;
            $key = $case->key;
            $defendant_name = dencryptdata($case->defendant_name, $secret);
            $plaintiff_id  = dencryptdata($case->plaintiff_id, $secret);
            $plaintiff_name = dencryptdata($case->plaintiff_name, $secret);
            $case_name  = dencryptdata($case-> case_name , $secret);
            $status = $case->status;
            $type_of_case = dencryptdata($case->type_of_case, $secret);
            $user_id = dencryptdata($case->user_id, $secret);

            // Add the decrypted data to the result array
            $decrypted_cases[] = [
                'id' => $id,
                'key' => $key,
                'defendant_name' => $defendant_name,
                'plaintiff_id' => $plaintiff_id,
                'plaintiff_name' => $plaintiff_name,
                'case_name' => $case_name,
                'status' => $status,
                'type_of_case' => $type_of_case,
                'user_id' => $user_id,
                // Add more columns as needed
            ];
        }

        return response([
            'status' => 'success',
            'cases' => $decrypted_cases,
        ]);
    }
    public function deactivate(Request $request,$id)
    {
        $cases =Court_case::find($id);
        $cases['status'] = 'deactivated';
        $cases->update();
        return response([
            'status' => 'success',
            'cases' => $cases,
        ]);

    }
    public function activate(Request $request,$id)
    {
        $auth_user = Auth::user()->id;
        $cases =Court_case::find($id);
        $cases['status'] = 'active';
        $cases->update();
        return response([
            'status' => 'success',
            'cases' => $cases,
        ]);
    }

    public function change_status(Request $request,$id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required',

        ]);

        // Check validation failure and return errors if validation fails
        if ($validator->fails()) {
            return response([
                'status' => 'failed',
                'errors' => $validator->errors()
            ], 422);
        }
        $cases = Court_case::find($id);
        $cases ->status=$request->status;
        $cases->update();

        return response([
            'status' => 'success',
            'cases' => $cases,
        ]);
    }
    public function edit(Request $request,$id)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required',
            'plaintiff_id' => 'required',
            'plaintiff_name' => 'required',
            'defendant_id' => 'required',
            'defendant_name' => 'required',
            'case_name' => 'required',
            'type_of_case' => 'required',
        ]);

        // Check validation failure and return errors if validation fails
        if ($validator->fails()) {
            return response([
                'status' => 'failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Create a new instance of the Court_case model
        $court_case = Court_case::find($id);

        // Assign values from the request to the model's properties
        $court_case->key = $request->key;
        $court_case->plaintiff_id = $request->plaintiff_id;
        $court_case->plaintiff_name = $request->plaintiff_name;
        $court_case->defendant_id = $request->defendant_id;
        $court_case->defendant_name = $request->defendant_name;
        $court_case->case_name = $request->case_name;
        $court_case->type_of_case = $request->type_of_case;

        // Save the model to the database
        $court_case->update();

        // Assuming that storelog and storerandom_log are custom functions
        storelog($court_case['type_of_case'], 'Abraham', 'MacOs');
        storerandom_log($court_case->id, '45', $request->key);

        return response([
            'status' => 'success',
            'message' => 'Successfully updated',
            'data' => $court_case,
        ]);
    }
}
