<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function add(Request $request)
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
        $doc = $request->file('document');
        $docName = time() . '_' .  $doc->getClientOriginalName();
        $doc->storeAs('Evidences/Documents', $docName, 'public');

        $user_id=Auth::user()->id;

        $document = new Document();
        $document->description = $request->description;
        $document->document = $docName;
        $document->user_id = $user_id;
//        $picture->user_id = Auth::user()->id;
        $document->save();

        return response([
            'status'=>'Success',
            'message'=>'Success saved',
            'document'=>$docName
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Document $document)
    {
        $user_id = Auth::user()->id;
        $document = Document::where('user_id', '=', $user_id)->get(); // Execute the query using get()

        return response([
            'status' => 'Success',
            'documents' => $document,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Document $document)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Document $document)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Document $document)
    {
        //
    }
}
