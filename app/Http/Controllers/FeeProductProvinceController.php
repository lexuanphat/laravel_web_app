<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Store;
use DataTables;
use DB;
use App\Http\Requests\FeeProductProvinceRequest;

class FeeProductProvinceController extends Controller
{
    public function index(){
        $provinces = DB::table("provinces")->selectRaw("id, name as text")->orderBy("text")->get();
        $products = DB::table("products")->selectRaw("id, name as text")->orderBy('text')->get();

        return view('admin.fee_product_province.index', [
            'provinces' => $provinces,
            'products' => $products,
        ]);
    }

    // public function getDataProduct(Request $request) {
    //     $data = DB::table("products")->selectRaw("id, name as text")->orderBy('text')->get();

    //     return $this->successResponse($data, 'Lấy dữ liệu thành công');
    // }

    // public function getDataProvince(Request $request) {
    //     $data = DB::table("provinces")->selectRaw("id, name as text")->orderBy('text')->get();

    //     return $this->successResponse($data, 'Lấy dữ liệu thành công');
    // }

    public function detail($id, Request $request){
        $data = DB::table("fee_product_province")
        ->where("id", $id)->selectRaw("province_id, product_id, fee")->first();

        if(!$data) {
            return $this->errorResponse('Không tìm thấy dữ liệu', 404);
        }
        $data->fee = number_format($data->fee, 0, ",", ".");
        return $this->successResponse($data, 'Thành công');
    }

    public function update($id, FeeProductProvinceRequest $request) {
        $data = DB::table("fee_product_province")->where("id", $id);

        if(!$data->first()) {
            return $this->errorResponse('Không tìm thấy dữ liệu', 404);
        }

        $data_update = $request->validated();
        $data_update['fee'] = str_replace(".", "", $data_update['fee']);
        $data_update['updated_user_id'] = auth()->user()->id;
        $data_update['updated_at'] = date("Y-m-d H:i:s");
        $data->update($data_update);

        return $this->successResponse([], 'Cập nhật thành công');
    }

    public function delete($id) {
        $data =  DB::table("fee_product_province")->where("id", $id);

        if(!$data->first()) {
            return $this->errorResponse('Không tìm thấy dữ liệu, vui lòng F5 thử lại', 404);
        }

        $data->delete();
        return $this->successResponse([], 'Đã xoá dữ liệu');
    }

    public function getData(Request $request){
        $search = isset($request->search) && !empty($request->search) ? $request->search : "";
        $search = ltrim($search, '?');
        parse_str($search, $parsed);

        $model = DB::table("fee_product_province")
        ->leftJoin('provinces', 'fee_product_province.province_id', '=', 'provinces.id')
        ->leftJoin('products', 'fee_product_province.product_id', '=', 'products.id')
        ->leftJoin('users as created_user', 'fee_product_province.created_user_id', '=', 'created_user.id')
        ->leftJoin('users as updated_user', 'fee_product_province.updated_user_id', '=', 'updated_user.id')
        ->selectRaw("
            fee_product_province.id,
            products.name as product_name, provinces.name as province_name, fee_product_province.fee,
            DATE_FORMAT(IFNULL(fee_product_province.updated_at, fee_product_province.created_at), '%d/%m/%Y') as date_action,
            IFNULL(updated_user.full_name, created_user.full_name) as user_action

        ")
        ->orderByRaw("province_name ASC, product_name ASC");

        if(isset($parsed['search'])) {
            $model->where("products.name", "LIKE" , "%".trim($parsed['search'])."%");
        }

        if(isset($parsed['province_id'])) {
            $model->where("provinces.id", $parsed['search']);
        }

        $datatables = DataTables::of($model)
        ->addIndexColumn()
        ->addColumn(
            'action', function($item){
                $view_loading = view("admin._partials.loading");
                $action_edit = route('admin.fee_product_province.detail', ['id' => $item->id]);
                $action_delete = route('admin.fee_product_province.delete', ['id' => $item->id]);

                $button_delete = "<button class='btn btn-danger remove-record' data-action='{$action_delete}' data-record='{$item->id}'><i class='ri-delete-bin-fill fs-5'></i></button>";

                $button_edit = "<button class='btn btn-warning edit-record' data-action='{$action_edit}' data-record='{$item->id}'><i class='ri-edit-box-fill fs-5'></i>{$view_loading}</button>";
                

                return "
                    <div class='button-list'>
                        $button_edit
                        $button_delete
                    </div>
                ";
            }
        )
        
        
        ->editColumn('province_name', function($item){
            $province_name = $item->province_name;
                return "
                    <div>{$province_name}</div>
                ";
            }
        )
        ->editColumn('product_name', function($item){
            $product_name = $item->product_name;
                return "
                    <div>{$product_name}</div>
                ";
            }
        )
        ->editColumn('fee', function($item){
            $fee = number_format($item->fee, 0, ',', '.');
                return "
                    <div>{$fee}</div>
                ";
            }
        )
        ->editColumn('date_action', function($item){
            return "
                <div>{$item->date_action}</div>
            ";
        }
        )
        ->editColumn('user_action', function($item){
            return "
                <div>{$item->user_action}</div>
            ";
        })
        ->rawColumns(['product_name','province_name', 'fee', 'date_action', 'user_action', 'action']);
        
        return $datatables->toJson();
    }

    public function store(FeeProductProvinceRequest $request) {
        $validated = $request->validated();
        $validated['fee'] = str_replace(".", "", $validated['fee']);
        $validated['created_user_id'] = auth()->id();
        $validated['created_at'] = date("Y-m-d H:i:s");
        $validated['updated_at'] = null;
        DB::table("fee_product_province")->insert($validated);

        return $this->successResponse([], 'Thêm mới thành công');
    }
}
