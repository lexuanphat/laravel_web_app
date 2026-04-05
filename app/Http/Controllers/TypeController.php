<?php

namespace App\Http\Controllers;

use App\Http\Requests\TypeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DataTables;

class TypeController extends Controller
{

    private const NAME_TYPE_REPORT = [
        "protein_level" => "Độ đạm",
        "salt_level" => "Nồng độ muối",
        "histamine_level" => "Histamin",
        "acid_level" => "Admin",
        "amon_level" => "Amon",
        "color" => "Màu sắc",
    ];
    public function index(){
        return view('admin.type.index');
    }

    public function getData(Request $request) {
        $query_builder = DB::table('list_type_report')
        ->leftJoin('users as user_create', 'list_type_report.create_user_id', '=', 'user_create.id')
        ->leftJoin('users as user_update', 'list_type_report.update_user_id', '=', 'user_update.id');

        $query_builder = $query_builder->selectRaw("
            list_type_report.*,
            IFNULL(user_update.full_name, user_create.full_name) as user_full_name
        ");

        $datatables = DataTables::of($query_builder)
        ->addIndexColumn()
        ->addColumn('date', function($item){
            $date = $item->updated_at ?? $item->created_at;
            $date = date("d/m/Y H:i", strtotime($date));

            return "<div class=''>$date</div>";
        })
        ->editColumn('type_report', function($item){
            return self::NAME_TYPE_REPORT[$item->type_report] ?? '';
        })
        ->addColumn(
            'action', function($item){
                $view_loading = view("admin._partials.loading");
                $action_edit = route('admin.type.detail', ['id' => $item->id]);
                $action_delete = route('admin.type.delete', ['id' => $item->id]);
                return "
                    <div class='button-list'>
                        <button class='btn btn-warning edit-record' data-action='{$action_edit}' data-record='{$item->id}'><i class='ri-edit-box-fill fs-5'></i>{$view_loading}</button>
                        <button class='btn btn-danger remove-record' data-action='{$action_delete}' data-record='{$item->id}'><i class='ri-delete-bin-fill fs-5'></i></button>
                    </div>
                ";
            }
        )
        ->rawColumns(['action', 'date']);
        return $datatables->toJson();
    }

    public function store(TypeRequest $request) {
        $data = $request->validated();
        $data['create_user_id'] = auth()->id();
        $data['created_at'] = date("Y-m-d H:i:s");

        DB::table('list_type_report')->insert($data);

        return $this->successResponse([], 'Thêm mới thành công');
    }

    public function detail($id, Request $request){
        $data = DB::table('list_type_report')->where('id', $id)->first();

        if(!$data) {
            return $this->errorResponse('Không tìm thấy dữ liệu', 404);
        }


        return $this->successResponse($data, 'Thành công');
    }

    public function update($id, TypeRequest $request) {
        $data = DB::table('list_type_report')->where('id', $id)->first();

        if(!$data) {
            return $this->errorResponse('Không tìm thấy dữ liệu', 404);
        }

        $data_update = $request->validated();
        $data_update['update_user_id'] = auth()->user()->id;
        $data_update['updated_at'] = date("Y-m-d H:i:s");
        DB::table('list_type_report')->where('id', $id)->update($data_update);

        return $this->successResponse($data, 'Cập nhật thành công');
    }

    public function delete($id){
        $data = DB::table('list_type_report')->where('id', $id)->first();

        if(!$data) {
            return $this->errorResponse('Không tìm thấy dữ liệu', 404);
        }

        DB::table('list_type_report')->delete($id);

        return $this->successResponse($data, 'Đã xoá dữ liệu');
    }
}
