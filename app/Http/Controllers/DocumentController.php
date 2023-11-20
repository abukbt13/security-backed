<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
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
        $doc->move(public_path('Evidences/Documents'), $docName);



        $document = new Document();
        $document->description = $request->description;
        $document->document = $docName;
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
        $documents=Document::all();
        return response([
            'status' => 'Success',
            'message' => 'Success retrieved',
            'documents' => $documents
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
