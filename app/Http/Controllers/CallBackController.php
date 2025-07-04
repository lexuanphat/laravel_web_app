<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CallBackController extends Controller
{
    public function callbackGhn(Request $request) {
        file_put_contents("callback-ghn.txt", "<pre>".print_r($request->all(), true)."</pre>", FILE_APPEND);
    }

    public function callbackGhtk(Request $request) {
        file_put_contents("callback-ghtk.txt", "<pre>".print_r($request->all(), true)."</pre>", FILE_APPEND);

        return $this->successResponse([], 'Ok');
    }
}
