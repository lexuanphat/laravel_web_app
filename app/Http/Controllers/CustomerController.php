<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerValidateRequest;
use App\Models\Customer;
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

        $model = Customer::with('user:id,full_name')->orderBy('id', 'desc');

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
        ->with('user:full_name')
        ->order(function($query){
            if(request()->has('order')) {
                $query->orderBy('full_name', request()->order[0]['dir']);
            }
        })
        ->addIndexColumn()
        ->addColumn(
            'action', function($customer){
                $view_loading = view("admin._partials.loading");
                $action_edit = route('admin.customer.detail', ['id' => $customer->id]);
                $action_delete = route('admin.customer.delete', ['id' => $customer->id]);
                return "
                    <div class='button-list'>
                        <button class='btn btn-warning edit-record' data-action='{$action_edit}' data-record='{$customer->id}'><i class='ri-edit-box-fill'></i>{$view_loading}</button>
                        <button class='btn btn-danger remove-record' data-action='{$action_delete}' data-record='{$customer->id}'><i class='ri-delete-bin-fill'></i></button>
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
        ->rawColumns(['action', 'full_name', 'date_action', 'phone']);
        return $datatables->toJson();
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
        $data->update($data_update);

        return $this->successResponse($data, 'Cập nhật thành công');
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
