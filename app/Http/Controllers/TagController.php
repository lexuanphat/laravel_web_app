<?php

namespace App\Http\Controllers;

use App\Http\Requests\TagValidateRequest;
use App\Models\Tag;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class TagController extends Controller
{
    //
    public function index() {
        // $tags = Category::get();
        return view('admin.tag.index');
    }

    public function getData(Request $request) {
        $search = isset($request->search) && !empty($request->search) ? $request->search : "";
        $search = ltrim($search, '?');
        parse_str($search, $parsed);

        $model = Tag::with('user:id,full_name')->orderBy('id', 'desc');

        if(isset($parsed['search'])) {
            $model->where("tag_name", "LIKE" , "%".trim($parsed['search'])."%");
        }

        $datatables = DataTables::eloquent($model)
        ->order(function($query){
            if(request()->has('order')) {
                $query->orderBy('tag_name', request()->order[0]['dir']);
            }
        })
        ->addIndexColumn()
        ->addColumn(
            'action', function($tag){
                $view_loading = view("admin._partials.loading");
                $action_edit = route('admin.tag.detail', ['id' => $tag->id]);
                $action_delete = route('admin.tag.delete', ['id' => $tag->id]);
                return "
                    <div class='button-list'>
                        <button class='btn btn-warning edit-record' data-action='{$action_edit}' data-record='{$tag->id}'><i class='ri-edit-box-fill fs-5'></i>{$view_loading}</button>
                        <button class='btn btn-danger remove-record' data-action='{$action_delete}' data-record='{$tag->id}'><i class='ri-delete-bin-fill fs-5'></i></button>
                    </div>
                ";
            }
        )
        ->addColumn('create_date', function($tag){
            $created_at = $tag->created_at ? date("d/m/Y H:i", strtotime($tag->created_at)) : 'X';
            return "$created_at";
        })
        ->addColumn('update_date', function($tag){
            $updated_at = $tag->updated_at ? date("d/m/Y H:i", strtotime($tag->updated_at)) : 'X';
            return "$updated_at";
        })
        ->editColumn('name', function($tag){
                return "$tag->name";
            }
        )
        ->rawColumns(['action', 'name', 'create_date', 'update_date']);
        return $datatables->toJson();
    }

    public function store(TagValidateRequest $request) {
        $validated = $request->validated();
        $validated['user_id'] = auth()->user()->id;
        $validated['created_at'] = date("Y-m-d H:i:s");
        $validated['updated_at'] = null;

        $data = Tag::create($validated);

        return $this->successResponse($data, 'Thêm mới thành công');
    }

    public function update($id, TagValidateRequest $request) {
        $data = Tag::find($id);

        if(!$data) {
            return $this->errorResponse('Không tìm thấy dữ liệu', 404);
        }

        $data_update = $request->validated();
        $data_update['user_id'] = auth()->user()->id;
        $data_update['updated_at'] = date("Y-m-d H:i:s");
        $data->update($data_update);

        return $this->successResponse($data, 'Cập nhật thành công');
    }

    public function detail($id, Request $request){
        $data = Tag::find($id, ['tag_name']);

        if(!$data) {
            return $this->errorResponse('Không tìm thấy dữ liệu', 404);
        }

        return $this->successResponse($data, 'Thành công');
    }

    public function delete($id) {
        $data = Tag::find($id);

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
