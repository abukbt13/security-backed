<?php

use App\Models\Log;

function storelog($title, $details, $platform){
    Log::create([
        'title'=>$title,
        'details'=>$details,
        'platform'=>$platform
    ]);
}
