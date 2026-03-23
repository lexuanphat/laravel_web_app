<?php

namespace App\Http\Controllers;

use App\Http\Requests\VatRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DataTables;

class VatController extends Controller
{

    private const STATUS = [
        "nguyen" => "Thùng nguyên",
        "long_tron" => "Thùng đang long trộn",
        "keo_rut" => "Thùng đang kéo rút",
        "keo_ra_bon_chua" => "Thùng đang kéo ra bồn chứa (bán nước mắm thô)",
        "keo_ra_bon_chai" => "Thùng đang kéo ra bồn ra chai (đóng chai thành phẩm)",
        "ban_xac" => "Thùng chuẩn bị bán xác mắm",
        "danh_nuoc_muoi" => "Thùng đang đánh nước muối",
    ];
    private const FISH_STATUS = [
        'ca_dep' => "Cá đẹp",
        'ca_binh_thuong' => "Cá bình thường",
        'ca_xau' => "Cá xấu",
        'ca_cuc_xau' => "Cá cực xấu",
    ];

    public function index(){
        return view('admin.vat.index');
    }

    public function getData(Request $request) {
        $query_builder = DB::table('vats')
        ->leftJoin('users as user_create', 'vats.create_user_id', '=', 'user_create.id')
        ->leftJoin('users as user_update', 'vats.update_user_id', '=', 'user_update.id');

        $query_builder = $query_builder->selectRaw("
            vats.*,
            IFNULL(user_update.full_name, user_create.full_name) as user_full_name
        ");

        $datatables = DataTables::of($query_builder)
        ->addIndexColumn()
        ->addColumn('date', function($item){
            $date = $item->updated_at ?? $item->created_at;
            $date = date("d/m/Y H:i", strtotime($date));

            return "<div class=''>$date</div>";
        })
        ->editColumn('status', function($item){
           
            $status = self::STATUS[$item->status];

            return "<div class=''>$status</div>";
        })
        ->editColumn('fish_status', function($item){
           
            $fish_status = self::FISH_STATUS[$item->fish_status];

            return "<div class=''>$fish_status</div>";
        })
        ->addColumn(
            'action', function($item){
                $view_loading = view("admin._partials.loading");
                $action_edit = route('admin.vat.detail', ['id' => $item->id]);
                $action_delete = route('admin.vat.delete', ['id' => $item->id]);
                return "
                    <div class='button-list'>
                        <button class='btn btn-warning edit-record' data-action='{$action_edit}' data-record='{$item->id}'><i class='ri-edit-box-fill fs-5'></i>{$view_loading}</button>
                        <button class='btn btn-danger remove-record' data-action='{$action_delete}' data-record='{$item->id}'><i class='ri-delete-bin-fill fs-5'></i></button>
                    </div>
                ";
            }
        )
        ->rawColumns(['action', 'date', 'fish_status', 'status']);
        return $datatables->toJson();
    }

    public function store(VatRequest $request) {
        $data = $request->validated();
        $data['status'] = $data['status_vat'];
        unset($data['status_vat']);
        $data['create_user_id'] = auth()->id();
        $data['created_at'] = date("Y-m-d H:i:s");

        DB::table('vats')->insert($data);

        return $this->successResponse([], 'Thêm mới thành công');
    }

    public function detail($id, Request $request){
        $data = DB::table('vats')->where('id', $id)->first();

        if(!$data) {
            return $this->errorResponse('Không tìm thấy dữ liệu', 404);
        }
        
        $data->status_vat = $data->status;
        $data->max_capacity = $data->max_capacity * 1;
        $data->current_capacity = $data->current_capacity * 1;

        return $this->successResponse($data, 'Thành công');
    }

    public function update($id, VatRequest $request) {
        $data = DB::table('vats')->where('id', $id)->first();

        if(!$data) {
            return $this->errorResponse('Không tìm thấy dữ liệu', 404);
        }

        $data_update = $request->validated();
        $data_update['update_user_id'] = auth()->user()->id;
        $data_update['updated_at'] = date("Y-m-d H:i:s");
        $data_update['status'] = $data_update['status_vat'];
        unset($data_update['status_vat']);
        DB::table('vats')->where('id', $id)->update($data_update);

        return $this->successResponse($data, 'Cập nhật thành công');
    }

    public function delete($id){
        $data = DB::table('vats')->where('id', $id)->first();

        if(!$data) {
            return $this->errorResponse('Không tìm thấy dữ liệu', 404);
        }

        DB::table('vats')->delete($id);

        return $this->successResponse($data, 'Đã xoá dữ liệu');
    }
}
