<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use DataTables;

class MapOrderTankVatController extends Controller
{
    public function index(Request $request){
        $data = DB::table('map_order_tank_vat')
        ->leftJoin('tanks', function($query){
            $query->on('map_order_tank_vat.target_id', '=', 'tanks.id')->where('map_order_tank_vat.target_type', 1);
        })
        ->leftJoin('vats', function($query){
            $query->on('map_order_tank_vat.target_id', '=', 'vats.id')->where('map_order_tank_vat.target_type', 2);
        })
        ->selectRaw("
            map_order_tank_vat.*, 
            IFNULL(tanks.code, vats.code) as code, 
            IFNULL(tanks.current_capacity, vats.current_capacity) as current_capacity,
            IF(tanks.id, 1, 2) as target_type
        ")
        ->get()->keyBy('order');    
        return view("admin.map_order.index", [
            'data' => $data,
        ]);
    }

    public function transLog(Request $request) {
        $map_id = $request->id;
        $find_data = DB::table('map_order_tank_vat')->where('id', $map_id)->first();

        $id = $find_data->target_id;

        $type = 'tanks';
        if($find_data->target_type == 2) {
            $type = 'vats';
        }

        $query_builder = DB::table('transfer_logs')
        ->leftJoin('users as user_create', 'transfer_logs.create_user_id', '=', 'user_create.id')
        ->leftJoin('tanks as tanks_from', function($join) {
            $join->on('tanks_from.id', '=', 'transfer_logs.from_id')
                ->where('transfer_logs.from_type', '=', 'tanks');
        })
        ->leftJoin('vats as vats_from', function($join) {
            $join->on('vats_from.id', '=', 'transfer_logs.from_id')
                ->where('transfer_logs.from_type', '=', 'vats');
        })
        ->leftJoin('tanks as tanks_to', function($join) {
            $join->on('tanks_to.id', '=', 'transfer_logs.to_id')
                ->where('transfer_logs.to_type', '=', 'tanks');
        })
        ->leftJoin('vats as vats_to', function($join) {
            $join->on('vats_to.id', '=', 'transfer_logs.to_id')
                ->where('transfer_logs.to_type', '=', 'vats');
        })
        ->select([
            'transfer_logs.from_id',
            'transfer_logs.from_type',
            'transfer_logs.to_id',
            'transfer_logs.to_type',
            'transfer_logs.created_at',
            DB::raw('IFNULL(tanks_from.code, vats_from.code) AS target_from'),
            DB::raw('IFNULL(tanks_to.code, vats_to.code) AS target_to'),
            DB::raw('
                IF(tanks_from.code IS NOT NULL, transfer_logs.from_tank_current_capacity, transfer_logs.from_vat_current_capacity) AS from_current_capacity
            '),
            DB::raw('
                IF(tanks_to.code IS NOT NULL, transfer_logs.to_tank_current_capacity, transfer_logs.to_vat_current_capacity) AS to_current_capacity
            '),
            'transfer_logs.amount',
            DB::raw('user_create.full_name as user_full_name')
        ])

       
        ->where(function($query) use ($id, $type) {
            $query->where(function($q) use ($id, $type) {
                $q->where('transfer_logs.from_type', $type)
                ->where('transfer_logs.from_id', $id);
            })
            ->orWhere(function($q) use ($id, $type) {
                $q->where('transfer_logs.to_type', $type)
                ->where('transfer_logs.to_id', $id);
            });
        })
        ->orderBy('transfer_logs.created_at', 'desc');

        $datatables = DataTables::of($query_builder)
        ->addColumn('date', function($item){  
            return date("d/m/Y H:i", strtotime($item->created_at));
        })
        ->editColumn('amount', function($item){
            return number_format($item->amount * 1, 0, ",", ".") . " lít";
        })
        ->addColumn('target_from_html', function($item){
            $from_current_capacity = $item->from_current_capacity * 1;
            $div = "
                <div>
                    $item->target_from
                    <div>DTHT: $from_current_capacity LÍT</div>
                </div>
            ";
            return $div . '<i class="mdi mdi-arrow-right-bold text-danger font-18 position-absolute top-50 start-100 translate-middle"></i>';
        })
        ->addColumn('target_to_html', function($item){
            $to_current_capacity = $item->to_current_capacity * 1;
            $div = "
                <div>
                    $item->target_to
                    <div>DTHT: $to_current_capacity LÍT</div>
                </div>
            ";
            return $div;
        })
        ->rawColumns(['date', 'target_from_html', 'target_to_html']);
        return $datatables->toJson();


    }

    public function handle(Request $request) {
        $request->merge([
            'qty' => str_replace(".", "", $request->qty)
        ]);
        $validated = $request->validate([
            'code_target_type' => 'required',
            'code_target_id' => 'required',
            'target_type' => 'required',
            'target_id' => 'required',
            'qty' => 'required|numeric',
        ], [
            'required' => 'không bỏ trống',
            'numeric' => 'không hợp lệ',
        ]);  

        $find_target = null;
        $from_type = null;
        $from_id = null;
        $from_fish_status_vats = null;
        $from_status_vats = null;
        $from_type_tanks = null;
        $from_table = null;
        $from_id = null;
        $from_current_capacity = 0;
        $from_tank_current_capacity = 0;
        $from_vat_current_capacity = 0;
        if($validated['code_target_type'] == 1){
            $find_target = DB::table('tanks')->where('code', $validated['code_target_id'])->first();
            $from_type = 'tanks';
            $from_id = $find_target->id;
            $from_type_tanks = $find_target->type;
            $from_table = 'tanks';
            $from_id = $find_target->id;
            $from_current_capacity = $find_target->current_capacity;
            $from_tank_current_capacity = $find_target->current_capacity;
        } else if($validated['code_target_type'] == 2){
            $find_target = DB::table('vats')->where('code', $validated['code_target_id'])->first();
            $from_type = 'vats';
            $from_id = $find_target->id;
            $from_fish_status_vats = $find_target->fish_status;
            $from_status_vats = $find_target->status;
            $from_table = 'vats';
            $from_id = $find_target->id;
            $from_current_capacity = $find_target->current_capacity;
            $from_vat_current_capacity = $find_target->current_capacity;
        }

        if(!$find_target) {
            return $this->errorResponse("Không tìm thấy dữ liệu, vui lòng F5 thử lại");
        }

        if((int) $validated['qty'] > ($find_target->current_capacity * 1)) {
           throw ValidationException::withMessages([
                'qty' => 'Số lượng nhập vượt quá dung tích hiện của '.$find_target->code,
            ]);
        }
    
        $find_target = null;
        $to_type = null;
        $to_id = null;
        $to_fish_status_vats = null;
        $to_status_vats = null;
        $to_type_tanks = null;
        $to_table = null;
        $to_id = null;
        $to_current_capacity = 0;
        $to_tank_current_capacity = 0;
        $to_vat_current_capacity = 0;
        if($validated['target_type'] === 'BON'){
            $find_target = DB::table('tanks')->where('id', $validated['target_id'])->first();
            $to_type = 'tanks';
            $to_id = $find_target->id;
            $to_type_tanks = $find_target->type;
            $to_table = 'tanks';
            $to_id = $find_target->id;
            $to_current_capacity = $find_target->current_capacity;
            $to_tank_current_capacity = $find_target->current_capacity;
        } else if($validated['target_type'] === 'THUNG'){
            $find_target = DB::table('vats')->where('id', $validated['target_id'])->first();
            $to_type = 'vats';
            $to_id = $find_target->id;
            $to_fish_status_vats = $find_target->fish_status;
            $to_status_vats = $find_target->status;
            $to_table = 'vats';
            $to_id = $find_target->id;
            $to_current_capacity = $find_target->current_capacity;
            $to_vat_current_capacity = $find_target->current_capacity;
        }

        if(!$find_target) {
            return $this->errorResponse("Không tìm thấy dữ liệu, vui lòng F5 thử lại");
        }

        $calculate = ($find_target->current_capacity * 1) + (int) $validated['qty'];
        if($calculate > $find_target->max_capacity * 1){
            throw ValidationException::withMessages([
                'qty' => 'Số lượng đổ nước vượt quá dung tích tối đa của '.$find_target->code,
            ]);
        }

        DB::beginTransaction();
        try {
            //  Cập nhật lại dung tích 
            DB::table($from_table)->where("id", $from_id)->update([
                'current_capacity' => ($from_current_capacity * 1) -  (int) $validated['qty']
            ]);
            //  Cập nhật lại dung tích 
            DB::table($to_table)->where("id", $to_id)->update([
                'current_capacity' => ($to_current_capacity * 1) +  (int) $validated['qty']
            ]);

            $data_insert = [
                'from_type' => $from_type,
                'from_id' => $from_id,
                'to_type' => $to_type,
                'to_id' => $to_id,
                'amount' => $validated['qty'],
                'from_fish_status_vats' => $from_fish_status_vats,
                'from_status_vats' => $from_status_vats,
                'from_type_tanks' => $from_type_tanks,
                'to_fish_status_vats' => $to_fish_status_vats,
                'to_status_vats' => $to_status_vats,
                'to_type_tanks' => $to_type_tanks,
                'from_tank_current_capacity' => $from_tank_current_capacity,
                'to_tank_current_capacity' => $to_tank_current_capacity,
                'from_vat_current_capacity' => $from_vat_current_capacity,
                'to_vat_current_capacity' => $to_vat_current_capacity,
                'created_at' => date("Y-m-d H:i:s"),
                'create_user_id' => auth()->id(),
            ];

            DB::table('transfer_logs')->insert($data_insert);

            DB::commit();

            return $this->successResponse([], "Thực thi thành công");
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();
            return $this->errorResponse("Có lỗi xảy ra, vui lòng xem lại");
        }
    }

    public function create(Request $request) {
        $find_target_type = DB::selectOne("
            SELECT id, 1 as target_type, current_capacity FROM tanks
            WHERE code = :code_tanks
            AND tanks.id NOT IN (SELECT target_id FROM map_order_tank_vat WHERE target_type = 1)
            UNION ALL
            SELECT id, 2 as target_type, current_capacity FROM vats
            WHERE code = :code_vats
            AND vats.id NOT IN (SELECT target_id FROM map_order_tank_vat WHERE target_type = 2)
        ", [
            'code_tanks' => $request->code,
            'code_vats' => $request->code,
        ]);

        if(!$find_target_type) {
            return $this->errorResponse("Không tìm thấy bồn hoặc thùng");
        }

        DB::table('map_order_tank_vat')->upsert(
            [
                [
                    'order'       => $request->order,
                    'target_type' => $find_target_type->target_type,
                    'target_id'   => $find_target_type->id,
                ]
            ],
            ['order'],
            ['target_type', 'target_id']
        );

        $data = DB::table('map_order_tank_vat')->where('order', $request->order)->first();
        $data->code = $request->code;
        $data->current_capacity = $find_target_type->current_capacity;

        return $this->successResponse($data, "Thực hiện thành công");
    }

    public function delete($id, Request $request){
        $data = DB::table('map_order_tank_vat')->where('id', $id)->first();

        if(!$data) {
            return $this->errorResponse('Không tìm thấy dữ liệu', 404);
        }

        DB::table('map_order_tank_vat')->delete($id);

        return $this->successResponse($data, 'Đã xoá dữ liệu');
    }

    public function getDataTarget(Request $request){
        $data = [];
        $code = $request->code;
        if($request->target_type === 'THUNG') {
            $data = DB::table('vats')
            ->where('code', '!=', $code)
            ->whereRaw("
                vats.id IN (SELECT target_id FROM map_order_tank_vat
                WHERE target_type = 2)
            ")
            ->get();
        } else if($request->target_type === 'BON'){
            $data = DB::table('tanks')
            ->where('code', '!=', $code)
            ->whereRaw("
                tanks.id IN (SELECT target_id FROM map_order_tank_vat
                WHERE target_type = 1)
            ")
            ->get();
        }

        return $this->successResponse($data, 'Lấy dữ liệu thành công');
    }
}
