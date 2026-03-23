<?php

namespace App\Http\Controllers;

use App\Http\Requests\TankRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DataTables;

class TankController extends Controller
{
    private const TYPE = [
        'thanh_pham' => 'THÀNH PHẨM',
        'ra_chai' => 'RA CHAI',
        'nhua' => 'NHỰA',
    ];
    public function index(){
        return view('admin.tank.index');
    }

    public function getData(Request $request) {
        $query_builder = DB::table('tanks')
        ->leftJoin('users as user_create', 'tanks.create_user_id', '=', 'user_create.id')
        ->leftJoin('users as user_update', 'tanks.update_user_id', '=', 'user_update.id');

        $query_builder = $query_builder->selectRaw("
            tanks.*,
            IFNULL(user_update.full_name, user_create.full_name) as user_full_name
        ");

        $datatables = DataTables::of($query_builder)
        ->addIndexColumn()
        ->addColumn('date', function($item){
            $date = $item->updated_at ?? $item->created_at;
            $date = date("d/m/Y H:i", strtotime($date));

            return "<div class=''>$date</div>";
        })
        ->editColumn('type', function($item){
           
            $type = self::TYPE[$item->type];

            return "<div class=''>$type</div>";
        })
        ->addColumn(
            'action', function($item){
                $view_loading = view("admin._partials.loading");
                $action_edit = route('admin.tank.detail', ['id' => $item->id]);
                $action_delete = route('admin.tank.delete', ['id' => $item->id]);
                return "
                    <div class='button-list'>
                        <button class='btn btn-warning edit-record' data-action='{$action_edit}' data-record='{$item->id}'><i class='ri-edit-box-fill fs-5'></i>{$view_loading}</button>
                        <button class='btn btn-danger remove-record' data-action='{$action_delete}' data-record='{$item->id}'><i class='ri-delete-bin-fill fs-5'></i></button>
                    </div>
                ";
            }
        )
        ->rawColumns(['action', 'date', 'type']);
        return $datatables->toJson();
    }

    public function store(TankRequest $request) {
        $data = $request->validated();
        $data['create_user_id'] = auth()->id();
        $data['created_at'] = date("Y-m-d H:i:s");

        DB::table('tanks')->insert($data);

        return $this->successResponse([], 'Thêm mới thành công');
    }

    public function detail($id, Request $request){
        $data = DB::table('tanks')->where('id', $id)->first();

        if(!$data) {
            return $this->errorResponse('Không tìm thấy dữ liệu', 404);
        }
        
        $data->max_capacity = $data->max_capacity * 1;
        $data->current_capacity = $data->current_capacity * 1;

        return $this->successResponse($data, 'Thành công');
    }

    public function update($id, TankRequest $request) {
        $data = DB::table('tanks')->where('id', $id)->first();

        if(!$data) {
            return $this->errorResponse('Không tìm thấy dữ liệu', 404);
        }

        $data_update = $request->validated();
        $data_update['update_user_id'] = auth()->user()->id;
        $data_update['updated_at'] = date("Y-m-d H:i:s");
        DB::table('tanks')->where('id', $id)->update($data_update);

        return $this->successResponse($data, 'Cập nhật thành công');
    }

    public function delete($id){
        $data = DB::table('tanks')->where('id', $id)->first();

        if(!$data) {
            return $this->errorResponse('Không tìm thấy dữ liệu', 404);
        }

        DB::table('tanks')->delete($id);

        return $this->successResponse($data, 'Đã xoá dữ liệu');
    }
}
