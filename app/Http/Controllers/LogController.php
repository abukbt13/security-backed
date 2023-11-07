<?php

namespace App\Http\Controllers;

use App\Models\Log;
use Illuminate\Http\Request;

class LogController extends Controller
{

    /**
     * Display the specified resource.
     */
    public function show()
    {
        $log=Log::all();
        return response([
            'status'=>'success',
            'data'=>$log,
        ]);
    }


}
