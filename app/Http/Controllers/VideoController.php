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
        $vid = $request->file('video');
        $vidName = time() . '_' .  $vid->getClientOriginalName();
        $vid->move(public_path('Evidences/Videos'), $vidName);
//        $vid->storeAs('Evidences/Videos', $vidName, 'public');

        $user_id=Auth::user()->id;

        $video = new Video();
        $video->description = $request->description;
        $video->video = $vidName;
        $video->case_id = $case_id;
        $video->user_id = $user_id;
        $video->save();

        return response([
            'status'=>'success',
            'message'=>'Video successfully saved ! ',
            'video'=>$video
        ]);
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
    public function show(Video $video,$case_id)
    {
        $user_id = Auth::user()->id;
        $video = Video::where('user_id', '=', $user_id)->where('case_id',$case_id)->get(); // Execute the query using get()

        return response([
            'status' => 'success',
            'videos' => $video,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Video $video)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Video $video)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Video $video)
    {
        //
    }
}
