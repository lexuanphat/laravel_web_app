<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use DataTables;

class ProductController extends Controller
{
    public function index(){
        return view('admin.product.index');
    }

    public function getData(Request $erquest){
        $model = Product::with('user:id,full_name', 'category:id,name')->orderBy('id', 'desc');
        $datatables = DataTables::eloquent($model)
        ->with('user:full_name')
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
                <div>{$created_at}</div>
                 <div>{$updated_at}</div>
                 <div>{$user_action}</div>
            ";
        })
        ->editColumn('name', function($product){
            return "
                <div>{$product->name}</div>
                <div class='text-warning'>{$product->code}</div>
                <div class='text-danger'>{$product->sku}</div>
            ";
        })
        ->rawColumns(['action', 'name', 'date_action']);
        return $datatables->toJson();
    }
}
