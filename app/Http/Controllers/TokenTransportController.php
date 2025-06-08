<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TokenTransportController extends Controller
{
    public function index(Request $request){
        $data = DB::table("tokens")->whereIn("is_transport", ["GHN", "GHTK"])->get()->keyBy('is_transport');
        return view('admin.token_transport.index', [
            'data' => $data
        ]);
    }

    public function store(Request $request){
        DB::table("tokens")
        ->updateOrInsert(
            ['is_transport' => $request->is_transport],
            [
                '_token' => $request->token_transport,
                'api' => $request->api_transport,
                'user_id' => auth()->user()->id,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => null
            ]
        );

        return $this->successResponse([], 'Thêm mới thành công');
    }
}
