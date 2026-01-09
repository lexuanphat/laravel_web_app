<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerValidateRequest;
use App\Models\Customer;
use App\Models\Tag;
use Illuminate\Http\Request;
use DataTables;
use DB;

class CustomerController extends Controller
{
    public function index(){
        $provinces = DB::table("provinces")->selectRaw("code as id, name")->get();
        return view('admin.customer.index-new', [
            'provinces' => $provinces,
        ]);
    }

    public function getData(Request $request) {
        $search = isset($request->search) && !empty($request->search) ? $request->search : "";
        $search = ltrim($search, '?');
        parse_str($search, $parsed);

        $model = Customer::with('user:id,full_name')
        ->leftJoin('orders', 'customers.id', '=', 'orders.customer_id')
        ->groupBy([
            'customers.id',
            'customers.code',
            'customers.full_name',
        ])
        ->selectRaw("
            customers.*, COUNT(orders.id) as total_order,
            SUM(orders.total_amount) as sum_total_amount,
            SUM(orders.paid_amount) as sum_paid_amount  
        ")
        ->orderBy('id', 'desc');

        if(isset($parsed['search'])) {
            $model->where("full_name", "LIKE" , "%".trim($parsed['search'])."%");
        }
        if(isset($parsed['phone'])) {
            $model->where("phone", "LIKE" , "%".trim($parsed['phone'])."%");
        }

        if(isset($parsed['province_id'])) {
            $model->where("province_code", trim($parsed['province_id']));
        }

        $datatables = DataTables::eloquent($model)
        ->addIndexColumn()
        ->addColumn(
            'action', function($customer){
                $view_loading = view("admin._partials.loading");
                $action_edit = route('admin.customer.detail', ['id' => $customer->id]);
                $action_delete = route('admin.customer.delete', ['id' => $customer->id]);
                $action_detail = route('admin.customer.detail_view', ['id' => $customer->id]);
                return "
                    <div class='d-flex flex-wrap gap-1'>
                        <button class='btn btn-warning edit-record' data-action='{$action_edit}' data-record='{$customer->id}'><i class='ri-edit-box-fill'></i>{$view_loading}</button>
                        <button class='btn btn-danger remove-record' data-action='{$action_delete}' data-record='{$customer->id}'><i class='ri-delete-bin-fill'></i></button>
                        <a class='btn btn-primary' href='$action_detail'><i class='ri-eye-line'></i></a>
                    </div>
                ";
            }
        )
        ->addColumn('date_action', function($customer){
            $date_action = $customer->updated_at ? date("d/m/Y", strtotime($customer->updated_at)) : date("d/m/Y", strtotime($customer->created_at));

            return "
                <div class='text-body'>{$date_action}</div>
            ";
        })
        ->editColumn('phone', function($customer){
            $phone = $customer->phone;
            

            return "
            <div><a href='tel:{$phone}'>{$phone}</a></div>
             
        ";
        })
        ->editColumn('full_name', function($customer){
                return "
                    <div>{$customer->full_name} <span class='badge badge-{$customer->getColorGender($customer->gender)}'>{$customer->getGender($customer->gender)}</span></div>
                ";
            }
        )
        ->editColumn('total_order', function($customer){
                $content = number_format($customer->total_order);
                return "
                    <div class='text-end'>$content</div>
                ";
            }
        )
        ->editColumn('no_phai_thu', function($customer){
                $content = $customer->sum_total_amount - $customer->sum_paid_amount;

                if($content > 0) {
                    $content = number_format($content);
                }else {
                    $content = 0;
                }

                return "
                    <div>$content đ</div>
                ";
            }
        )
        ->editColumn('tong_chi_tieu', function($customer){
            $content = number_format($customer->sum_paid_amount);
            return "
                <div>$content đ</div>
            ";
        }
        )
        ->rawColumns(['action', 'full_name', 'date_action', 'phone', 'total_order', 'no_phai_thu', 'tong_chi_tieu']);
        return $datatables->toJson();
    }

    public function detailView(Request $request, $id) {
        $find_data = Customer::where('id', $id)
        ->first();

        $find_data->list_order = DB::table('orders')
        ->where('customer_id', $id)
        ->get();

        $find_data->tags_customer = DB::table('product_tag_details')
        ->join('tags', 'product_tag_details.tag_id', '=', 'tags.id')
        ->where('product_tag_details.product_id', $id)
        ->where('tags.type', Tag::TAG_IS['CUSTOMER'])
        ->selectRaw('product_tag_details.tag_id')
        ->get()->pluck('tag_id')->toArray();


        $get_customer_tags = DB::table('tags')
        ->where('type', Tag::TAG_IS['CUSTOMER'])
        ->selectRaw('id, tag_name')
        ->get();

        $get_customer_join = Customer::getJoinedCustomer($find_data->created_at);

        return view('admin.customer.detail_view', [
            'find_data' => $find_data,
            'get_customer_tags' => $get_customer_tags,
            'get_customer_join' => $get_customer_join,
        ]);
    }

    public function store(CustomerValidateRequest $request){
        $validated = $request->validated();
        $validated['user_id'] = auth()->user()->id;
        $validated['code'] = Customer::generateCodeCustomer();
        $validated['created_at'] = date("Y-m-d H:i:s");
        $validated['updated_at'] = null;

        $data = Customer::create($validated);
        return $this->successResponse($data, 'Thêm mới thành công');
    }

    public function detail($id, Request $request){
        $data = Customer::find($id, ['id', 'province_code', 'full_name', 'email', 'phone', 'gender', 'date_of_birth', 'address', 'ward_code']);

        if(!$data) {
            return $this->errorResponse('Không tìm thấy dữ liệu', 404);
        }

        return $this->successResponse($data, 'Thành công');
    }

    public function update($id, CustomerValidateRequest $request) {
        $data = Customer::find($id);

        if(!$data) {
            return $this->errorResponse('Không tìm thấy dữ liệu', 404);
        }

        $data_update = $request->validated();
        $data_update['user_id'] = auth()->user()->id;
        $data_update['updated_at'] = date("Y-m-d H:i:s");

        DB::beginTransaction();
        try {
            if(isset($data_update['tags'])){
                $insert_tag = [];

                DB::table('product_tag_details')
                ->join('tags', 'product_tag_details.tag_id', '=', 'tags.id')
                ->where('product_tag_details.product_id', $id)
                ->where('tags.type', Tag::TAG_IS['CUSTOMER'])
                ->delete();

                foreach($data_update['tags'] as $tag_id) {
                    $insert_tag[] = [
                        'product_id' => $id,
                        'tag_id' => $tag_id,
                    ];
                }
                DB::table('product_tag_details')->insert($insert_tag);
            }
    
            $data->update($data_update);
            DB::commit(); 
            return $this->successResponse($data, 'Cập nhật thành công');
        } catch (\Throwable $th) {
            throw $th;
            DB::rollBack();
        }
    }

    public function delete($id) {
        $data = Customer::find($id);

        if(!$data) {
            return $this->errorResponse('Không tìm thấy dữ liệu, vui lòng F5 thử lại', 404);
        }
        $data->delete();
        return $this->successResponse($data, 'Đã xoá dữ liệu');
    }
}
