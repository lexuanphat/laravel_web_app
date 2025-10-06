<?php

namespace App\Http\Controllers;

use App\Http\Requests\CouponRequest;
use Illuminate\Http\Request;
use DB;
use DataTables;

class CouponController extends Controller
{
    public function index(){
        $provinces = DB::table("provinces")->selectRaw("id, name as text")->orderBy("text")->get();
        $products = DB::table("products")->selectRaw("id, name as text")->orderBy('text')->get();

        return view('admin.coupon.index', [
            'provinces' => $provinces,
            'products' => $products,
        ]);
    }

    public function detail($id, Request $request){
        $data = DB::table("coupon")
        ->where("id", $id)->first();

        if(!$data) {
            return $this->errorResponse('Không tìm thấy dữ liệu', 404);
        }
        $data->fee = number_format($data->fee, 0, ",", ".");

        return $this->successResponse($data, 'Thành công');
    }

    public function update($id, CouponRequest $request) {
        $data = DB::table("coupon")->where("id", $id);

        if(!$data->first()) {
            return $this->errorResponse('Không tìm thấy dữ liệu', 404);
        }

        $data_update = $request->validated();
        $data_update['fee'] = str_replace(".", "", $data_update['fee']);
        $data_update['updated_user_id'] = auth()->user()->id;
        $data_update['updated_at'] = date("Y-m-d H:i:s");
        $data->update($data_update);

        return $this->successResponse([], 'Cập nhật thành công');
    }

    public function delete($id) {
        $data =  DB::table("coupon")->where("id", $id);

        if(!$data->first()) {
            return $this->errorResponse('Không tìm thấy dữ liệu, vui lòng F5 thử lại', 404);
        }

        $data->delete();
        return $this->successResponse([], 'Đã xoá dữ liệu');
    }

    public function getData(Request $request){
        $search = isset($request->search) && !empty($request->search) ? $request->search : "";
        $search = ltrim($search, '?');
        parse_str($search, $parsed);

        $model = DB::table("coupon")
        ->leftJoin('users as created_user', 'coupon.created_user_id', '=', 'created_user.id')
        ->leftJoin('users as updated_user', 'coupon.updated_user_id', '=', 'updated_user.id')
        ->selectRaw("
            coupon.*,
            DATE_FORMAT(IFNULL(coupon.updated_at, coupon.created_at), '%d/%m/%Y') as date_action,
            IFNULL(updated_user.full_name, created_user.full_name) as user_action

        ")
        ->orderByRaw("created_at DESC, coupon.name ASC");

        // if(isset($parsed['search'])) {
        //     $model->where("products.name", "LIKE" , "%".trim($parsed['search'])."%");
        // }

        // if(isset($parsed['province_id'])) {
        //     $model->where("provinces.id", $parsed['search']);
        // }

        $datatables = DataTables::of($model)
        ->addIndexColumn()
        ->addColumn(
            'action', function($item){
                $view_loading = view("admin._partials.loading");
                $action_edit = route('admin.coupon.detail', ['id' => $item->id]);
                $action_delete = route('admin.coupon.delete', ['id' => $item->id]);

                $button_delete = "<button class='btn btn-danger remove-record' data-action='{$action_delete}' data-record='{$item->id}'><i class='ri-delete-bin-fill fs-5'></i></button>";

                $button_edit = "<button class='btn btn-warning edit-record' data-action='{$action_edit}' data-record='{$item->id}'><i class='ri-edit-box-fill fs-5'></i>{$view_loading}</button>";
                

                return "
                    <div class='button-list'>
                        $button_edit
                        $button_delete
                    </div>
                ";
            }
        )
        
        
        ->editColumn('type', function($item){
            $type = $item->type;

            if($type === 'PHAN_TRAM'){
                $type = 'Phần trăm';
            }else {
                $type = 'Tiền';
            }

                return "
                    <div>{$type}</div>
                ";
            }
        )
        ->editColumn('name', function($item){
            $name = $item->name;
            $code = $item->code;
                return "
                    <div>{$name}</div>
                    <div><span class='badge bg-primary'>{$code}</span></div>
                ";
            }
        )
        ->editColumn('fee', function($item){
            $fee = number_format($item->fee, 0, ',', '.');

            if($item->type === 'PHAN_TRAM'){
                $fee .= ' %';
            } else {
                $fee .= ' đ';
            }

                return "
                    <div>{$fee}</div>
                ";
            }
        )
        ->editColumn('date_action', function($item){
            return "
                <div>{$item->date_action}</div>
            ";
        }
        )
        ->editColumn('user_action', function($item){
            return "
                <div>{$item->user_action}</div>
            ";
        })
        ->editColumn('date_start_apply', function($item){
            $date = date("d/m/Y", strtotime($item->date_start_apply));

            return "
                <div>$date</div>
            ";
        })
        ->editColumn('date_end_apply', function($item){
            $date = date("d/m/Y", strtotime($item->date_end_apply));
            
            return "
                <div>$date</div>
            ";
        })
        ->rawColumns(['name','type', 'fee', 'date_action', 'user_action', 'action', 'date_start_apply', 'date_end_apply']);
        
        return $datatables->toJson();
    }

    public static function generateCodeCustomer($length = 10) {
        do {
            $code = "COUPON-" . \Str::upper(\Str::random($length));
        } while (DB::table("coupon")->where("code", $code)->exists());
    
        return $code;
    }

    public function store(CouponRequest $request) {
        $validated = $request->validated();

        $validated['fee'] = str_replace(".", "", $validated['fee']);
        $validated['code'] = self::generateCodeCustomer();
        $validated['created_user_id'] = auth()->id();
        $validated['created_at'] = date("Y-m-d H:i:s");
        $validated['updated_at'] = null;

        DB::table("coupon")->insert($validated);

        return $this->successResponse([], 'Thêm mới thành công');
    }
}
