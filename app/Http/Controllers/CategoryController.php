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
        $search = isset($request->search) && !empty($request->search) ? $request->search : "";
        $search = ltrim($search, '?');
        parse_str($search, $parsed);

        $model = Category::with('user:id,full_name')->withCount('products')->orderBy('id', 'desc');

        if(isset($parsed['search'])) {
            $model->where("name", "LIKE" , "%".trim($parsed['search'])."%");
        }

        $datatables = DataTables::eloquent($model)
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
        ->addColumn('products_count', function($category){
            return "<span class='badge bg-dark text-light'>Tổng: {$category->products_count}</span>";
        })
        ->addColumn('date_action', function($category){
            $date_action = $category->updated_at ? date("d/m/Y", strtotime($category->updated_at)) : date("d/m/Y", strtotime($category->created_at));

            return "
                <div class='text-body'>{$date_action}</div>
            ";
        })
        ->editColumn('name', function($category){
                return "
                    <div>{$category->name}</div>
                ";
            }
        )
        ->rawColumns(['action', 'name', 'date_action', 'products_count']);
        return $datatables->toJson();
    }

    public function store(CategoryValidateRequest $request) {
        $validated = $request->validated();
        $validated['code'] = Category::generateCode();
        $validated['user_id'] = auth()->user()->id;
        $validated['created_at'] = date("Y-m-d H:i:s");
        $validated['updated_at'] = null;

        $data = Category::create($validated);

        return $this->successResponse($data, 'Thêm mới thành công');
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
        $data = Category::withCount('products')->find($id);

        if(!$data) {
            return $this->errorResponse('Không tìm thấy dữ liệu, vui lòng F5 thử lại', 404);
        }

        if($data->products_count > 0) {
            return $this->errorResponse('Danh mục này đang tồn tại nên sản phẩm không thể xoá', 404);
        }

        $data->delete();
        return $this->successResponse($data, 'Đã xoá dữ liệu');
    }
}
