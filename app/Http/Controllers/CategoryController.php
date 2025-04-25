<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryValidateRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use DataTables;

class CategoryController extends Controller
{
    public function index(){
        return view('admin.category.index');
    }

    public function getData(Request $request) {
        $model = Category::with('user:id,full_name')->orderBy('id', 'desc');
        $datatables = DataTables::eloquent($model)
        ->with('user:full_name')
        ->order(function($query){
            if(request()->has('order')) {
                $query->orderBy('name', request()->order[0]['dir']);
            }
        })
        ->addIndexColumn()
        ->addColumn(
            'action', function($category){
                $view_loading = view("admin._partials.loading");
                $action_edit = route('admin.category.detail', ['id' => $category->id]);
                $action_delete = route('admin.category.delete', ['id' => $category->id]);
                return "
                    <div class='button-list'>
                        <button class='btn btn-warning edit-record' data-action='{$action_edit}' data-record='{$category->id}'><i class='ri-edit-box-fill fs-5'></i>{$view_loading}</button>
                        <button class='btn btn-danger remove-record' data-action='{$action_delete}' data-record='{$category->id}'><i class='ri-delete-bin-fill fs-5'></i></button>
                    </div>
                ";
            }
        )
        ->addColumn('total_product', function($category){
            return "<div>0</div>";
        })
        ->addColumn('date_action', function($category){
            $created_at = $category->created_at ? date("d/m/Y H:i", strtotime($category->created_at)) : 'X';
            $updated_at = $category->updated_at ? date("d/m/Y H:i", strtotime($category->updated_at)) : 'X';
            $user_action = $category->user->full_name;
            return "
                <div>{$created_at}</div>
                 <div>{$updated_at}</div>
                 <div>{$user_action}</div>
            ";
        })
        ->editColumn('name', function($category){
                return "
                    <div>{$category->name}</div>
                    <di class='text-warning'>{$category->code}</a></div>
                ";
            }
        )
        ->rawColumns(['action', 'name', 'date_action', 'total_product']);
        return $datatables->toJson();
    }

    public function store(CategoryValidateRequest $request) {
        $validated = $request->validated();
        $validated['code'] = Category::generateCode();
        $validated['user_id'] = auth()->user()->id;
        $validated['created_at'] = date("Y-m-d H:i:s");
        $validated['updated_at'] = null;

        Category::create($validated);

        return $this->successResponse([], 'Thêm mới thành công');
    }

    public function detail($id, Request $request){
        $data = Category::find($id, ['name']);

        if(!$data) {
            return $this->errorResponse('Không tìm thấy dữ liệu', 404);
        }

        return $this->successResponse($data, 'Thành công');
    }

    public function update($id, CategoryValidateRequest $request) {
        $data = Category::find($id);

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
        $data = Category::find($id);

        if(!$data) {
            return $this->errorResponse('Không tìm thấy dữ liệu, vui lòng F5 thử lại', 404);
        }
        $data->delete();
        return $this->successResponse($data, 'Đã xoá dữ liệu');
    }
}
