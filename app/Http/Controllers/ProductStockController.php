<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductStockValidateRequest;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\Store;
use Illuminate\Http\Request;
use DataTables;

class ProductStockController extends Controller
{
    public function index(){
        $store = Store::get();
        return view('admin.product_stock.index', [
            'store' => $store,
        ]);
    }

    public function getData(Request $request){
        $model = ProductStock::with('user:id,full_name', 'product', 'store');
        $datatables = DataTables::eloquent($model)
        ->addIndexColumn()
        ->addColumn(
            'action', function($product_stock){
                $action_delete = route('admin.product_stock.delete', ['id' => $product_stock->id]);
                return "
                    <div class='button-list'>
                        <button class='btn btn-danger remove-record' data-action='{$action_delete}' data-record='{$product_stock->id}'><i class='ri-delete-bin-fill'></i></button>
                    </div>
                ";
            }
        )
        ->addColumn('date_action', function($product_stock){
            $created_at = $product_stock->created_at ? date("d/m/Y H:i", strtotime($product_stock->created_at)) : 'X';
            $updated_at = $product_stock->updated_at ? date("d/m/Y H:i", strtotime($product_stock->updated_at)) : 'X';
            $user_action = $product_stock->user->full_name;
            return "
                <div class='text-body'>{$created_at}</div>
                 <div class='text-body'>{$updated_at}</div>
                 <div class='text-body'>{$user_action}</div>
            ";
        })
        ->addColumn('data_col_2', function($product_stock){
            $image_prod = asset("storage/{$product_stock->product->image_url}");
            return "
                <img src='{$image_prod}' loading='lazy' decoding='async' alt='contact-img' title='contact-img' class='rounded me-3' height='48' />
                <div class='m-0 d-inline-block align-middle font-16'>
                    <p class='text-body m-0'>{$product_stock->product->name}</p>
                    <p class='text-warning m-0'>{$product_stock->product->code}</p>
                    <p class='text-danger m-0'>{$product_stock->product->sku}</p>
                </div>
            ";
        })
        ->addColumn('data_col_3', function($product_stock){
            $stock_price = number_format($product_stock->stock_price);
            return "
                <div class='text-end'>{$product_stock->stock_quantity}</div>
                <div class='text-end'>{$product_stock->available_quantity}</div>
                <div class='text-end'>{$stock_price}</div>
            ";
        })
        ->rawColumns(['action', 'date_action', 'data_col_2', 'data_col_3']);
        return $datatables->toJson();
    }

    public function getDataProduct(Request $request){
        $data = Product::where("name", "like", "%{$request->search}%")->paginate(20);

        return $this->successResponse($data, 'Lấy dữ liệu thành công');
    }

    public function store(ProductStockValidateRequest $request) {
        $data = $request->validated();
        $data['user_id'] = auth()->user()->id;
        $data['created_at'] = date("Y-m-d H:i:s");
        $data['updated_at'] = null;

        ProductStock::create($data);

        return $this->successResponse([], 'Nhập kho thành công');
    }

    public function delete($id) {
        $data = ProductStock::find($id);

        if(!$data) {
            return $this->errorResponse('Không tìm thấy dữ liệu, vui lòng F5 thử lại', 404);
        }
        $data->delete();
        return $this->successResponse($data, 'Đã xoá dữ liệu');
    }
}
