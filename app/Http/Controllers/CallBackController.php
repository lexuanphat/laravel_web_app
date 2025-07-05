<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CallBackController extends Controller
{
    public function callbackGhn(Request $request) {
        Log::info('Webhook GHN nhận được:', $request->all());

        return $this->successResponse([], 'Ok');
    }

    public function callbackGhtk(Request $request) {
        Log::info('Webhook GHTK nhận được:', $request->all());

        return $this->successResponse([], 'Ok');
    }
}
