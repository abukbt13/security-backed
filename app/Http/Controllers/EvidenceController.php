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
        $pic = $request->file('picture');
        $picName = time() . '_' .  $pic->getClientOriginalName();
        $pic->storeAs('Evidences/Pictures', $picName, 'public');



        $picture = new Evidence();
        $picture->description = $request->description;
        $picture->picture = $picName;
        $picture->case_id = $case_id;
        $picture->user_id = Auth::user()->id;
        $picture->save();

        return response([
            'status'=>'success',
            'message'=>'Picture saved successfully',
            'data'=>$picture
        ]);
    }
    public function show_all($case_id){
        $user_id = Auth::user()->id;
        $picture = Evidence::where('user_id', $user_id)->where('case_id',$case_id)
        ->get(); // Execute the query using get()

        return response([
            'status' => 'Success',
            'picture' => $picture,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */


    /**
     * Display the specified resource.
     */
    public function show(Evidence $evidence)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Evidence $evidence)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Evidence $evidence)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Evidence $evidence)
    {
        //
    }
}
