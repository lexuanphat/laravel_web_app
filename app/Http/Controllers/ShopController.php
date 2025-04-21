<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShopValidateRequest;
use App\Models\Store;
use Illuminate\Http\Request;
use DataTables;

class ShopController extends Controller
{
    public function index(){
        return view('admin.shop.index');
    }

    public function getData(Request $request) {
        $model = Store::with('user:id,full_name')->orderBy('id', 'desc');
        $datatables = DataTables::eloquent($model)
        ->with('user:full_name')
        ->order(function($query){
            if(request()->has('order')) {
                $query->orderBy('name', request()->order[0]['dir']);
            }
        })
        ->addIndexColumn()
        ->addColumn(
            'action', function($store){
                $view_loading = view("admin._partials.loading");
                $action_edit = route('admin.shop.detail', ['id' => $store->id]);
                $action_delete = route('admin.shop.delete', ['id' => $store->id]);
                return "
                    <div class='button-list'>
                        <button class='btn btn-warning edit-record' data-action='{$action_edit}' data-record='{$store->id}'><i class='dripicons-document-edit'></i>{$view_loading}</button>
                        <button class='btn btn-danger remove-record' data-action='{$action_delete}' data-record='{$store->id}'><i class='dripicons-trash'></i></button>
                    </div>
                ";
            }
        )
        ->addColumn('date_action', function($store){
            $created_at = $store->created_at ? date("d/m/Y H:i", strtotime($store->created_at)) : 'X';
            $updated_at = $store->updated_at ? date("d/m/Y H:i", strtotime($store->updated_at)) : 'X';
            $user_action = $store->user->full_name;
            return "
                <div>{$created_at}</div>
                 <div>{$updated_at}</div>
                 <div>{$user_action}</div>
            ";
        })
        ->editColumn('name', function($store){
                return "
                    <div>{$store->name}</div>
                    <div><a href='tel:{$store->contact_phone}'>{$store->contact_phone}</a></div>
                ";
            }
        )
        ->rawColumns(['action', 'name', 'date_action']);
        return $datatables->toJson();
    }

    public function store(ShopValidateRequest $request) {
        $validated = $request->validated();
        $validated['user_id'] = auth()->user()->id;
        $validated['created_at'] = date("Y-m-d H:i:s");
        $validated['updated_at'] = null;

        Store::create($validated);

        return $this->successResponse([], 'Thêm mới thành công');
    }

    public function detail($id, Request $request){
        $data = Store::find($id, ['name', 'address', 'contact_phone']);

        if(!$data) {
            return $this->errorResponse('Không tìm thấy dữ liệu', 404);
        }

        return $this->successResponse($data, 'Thành công');
    }

    public function update($id, ShopValidateRequest $request) {
        $data = Store::find($id);

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
        $data = Store::find($id);

        if(!$data) {
            return $this->errorResponse('Không tìm thấy dữ liệu, vui lòng F5 thử lại', 404);
        }
        $data->delete();
        return $this->successResponse($data, 'Đã xoá dữ liệu');
    }
}
