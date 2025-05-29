<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransportValidateRequest;
use App\Models\Transport;
use Illuminate\Http\Request;
use DataTables;

class TransportController extends Controller
{
    public function index(){
        return view('admin.transport.index');
    }

    public function getData(Request $request) {
        $model = Transport::query()->orderBy('id', 'DESC');
        $datatables = DataTables::eloquent($model)
        ->addIndexColumn()
        ->addColumn(
            'action', function($transport){
                $view_loading = view("admin._partials.loading");
                $action_edit = route('admin.transport.detail', ['id' => $transport->id]);
                $action_delete = route('admin.transport.delete', ['id' => $transport->id]);
                return "
                    <div class='button-list'>
                        <button class='btn btn-warning edit-record' data-action='{$action_edit}' data-record='{$transport->id}'><i class='ri-edit-box-fill fs-5'></i>{$view_loading}</button>
                        <button class='btn btn-danger remove-record' data-action='{$action_delete}' data-record='{$transport->id}'><i class='ri-delete-bin-fill fs-5'></i></button>
                    </div>
                ";
            }
        )
        ->addColumn('date_action', function($transport){
            $created_at = $transport->created_at ? date("d/m/Y H:i", strtotime($transport->created_at)) : 'X';
            $updated_at = $transport->updated_at ? date("d/m/Y H:i", strtotime($transport->updated_at)) : 'X';
            $user_action = $transport->user->full_name;
            return "
                <div>{$created_at}</div>
                 <div>{$updated_at}</div>
                 <div>{$user_action}</div>
            ";
        })
        ->editColumn('full_name', function($transport){
                return "
                    <div>{$transport->full_name}</div>
                    <di class='text-warning'><a href='tel:{$transport->phone}'>{$transport->phone}</a></div>
                ";
            }
        )
        ->editColumn('role', function($transport){
            return "
                <div>".Transport::ROLE_RENDER_BLADE[$transport->role]."</div>
            ";
        })
        ->rawColumns(['action', 'full_name', 'role', 'date_action']);
        return $datatables->toJson();
    }

    public function store(TransportValidateRequest $request) {
        $validated = $request->validated();
        $validated['user_id'] = auth()->user()->id;
        $validated['created_at'] = date("Y-m-d H:i:s");
        $validated['updated_at'] = null;

        $data = Transport::create($validated);

        return $this->successResponse($data, 'Thêm mới thành công');
    }

    public function detail($id, Request $request){
        $data = Transport::find($id);

        if(!$data) {
            return $this->errorResponse('Không tìm thấy dữ liệu', 404);
        }

        return $this->successResponse($data, 'Thành công');
    }

    public function update($id, TransportValidateRequest $request) {
        $data = Transport::find($id);

        if(!$data) {
            return $this->errorResponse('Không tìm thấy dữ liệu', 404);
        }

        $data_update = $request->validated();
        $data_update['user_id'] = auth()->user()->id;
        $data_update['updated_at'] = date("Y-m-d H:i:s");
        $data->update($data_update);

        return $this->successResponse($data, 'Cập nhật thành công');
    }

    public function delete($id) {
        $data = Transport::find($id);

        if(!$data) {
            return $this->errorResponse('Không tìm thấy dữ liệu, vui lòng F5 thử lại', 404);
        }

        $data->delete();
        return $this->successResponse($data, 'Đã xoá dữ liệu');
    }
}
