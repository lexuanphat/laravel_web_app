<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DataTables;
use DB;
use App\Models\ShippingFee;
use App\Http\Requests\ShippingFeeRequest;

class ShippingFeeController extends Controller
{
    private function getProvince($ignore_id = null){
        $get_province = DB::table("provinces")
        ->selectRaw("id, name as text")
        ->whereNotIn("id", function($query){
            $query->selectRaw('province_id')
            ->from('shipping_fees')
            ->get();
        });

        if($ignore_id) {
            $get_province->unionAll(DB::table("provinces")->where("id", $ignore_id)->selectRaw("id, name as text"));
        }

        return $get_province->orderBy('text')->get();
    }

    public function detail($id){
        $data = ShippingFee::find($id, ['province_id', 'fee']);
        $data->fee = number_format($data->fee, 0, ',', '.');

        if(!$data) {
            return $this->errorResponse('Không tìm thấy dữ liệu', 404);
        }

        $data['provinces'] = $this->getProvince($data->province_id);

        return $this->successResponse($data, 'Thành công');
    }

    public function index(){
        $get_province = $this->getProvince();

        return view('admin.shipping_fee.index', [
            'get_province' => $get_province,
        ]);
    }

    public function store(ShippingFeeRequest $request) {
        $validated = $request->validated();
        $validated['fee'] = str_replace(".", "", $validated['fee']);
        $validated['created_user_id'] = auth()->id();
        $validated['created_at'] = date("Y-m-d H:i:s");
        $validated['updated_at'] = null;
        ShippingFee::create($validated);

        return $this->successResponse([
            'provinces' => $this->getProvince(),
        ], 'Thêm phí vận chuyển thành công');
    }

    public function update($id, ShippingFeeRequest $request) {
        $data = ShippingFee::find($id);

        if(!$data) {
            return $this->errorResponse('Không tìm thấy dữ liệu', 404);
        }

        $data_update = $request->validated();
        $data_update['fee'] = str_replace(".", "", $data_update['fee']);
        $data_update['user_id'] = auth()->user()->id;
        $data_update['updated_at'] = date("Y-m-d H:i:s");
        $data->update($data_update);

        return $this->successResponse([
            'provinces' => $this->getProvince(),
        ], 'Cập nhật thành công');
    }

    public function delete($id) {
        $data = ShippingFee::find($id);

        if(!$data) {
            return $this->errorResponse('Không tìm thấy dữ liệu, vui lòng F5 thử lại', 404);
        }

        if($data->province_id === -1) {
            return $this->errorResponse('Dữ liệu mặc định của hệ thống, không thể xoá', 404);
        }

        $data->delete();
        return $this->successResponse([
            'provinces' => $this->getProvince(),
        ], 'Đã xoá dữ liệu');
    }

    public function getData(){
        $model = ShippingFee::leftJoin('provinces', 'shipping_fees.province_id', '=', 'provinces.id')
        ->leftJoin('users', 'shipping_fees.created_user_id', '=', 'users.id')
        ->selectRaw("shipping_fees.*, provinces.name as province_name, users.full_name as user_full_name, IF(shipping_fees.province_id = -1, 1, 2) as custom_order")
        ->orderBy('custom_order', 'asc')
        ->orderBy('provinces.name', 'asc');

        $datatables = DataTables::eloquent($model)
        ->addIndexColumn()
        ->addColumn(
            'action', function($shipping_fee){
                $view_loading = view("admin._partials.loading");
                $action_edit = route('admin.shipping_fee.detail', ['id' => $shipping_fee->id]);
                $action_delete = route('admin.shipping_fee.delete', ['id' => $shipping_fee->id]);

                $button_delete = "<button class='btn btn-danger remove-record' data-action='{$action_delete}' data-record='{$shipping_fee->id}'><i class='ri-delete-bin-fill fs-5'></i></button>";

                $id_is_default = "";
                if($shipping_fee->province_id === -1) {
                    // $button_delete = "";
                    $id_is_default = "id='record_default'";
                }
                $button_edit = "<button $id_is_default class='btn btn-warning edit-record' data-action='{$action_edit}' data-record='{$shipping_fee->id}'><i class='ri-edit-box-fill fs-5'></i>{$view_loading}</button>";
                

                return "
                    <div class='button-list'>
                        $button_edit
                        $button_delete
                    </div>
                ";
            }
        )
        
        
        ->addColumn('province', function($shipping_fee){
            $province_name = $shipping_fee->province_name;

            if($shipping_fee->province_id === -1) {
                $province_name = 'MẶC ĐỊNH';
            }

                return "
                    <div>{$province_name}</div>
                ";
            }
        )
        ->editColumn('fee', function($shipping_fee){
            $fee = number_format($shipping_fee->fee, 0, ',', '.');
                return "
                    <div>{$fee}</div>
                ";
            }
        )
        ->addColumn('user_full_name', function($shipping_fee){
            return "
                <div>{$shipping_fee->user_full_name}</div>
            ";
        }
    )
        ->rawColumns(['province', 'fee', 'action', 'user_full_name']);
        return $datatables->toJson();
    }
}
