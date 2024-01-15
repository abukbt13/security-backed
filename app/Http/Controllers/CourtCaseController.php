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
        $court_case = new Court_case();

        $key=$request['key'];
        $secret_key=new Secret_Key();
        $secret_key->user_id= $user_id;
        $secret_key->key= encryptdata($request->key,$key);
        $secret_key->save();

        // Create a new instance of the Court_case model


        // Assign values from the request to the model's properties
        $court_case->key = $secret_key->id;
        $court_case->plaintiff_id = $request->plaintiff_id;
        $court_case->plaintiff_name = $request->plaintiff_name;
        $court_case->defendant_id = $request->defendant_id;
        $court_case->defendant_name = $request->defendant_name;
        $court_case->case_name = $request->case_name;
        $court_case->description =  encryptdata($request->description,$key);
        $court_case->type_of_case = $request->type_of_case;
        $court_case->user_id = $user_id;


        // Save the model to the database
        $court_case->save();

        // Assuming that storelog and storerandom_log are custom functions
        storelog($court_case['type_of_case'], 'Abraham', 'MacOs');
        storerandom_log($court_case->id, '45', $request->key);

        return response([
            'status' => 'success',
            'data' => $court_case,
        ]);
    }

    public function show()
    {
        $auth_user = Auth::user()->id;
        $cases = Court_case::select('id','key', 'case_name', 'plaintiff_name', 'defendant_name', 'type_of_case', 'defendant_id', 'plaintiff_id')
            ->where('user_id', $auth_user)
            ->where('status', 'active')
            ->get();

        return response([
            'status' => 'success',
            'cases' => $cases,
        ]);

    }
    public function show_deactivated()
    {
        $auth_user = Auth::user()->id;
        $cases = Court_case::select('id','key', 'case_name', 'plaintiff_name', 'defendant_name', 'type_of_case', 'defendant_id', 'plaintiff_id')
            ->where('user_id', $auth_user)
            ->where('status', 'deactivated')
            ->get();

        return response([
            'status' => 'success',
            'cases' => $cases,
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
