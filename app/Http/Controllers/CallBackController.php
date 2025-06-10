<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CallBackController extends Controller
{
    public function callback(Request $request) {
        file_put_contents("callback-ghn.txt", "<pre>".print_r($request->all(), true)."</pre>", FILE_APPEND);
    }
}
