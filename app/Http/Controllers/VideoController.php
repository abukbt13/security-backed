<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class VideoController extends Controller
{

    public function add(Request $request,$case_id)
    {
        $rules=[
            'description' => 'required',
            'video' => 'required',
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

        $vid = $request->file('video');
        $vidName = time() . '_' .  $vid->getClientOriginalName();
        $vid->move(public_path('Evidences/Videos'), $vidName);
//        $vid->storeAs('Evidences/Videos', $vidName, 'public');

        $user_id=Auth::user()->id;
        $video = new Video();
        $video->description = encryptdata($request->description,$secret);
        $video->video = encryptdata($vidName,$secret);
        $video->case_id = encryptdata($case_id,$secret);
        $video->user_id = encryptdata($user_id,$secret);
        $video->save();

        return response([
            'status'=>'success',
            'message'=>'Video successfully saved ! ',
            'video'=>$video
        ]);
    }

    public function show(Video $video,$case_id)
    {
        $secret ="@topsecurity@123secured";
        function dencryptdata($data, $key_to_use) {
            $encryption_key = base64_encode($key_to_use);
            list($encrypted_data, $iv) = array_pad(explode('::', base64_decode($data), 2), 2, null);
            return openssl_decrypt($encrypted_data, 'aes-256-cbc', $encryption_key, 0, $iv);
        }

        $user_id = Auth::user()->id;
        $video = Video::where('user_id', dencryptdata($user_id,$secret))->where('case_id',dencryptdata($case_id,$secret))->get();
        // Execute the query using get()
        foreach ($video as $vid) {
            $id= $vid->id;
            $picture= dencryptdata($vid->video, $secret);
            $description = dencryptdata($vid->description, $secret);
            $decrypted_videos[] = [
                'id' => $id,
                'video' => $picture,
                'description' => $description,
            ];
        }
        return response([
            'status' => 'success',
            'videos' => $decrypted_videos,
        ]);
    }

}
