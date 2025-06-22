<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductValidateRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(){
        $categories = Category::get();
        return view('admin.product.index', [
            'categories' => $categories,
        ]);
    }

    public function getData(Request $request){
        $search = isset($request->search) && !empty($request->search) ? $request->search : "";
        $search = ltrim($search, '?');
        parse_str($search, $parsed);

        $model = Product::with('user:id,full_name', 'category:id,name')->orderBy('id', 'desc');

        if(isset($parsed['search']) && !empty(trim($parsed['search']))) {
            $model->where("products.name", "LIKE", "%".trim($parsed['search'])."%");
        }

        if(isset($parsed['category'])) {
           $model->where("products.category_id", trim($parsed['category']));
        }

        $datatables = DataTables::eloquent($model)
        ->order(function($query){
            if(request()->has('order')) {
                $query->orderBy('name', request()->order[0]['dir']);
            }
        })
        ->addIndexColumn()
        ->addColumn(
            'action', function($product){
                $view_loading = view("admin._partials.loading");
                $action_edit = route('admin.product.detail', ['id' => $product->id]);
                $action_delete = route('admin.product.delete', ['id' => $product->id]);
                return "
                    <div class='button-list'>
                        <button class='btn btn-warning edit-record' data-action='{$action_edit}' data-record='{$product->id}'><i class='ri-edit-box-fill'></i>{$view_loading}</button>
                        <button class='btn btn-danger remove-record' data-action='{$action_delete}' data-record='{$product->id}'><i class='ri-delete-bin-fill'></i></button>
                    </div>
                ";
            }
        )
        ->addColumn('date_action', function($product){
            $created_at = $product->created_at ? date("d/m/Y H:i", strtotime($product->created_at)) : 'X';
            $updated_at = $product->updated_at ? date("d/m/Y H:i", strtotime($product->updated_at)) : 'X';
            $user_action = $product->user->full_name;
            return "
                <div class='text-body'>{$created_at}</div>
                 <div class='text-body'>{$updated_at}</div>
                 <div class='text-body'>{$user_action}</div>
            ";
        })
        ->addColumn('data_col_3', function($product){
            $price = number_format($product->price);
            return "
                <div>
                    <p class='mb-0'>{$product->category->name}</p>
                    <p class='mb-0'>X</p>
                    <p class='mb-0'>{$price}</p>
                </div>
            ";
        })
        ->editColumn('name', function($product){
            $image_prod = asset("storage/{$product->image_url}");
            return "
                <img src='{$image_prod}' loading='lazy' decoding='async' alt='contact-img' title='contact-img' class='rounded me-3' height='48' />
                <div class='m-0 d-inline-block align-middle font-16'>
                    <p class='text-body m-0'>{$product->name}</p>
                    <p class='text-warning m-0'>{$product->code}</p>
                    <p class='text-danger m-0'>{$product->sku}</p>
                </div>
            ";
        })
        ->rawColumns(['action', 'name', 'date_action', 'data_col_3']);
        return $datatables->toJson();
    }

    public function getDataCategory(Request $request){
        if(!isset($request->search) || empty($request->search)) {
            return $this->successResponse([], 'Không có dữ liệu nào');
        }

        $data = Category::where('name', 'like', '%'.$request->search.'%')
        ->select("id", "name")
        ->get();

        return $this->successResponse($data, 'Lấy dữ liệu thành công', ['exists' => $data->isNotEmpty()]);
    }

    public function store(ProductValidateRequest $request) {
        $validated = $request->validated();

        if(isset($validated['image_url'])) {
            $validated['image_url'] = $validated['image_url']->store('products', 'public');
        }

        $validated['code'] = Product::generateCode();
        $validated['user_id'] = auth()->user()->id;
        $validated['created_at'] = date("Y-m-d H:i:s");
        $validated['updated_at'] = null;
        Product::create($validated);

        return $this->successResponse([], 'Thêm mới thành công');
    }

    public function detail($id, Request $request){
        $data = Product::with('category:id,name')->find($id);
        $data->price = number_format($data->price, 0, ',', '.');

        if(!$data) {
            return $this->errorResponse('Không tìm thấy dữ liệu', 404);
        }

        return $this->successResponse($data, 'Thành công');
    }

    public function update($id, ProductValidateRequest $request) {
        // dd($request->all(), $request->file('image_url'));
        $data = Product::find($id);

        if(!$data) {
            return $this->errorResponse('Không tìm thấy dữ liệu, vui lòng F5 thử lại', 404);
        }

        $validated = $request->validated();

        if(isset($validated['image_url'])) {
            $validated['image_url'] = $validated['image_url']->store('products', 'public');
        }

        if($data->image_url !== $request->current_image) {
            Storage::disk('public')->delete($data->image_url);
            $validated['image_url'] = null;
        }

        $validated['code'] = Product::generateCode();
        $validated['user_id'] = auth()->user()->id;
        $validated['created_at'] = date("Y-m-d H:i:s");
        $validated['updated_at'] = null;

        $data->update($validated);

        return $this->successResponse([], 'Cập nhật thành công');
    }

    public function delete($id) {
        $data = Product::find($id);

        if(!$data) {
            return $this->errorResponse('Không tìm thấy dữ liệu, vui lòng F5 thử lại', 404);
        }
        $data->delete();
        return $this->successResponse($data, 'Đã xoá dữ liệu');
    }
}
