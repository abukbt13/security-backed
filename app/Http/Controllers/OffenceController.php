<?php

namespace App\Http\Controllers;

use App\Models\Court_case;
use App\Models\Offence;
use App\Models\Secret_Key;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OffenceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request){

        $validator = Validator::make($request->all(), [
            'key' =>'required',
            'title' =>'required',
            'description' => 'required',
        ]);
        // Check validation failure
        if (count($validator->errors())) {
            return response([
                'status' => 'failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $key=$request['key'];

        function encryptdata($data,$key){
            $encryption_key= base64_encode($key);
            $iv=openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
            $encrypted = openssl_encrypt($data,'aes-256-cbc',$encryption_key,0,$iv);
            return base64_encode($encrypted . '::' . $iv);
        }
        $offence = new Offence();

        $title=$request->title;
        $description=$request->description;
        storelog($title,$description,'MacOs');

        $offence->title = encryptdata($title,$key);
        $offence->description = encryptdata($description,$key);


        // Store the image path in the 'image' field
        $offence->save();
        storerandom_log($offence['id'],'45',$key);
        return response([
            'status'=>'success',
            'data'=>$offence,
        ]);
    }




    /**
     * Display the specified resource.
     */
    public function show(Offence $offence)
    {
        $key="123@guys";

        function dencryptdata($data,$key){
            $encryption_key= base64_encode($key);
            list($encrypted_data,$iv) =array_pad(explode('::',base64_decode($data),2),2,null);
            return openssl_decrypt($encrypted_data,'aes-256-cbc',$encryption_key,0,$iv);
        }

        $titles = Offence::select('id', 'title')->get();

// Decrypt the 'title' column for each record
        $decryptedTitles = $titles->map(function ($title) use ($key) {
            $title->title = dencryptdata($title->title, $key);
            return $title;
        });

        return response([
            'status' => 'success',
            'cases' => $decryptedTitles,
        ]);


    }

    /**
     * Show the form for editing the specified resource.
     */

    public function show_single(Request $request, $id, $kamt, $secret) {

        $secret_key=Secret_Key::find($kamt);
        $encrypted_key=$secret_key->key;


        function dencryptdata($data, $key_to_use) {
            $encryption_key = base64_encode($key_to_use);
            list($encrypted_data, $iv) = array_pad(explode('::', base64_decode($data), 2), 2, null);
            return openssl_decrypt($encrypted_data, 'aes-256-cbc', $encryption_key, 0, $iv);
        }

        $show_key = $encrypted_key;


        if ($show_key === $secret) {

            $cases = Court_case::find($id);

            // Assuming `dencryptdata` is your decryption function
            $description = dencryptdata($cases['description'], $show_key);
            $cases['description'] = $description;
            return response([
                'status' => 'success',
                'message' => "Data retrieved successfully",
                'data' => $cases,
            ]);
        }
        else{
            return response([
                'status' => 'failed',
                'message' => "Error Enter correct secret key",
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Offence $offence)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Offence $offence)
    {
        //
    }
}
