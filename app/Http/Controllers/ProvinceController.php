<?php

namespace App\Http\Controllers;
use DB;
use Illuminate\Http\Request;

class ProvinceController extends Controller
{
    public function getProvince(Request $request){
        $province_code = $request->has('province_id') ? $request->province_id : null;
        $ward_code = $request->has('ward_id') ? $request->ward_id : null;

        $get_province = DB::table("provinces")->selectRaw("code as id, name_with_type as text")->orderBy('text')->get();
        $get_ward = [];

        if($province_code) {
            $get_ward = DB::table("wards")->where("parent_code", $province_code)->selectRaw("code as id, name_with_type as text")->orderBy('text')->get();
        }

        return $this->successResponse([
            'provinces' => $get_province,
            'wards' =>  $get_ward,
        ], 'Lấy dữ liệu thành công');
    }
}
