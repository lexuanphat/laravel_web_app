<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerValidateRequest;
use App\Models\Customer;
use Illuminate\Http\Request;
use DataTables;

class CustomerController extends Controller
{
    public function index(){
        return view('admin.customer.index');
    }

    public function getData(Request $request) {
        $model = Customer::with('user:id,full_name')->orderBy('id', 'desc');
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
            $created_at = $customer->created_at ? date("d/m/Y H:i", strtotime($customer->created_at)) : 'X';
            $updated_at = $customer->updated_at ? date("d/m/Y H:i", strtotime($customer->updated_at)) : 'X';
            $user_action = $customer->user->full_name;
            return "
                <div>{$created_at}</div>
                 <div>{$updated_at}</div>
                 <div>{$user_action}</div>
            ";
        })
        ->addColumn('info', function($customer){
            $phone = $customer->phone;
            $email = $customer->email ? $customer->email : 'X';
            $date_of_birth = $customer->date_of_birth ? date("d/m/Y", strtotime($customer->date_of_birth)) : 'X';

            return "
            <div><a href='tel:{$phone}'>{$phone}</a></div>
             <div><a href='mailto:{$email}'>{$email}</a></div>
             <div>{$date_of_birth}</div>
        ";
        })
        ->editColumn('full_name', function($customer){
                return "
                    <div>{$customer->full_name} <span class='badge badge-{$customer->getColorGender($customer->gender)}'>{$customer->getGender($customer->gender)}</span></div>
                    <div class='text-warning'>{$customer->code}</div>
                ";
            }
        )
        ->rawColumns(['action', 'full_name', 'date_action', 'info']);
        return $datatables->toJson();
    }

    public function store(CustomerValidateRequest $request){
        $validated = $request->validated();
        $validated['user_id'] = auth()->user()->id;
        $validated['code'] = Customer::generateCodeCustomer();
        $validated['created_at'] = date("Y-m-d H:i:s");
        $validated['updated_at'] = null;

        Customer::create($validated);
        return $this->successResponse([], 'Thêm mới thành công');
    }

    public function detail($id, Request $request){
        $data = Customer::find($id);

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
        $validated['user_id'] = auth()->user()->id;
        $validated['created_at'] = null;
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
