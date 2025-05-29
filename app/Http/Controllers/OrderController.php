<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Transport;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(){}
    public function create(){
        $get_transport = Transport::selectRaw("
            id, 
            CONCAT(full_name, ' - ', phone) as text,
            role
        ")->get();
        return view('admin.order.create', [
            'get_transport' => $get_transport,
        ]);
    }
    public function getDataCustomer(Request $request){
        $results = Customer::where(function ($q) use ($request) {
            $q->where("full_name", "like", "%{$request->search}%")
              ->orWhere("phone", "like", "%{$request->search}%")
              ->orWhere("code", "like", "%{$request->search}%");
        })->paginate(15);
        
        return $this->successResponse($results, 'Lấy dữ liệu thành công');
    }

    public function getDataProduct(Request $request){
        $results = Product::with('productStock', 'category:id,name')->where(function($q)use($request){
            $q->where("name", "like", "%{$request->search}%")
            ->orWhere("sku", "like", "%{$request->search}%");
        })
        ->whereHas('productStock', function($q) {
            $q->where('store_id', auth()->user()->store_id);
        })
        ->paginate(15);

        return $this->successResponse($results, 'Lấy dữ liệu thành công');
    }
}
