<?php

namespace App\Http\Controllers;

use App\Models\Evidence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class EvidenceController extends Controller
{

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request,$case_id){
        $rules=[
            'description' => 'required',
            'picture' => 'required',
        ];
        $data=request()->all();
        $valid=Validator::make($data,$rules);
        if(count($valid->errors())){
            return response([
                'status'=>'failed',
                'message'=>$valid->errors()
            ]);
        }

        function encryptdata($data,$key){
            $encryption_key= base64_encode($key);
            $iv=openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
            $encrypted = openssl_encrypt($data,'aes-256-cbc',$encryption_key,0,$iv);
            return base64_encode($encrypted . '::' . $iv);
        }

        $user_id = Auth::user()->id;
        $secret ="@topsecurity@123secured";
        $pic = $request->file('picture');
        $picName = time() . '_' .  $pic->getClientOriginalName();

        $pic->move(public_path('Evidences/Pictures'), $picName);
//        $pic->storeAs('Evidences/Pictures', $picName, 'public');

        $picture = new Evidence();
        $picture->description = encryptdata($request->description,$secret);
        $picture->picture = encryptdata($picName,$secret);
        $picture->case_id = $case_id;
        $picture->user_id = $user_id;
        $picture->save();

        return response([
            'status'=>'success',
            'message'=>'Picture saved successfully',
            'data'=>$picture
        ]);
    }
    public function show_all($case_id)
    {
        $secret = "@topsecurity@123secured";
        function dencryptdata($data, $key_to_use)
        {
            $encryption_key = base64_encode($key_to_use);
            list($encrypted_data, $iv) = array_pad(explode('::', base64_decode($data), 2), 2, null);
            return openssl_decrypt($encrypted_data, 'aes-256-cbc', $encryption_key, 0, $iv);
        }

        $user_id = Auth::user()->id;
        $count = Evidence::where('user_id',$user_id)->where('case_id',$case_id)->count();
        if ($count > 0) {
            $picture = Evidence::where('user_id', $user_id)->where('case_id', $case_id)->get(); // Execute the query using get()
            foreach ($picture as $case) {
                $id = $case->id;
                $picture = dencryptdata($case->picture, $secret);
                $description = dencryptdata($case->description, $secret);
                $decrypted_pictures[] = [
                    'id' => $id,
                    'picture' => $picture,
                    'description' => $description,
                ];
            }

            return response([
                'status' => 'Success',
                'picture' => $decrypted_pictures,
            ]);
        }
        else{
            return response([
                'status' => 'failed',
                'message' => "No picture available",
            ]);
        }
    }

}
