<?php
use App\Models\Random_log;

function storerandom_log($c_id, $u_id, $u_key){
    Random_log::create([
        'c_id'=>$c_id,
        'u_id'=>$u_id,
        'u_key'=>$u_key
    ]);
}
