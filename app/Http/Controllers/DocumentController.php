<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DocumentController extends Controller
{

    public function add(Request $request,$case_id)
    {
        $rules=[
            'description' => 'required',
            'document' => 'required',
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

        $doc = $request->file('document');
        $docName = time() . '_' .  $doc->getClientOriginalName();
        $doc->move(public_path('Evidences/Documents'), $docName);
//        $doc->storeAs('Evidences/Videos', $docName, 'public');

        $user_id=Auth::user()->id;
        $document = new Document();
        $document->description = encryptdata($request->description,$secret);
        $document->document = encryptdata($docName,$secret);
        $document->case_id = $case_id;
        $document->user_id = $user_id;
        $document->save();

        return response([
            'status'=>'success',
            'message'=>'Video successfully saved ! ',
        ]);
    }

    public function show(Document $document,$case_id)
    {
        $secret ="@topsecurity@123secured";
        function dencryptdata($data, $key_to_use) {
            $encryption_key = base64_encode($key_to_use);
            list($encrypted_data, $iv) = array_pad(explode('::', base64_decode($data), 2), 2, null);
            return openssl_decrypt($encrypted_data, 'aes-256-cbc', $encryption_key, 0, $iv);
        }

        $user_id = Auth::user()->id;
        $count  = Document::count();
        if ($count > 0) {
            $document = Document::where('user_id', $user_id)->where('case_id',$case_id)->get();
            // Execute the query using get()
            foreach ($document as $doc) {
                $id= $doc->id;
                $document = dencryptdata($doc->document, $secret);
                $description = dencryptdata($doc->description, $secret);
                $decrypted_documents[] = [
                    'id' => $id,
                    'document' => $document,
                    'description' => $description,
                ];
            }
            return response([
                'status' => 'success',
                'documents' => $decrypted_documents,
            ]);
        }
        else
        {
            return response([
                'status' => 'failed',
            ]);
        }

    }
}
