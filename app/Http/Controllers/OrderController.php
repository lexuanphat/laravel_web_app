<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderStatusValidateRequest;
use App\Http\Requests\OrderValidateRequest;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use App\Models\Transport;
use App\Services\Shipping\Ghn;
use App\Services\Shipping\Ghtk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Jenssegers\Agent\Agent;

use DataTables;
class OrderController extends Controller
{
    public $gthk_list_pick_add = [];
    public $config_ghtk = [];

    public function __construct(){
        $find_config_api_ghtk = DB::table('tokens')->where('is_transport', $this::PARTNER['GHTK'])->selectRaw('_token, api')->first();
        $ghtk = new Ghtk($find_config_api_ghtk->_token, '' , $find_config_api_ghtk->api);
        
        $this->config_ghtk = $find_config_api_ghtk;
        $this->gthk_list_pick_add = $ghtk->getListPickAdd();
    }

    private const PARTNER = [
        'GHN' => 'GHN',
        'GHTK' => 'GHTK',
    ];
    private const PREFIX_KEY_CODE = "DH-";
    private const STATUS_GHN = [
        'ready_to_pick'             => 'Mới tạo đơn hàng',
        'picking'                   => 'Nhân viên đang lấy hàng',
        'cancel'                    => 'Hủy đơn hàng',
        'money_collect_picking'     => 'Đang thu tiền người gửi',
        'picked'                    => 'Nhân viên đã lấy hàng',
        'storing'                   => 'Hàng đang nằm ở kho',
        'transporting'              => 'Đang luân chuyển hàng',
        'sorting'                   => 'Đang phân loại hàng hóa',
        'delivering'                => 'Nhân viên đang giao cho người nhận',
        'money_collect_delivering'  => 'Nhân viên đang thu tiền người nhận',
        'delivered'                 => 'Nhân viên đã giao hàng thành công',
        'delivery_fail'             => 'Nhân viên giao hàng thất bại',
        'waiting_to_return'         => 'Đang đợi trả hàng về cho người gửi',
        'return'                    => 'Trả hàng',
        'return_transporting'       => 'Đang luân chuyển hàng trả',
        'return_sorting'            => 'Đang phân loại hàng trả',
        'returning'                 => 'Nhân viên đang đi trả hàng',
        'return_fail'               => 'Nhân viên trả hàng thất bại',
        'returned'                  => 'Nhân viên trả hàng thành công',
        'exception'                 => 'Đơn hàng ngoại lệ không nằm trong quy trình',
        'damage'                    => 'Hàng bị hư hỏng',
        'lost'                      => 'Hàng bị mất',
    ];
    private const DELIVERY_METHOD = [
        1 => "Hãng vận chuyển",
        2 => "Vận chuyển ngoài",
        3 => "Nhận tại cửa hàng",
        4 => "Giao hàng sau",
    ];
    private const PAYER_FEE = [
        'shop' => 'Shop trả',
        'customer' => 'Khách trả',
    ];
    private const GUESS_TRANSPORT = [
        '-1' => "Giao hàng nhanh",
        '-2' => "Giao hàng tiết kiệm",
    ];
    private const IMAGE_TRANSPORT = [
        '-1' => '/assets/images/transport/logo-ghn-new-vip.png',
        '-2' => '/assets/images/transport/logo-ghtk.png',
    ];
    private const TRANSPORT_TYPE = [
        ...Transport::ROLE_RENDER_BLADE,
        'DVVC' => 'Hãng vận chuyển',
    ];

    const ORDER_STATUS = [
        'processing' => 1,
        'delivering' => 2,
        'delivered' => 3,
        'cancelled' => 4,
        'failed' => 5,
        'returned' => 6,
        'refunded' => 7,
        'completed' => 8,
        'requested_cancel' => 9,
    ];

    const ORDER_STATUS_MESSAGE = [
        1 => 'Đang chuẩn bị hàng / đóng gói',
        2 => 'Đang giao',
        3 => 'Giao thành công',
        4 => 'Đã hủy đơn',
        5 => 'Giao thất bại',
        6 => 'Bị trả hàng',
        7 => 'Đã hoàn tiền',
        8 => 'Hoàn tất đơn',
        9 => 'Đã gửi yêu cầu huỷ đơn',
    ];

    const ORDER_STATUS_MESSAGE_MOBILE = [
        1 => 'Chuẩn bị hàng',
        2 => 'Đang giao',
        3 => 'Thành công',
        4 => 'Đã hủy đơn',
        5 => 'Thất bại',
        6 => 'Trả hàng',
        7 => 'Hoàn tiền',
        8 => 'Hoàn tất',
        9 => 'Yêu cầu huỷ đơn',
    ];

    public function index(){
        $staffs = DB::table("users")
        ->selectRaw("id, full_name")
        ->get();

        $status_order = self::ORDER_STATUS_MESSAGE;

        
        $get_customers  = DB::table("customers")
        ->selectRaw("id, full_name, phone, code")
        ->get();

        return view("admin.order.index", [
            'staffs' => $staffs,
            'status_order' => $status_order,
            'get_customers' => $get_customers,
        ]);
    }

    public function changeStatus(OrderStatusValidateRequest $request) {
        $validated = $request->validated();

        $validator = Validator::make( [], []);
        $ids = isset($request->ids) && !empty($request->ids) ? $request->ids : [];
        if(empty($ids)) {
            $validator->errors()->add('status_code', 'Vui lòng chọn đơn hàng');
            throw new \Illuminate\Validation\ValidationException($validator);
        }

        $list_ids_valid = [];
        foreach($ids as $id) {
            $is_valid = DB::table("order_shipments")->where("order_id", $id)->whereNotIn("shipping_partner_id", array_keys(self::GUESS_TRANSPORT))->exists();
            if(!$is_valid) {
                $validator->errors()->add('status_code', 'Có đơn hàng không hợp lệ, vui lòng kiểm tra lại');
                throw new \Illuminate\Validation\ValidationException($validator);
            }
            $list_ids_valid[] = $id;
        }

        DB::beginTransaction();

        try{
            foreach($list_ids_valid as $id_valid) {
                $query_order_shipment = DB::table("order_shipments")->where("order_id", $id_valid);

                DB::table("orders")->where("id", $id_valid)->update([
                    'status' => $validated['status_code'],
                    'user_id' => auth()->user()->id,
                    'updated_at' => date("Y-m-d H:i:s"),
                ]);

                $query_order_shipment->update([
                    'current_status' => $validated['status_code'],
                    'updated_at' => date("Y-m-d H:i:s"),
                ]);

                DB::table("order_shipment_status_logs")->insert([
                    'order_shipment_id' => $query_order_shipment->first()->id,
                    'status_code' => $validated['status_code'],
                    'status_text' => self::ORDER_STATUS_MESSAGE[$validated['status_code']],
                    'status_time' => date("Y-m-d H:i:s"),
                    'note' => $validated['note_logs'],
                    'raw_payload' => null,
                    'updated_at' => date("Y-m-d H:i:s"),
                ]);
            }

            DB::commit();
            return $this->successResponse([], 'Cập nhật thành công');
        }catch (\Throwable $th) {
            dd($th);
            DB::rollBack();
            return $this->errorResponse('Có lỗi vui lòng thử lại');
        }

    }

    public function getData(Request $request) {
        $search = isset($request->search) && !empty($request->search) ? $request->search : "";
        $search = ltrim($search, '?');
        parse_str($search, $parsed);

        $query = DB::table("orders")
        ->leftJoin("users", "orders.user_id", "=", "users.id")
        ->leftJoin("customers", "orders.customer_id", "=", "customers.id")
        ->leftJoin("order_shipments", "orders.id", "=", "order_shipments.order_id")
        ->leftJoin('transports', 'order_shipments.shipping_partner_id', '=', 'transports.id');
        

        // if(auth()->user()->role !== User::ROLE_ACCESS_PAGE['admin']) {
        //     $query = $query->where("orders.store_id", auth()->user()->store_id);
        // }

        if(isset($parsed['date']) && !empty($parsed['date'])) {
            $current_date = date("Y-m-d");

            switch ($parsed['date']) {
                case '7days':
                    $filter_date = date("Y-m-d", strtotime("-7 days"));
                    break;
                case '30days':
                    $filter_date = date("Y-m-d", strtotime("-30 days"));
                    break;
                case '1year':
                    $filter_date = date("Y-m-d", strtotime("-1 year"));
                    break;
                case '2year':
                    $filter_date = date("Y-m-d", strtotime("-2 year"));
                    break;
                
                default:
                    $filter_date = date("Y-m-d");
                    break;
            }
            $query->where("orders.created_at", ">=", "$filter_date 00:00:00")->where("orders.created_at", "<=", "$current_date 23:59:59");
        }

        if(isset($parsed['staff']) && !empty($parsed['staff'])) {
            $query->where("orders.user_id", $parsed['staff']);
        }

        if(isset($parsed['object_order']) && !empty($parsed['object_order'])) {
            $query->whereRaw("( (orders.user_order = ?) OR (orders.user_consignee = ?) OR (orders.user_payer = ?) )", [
                $parsed['object_order'], $parsed['object_order'], $parsed['object_order']
            ]);
        }

        if(isset($parsed['status_order']) && !empty($parsed['status_order']) && $parsed['status_order'] != -1) {
            $query->where("orders.status", self::ORDER_STATUS[$parsed['status_order']]);
        }

        if(isset($parsed['search'])) {
            $query->where(function($q)use($parsed){
                $q->whereExists(function($q)use($parsed){
                    $q->select(DB::raw(1))
                    ->from("order_items")
                    ->whereColumn('orders.id', 'order_items.order_id')
                    ->where("product_name", "like" , "%".trim($parsed['search'])."%");
                })
                ->orWhere("orders.customer_full_name", trim($parsed['search']))
                ->orWhere("orders.code", trim($parsed['search']));
            });
        }

        $query = $query->selectRaw("
            orders.*, customers.full_name as main_customer_full_name, order_shipments.shipping_partner_id, transports.full_name as partner_name, orders.status as status_raw,
            users.full_name as user_full_name
        ")
        ->orderBy("created_at", "DESC");

        $datatables = DataTables::query($query)
        ->editColumn('created_at', function($item){
            return "
                <div class='text-center'>".date("d/m/Y H:i", strtotime($item->created_at))."</div>
            ";
        })
        ->editColumn('total_amount', function($item){
            return "
                <div class='text-end'>".number_format($item->total_amount, 0, ",", ".")."</div>
            ";
        })
        ->editColumn('status', function($item){
            $content = "";
            if(is_null($item->shipping_partner_id) || !in_array($item->shipping_partner_id, array_keys(self::GUESS_TRANSPORT))) {
                $content = "<div class='text-center'>".self::ORDER_STATUS_MESSAGE[$item->status]."</div>";
            } else if($item->shipping_partner_id === -1) {
                $content = "<div class='text-center'>".self::ORDER_STATUS_MESSAGE[$item->status]."</div>";
            } else if($item->shipping_partner_id === -2){$content = "<div class='text-center'>".Ghtk::STATUS_ORDER[$item->status]."</div>";
                if($item->status === self::ORDER_STATUS['requested_cancel']) {
                    $content = "<div class='text-center'>".self::ORDER_STATUS_MESSAGE[$item->status]."</div>";
                } else {
                    $content = "<div class='text-center'>".Ghtk::STATUS_ORDER[$item->status]."</div>";
                }
            }

            return $content;

        })
        ->addColumn('object_partner', function($item){

            if(in_array($item->shipping_partner_id, array_keys(self::GUESS_TRANSPORT))) {
                $image = '<img src="'.self::IMAGE_TRANSPORT[$item->shipping_partner_id].'" alt="table-user" class="me-2">';
                $html = "<div class='table-user text-center'>".$image."</div>";
            } else if(!is_null($item->shipping_partner_id) && $item->shipping_partner_id){
                $html = "<div class='text-center'>{$item->partner_name}</div>";
            } else {
                $html = "<div class='text-center'>Nhận tại cửa hàng</div>";
            }

            return $html;
        })
        ->editColumn('code', function($item){
            $link = route('admin.order.detail', ['id' => $item->id]);
            return "
                <div class='text-center'><a href='$link'>{$item->code}</a></div>
            ";
        })
        ->addColumn('checkbox', function($item){
            $is_disabled = '';
            if(in_array($item->shipping_partner_id, array_keys(self::GUESS_TRANSPORT)) || is_null($item->shipping_partner_id) && $item->status === self::ORDER_STATUS['completed']
            || is_null($item->shipping_partner_id) && $item->status === self::ORDER_STATUS['cancelled']
            ) {
                $is_disabled = 'disabled';
            }

            return '
                <div class="form-check">
                    <input type="checkbox" '.$is_disabled.' class="form-check-input check-input-item" id="check-input-'.$item->id.'" value='.$item->id.'>
                    <label class="form-check-label" for="check-input-'.$item->id.'">&nbsp;</label>
                </div>
            ';
        })
        ->addColumn('user_order', function($item){
            return "<div>{$item->user_full_name}</div>";
        })
        ->addColumn('function', function($item){
            $link = route('admin.order.detail', ['id' => $item->id]);
            $link_delete = route('admin.order.delete', ['id' => $item->id]);
            $elements = "<div class='d-flex flex-wrap justify-content-center'>";
            $elements .= "<a href='$link'><i class='ri-eye-fill fs-4'> </i></a>";
            if(in_array($item->shipping_partner_id, array_keys(self::GUESS_TRANSPORT))) {
                $is_disabled = "";
                $text = "Huỷ đơn hàng";
                $key_partner = self::PARTNER['GHN'];
                if($item->shipping_partner_id == -2){
                    $key_partner = self::PARTNER['GHTK'];
                }
                if($item->status === self::ORDER_STATUS['cancelled']) {
                    $is_disabled = "disabled";
                }
                if($item->status === self::ORDER_STATUS['requested_cancel']) {
                    $is_disabled = "disabled";
                    $text = "Đã gửi yêu cầu";
                }
                $elements .= '<button class="btn btn-primary" '.$is_disabled.' onclick="cancelOrderPartner('.$item->store_id.', '.$item->id.', `'.$key_partner.'`)">'.$text.'</button>';
            }
            $elements .= '<button class="btn btn-danger remove-record" data-action="'.$link_delete.'" data-record="'.$item->id.'"><i class="ri-delete-bin-fill"></i></button>';
            $elements .= "</div>";

            return $elements;
        })
        ->rawColumns(['total_amount', 'created_at', 'status', 'code', 'function', 'checkbox', 'object_partner', 'user_order']);
        return $datatables->toJson();
    }

    public function delete($id) {
        $find_data_order = DB::table("orders")->where("id", $id);
        
        if(!$find_data_order->first()) {
            return $this->errorResponse('Không tìm thấy dữ liệu, vui lòng F5 thử lại', 404);
        }
        
        DB::beginTransaction();

        try {
            $find_data_order->delete();
            DB::table("order_items")->where("order_id", $id)->delete();
            DB::commit();

            return $this->successResponse([], 'Đã xoá dữ liệu');
        } catch (\Exception $e) {
            DB::rollback();
            // something went wrong
        }
    }

    public function cancelOrderPartner(Request $request) {
        $find_order = DB::table("orders")
        ->join("store_details", "orders.store_id", "=", "store_details.store_id")
        ->join("order_shipments", "orders.id", "=", "order_shipments.order_id")
        ->join("tokens", "store_details.is_transport", "=", "tokens.is_transport")
        ->where("orders.id", $request->order_id ?: 0)
        ->where("store_details.is_transport", $request->key_partner ?: 0)
        ->where("orders.store_id", $request->store_id ?: 0 )
        ->where("orders.status", "!=", self::ORDER_STATUS['cancelled'])
        ->selectRaw("
            order_shipments.tracking_number, tokens.api, tokens._token, store_details.transport_id, order_shipments.id as order_shipment_id,
            (
                SELECT JSON_ARRAYAGG(
                    JSON_OBJECT(
                        'product_id', oi.product_id,
                        'product_quantity', oi.product_quantity
                    )
                )
                FROM order_items as oi
                WHERE oi.order_id = orders.id AND oi.order_id = orders.id
            ) as items
        ")
        ->first();

        if(!$find_order) {
            return $this->errorResponse('Không tìm thấy đơn hàng');
        }

        DB::beginTransaction();
        try{
            if($request->key_partner === self::PARTNER['GHN']) {
                $ghn_service = new Ghn($find_order->_token, $find_order->transport_id, $find_order->api);
                $response =  $ghn_service->cancelOrder($find_order->tracking_number);

                if(isset($response[0]) && $response[0]['result']) {
                    DB::table("orders")->where("id", $request->order_id)->update([
                        'status' => self::ORDER_STATUS['cancelled'],
                    ]);
                    DB::table("order_shipments")
                    ->where("order_id", $request->order_id)
                    ->where("shipping_partner_id", "-1")
                    ->update([
                        'current_status' => self::ORDER_STATUS['cancelled'],
                    ]);
                    DB::table("order_shipment_status_logs")->insert([
                        'order_shipment_id' => $find_order->order_shipment_id,
                        'status_code' => self::ORDER_STATUS['cancelled'],
                        'status_text' => self::ORDER_STATUS_MESSAGE[self::ORDER_STATUS['cancelled']],
                        'status_time' => date("Y-m-d H:i:s"),
                        'note' => isset($request->note) ? $request->note : null,
                        'raw_payload' => json_encode($response),
                        'created_at' => date("Y-m-d H:i:s"),
                    ]);

                    foreach(json_decode($find_order->items, true) as $prod_item) {
                        DB::table("product_stocks")
                        ->where("product_id", $prod_item['product_id'])
                        ->where("store_id", $request->order_id)
                        ->update([
                            "quantity_sold" => DB::raw("quantity_sold + {$prod_item['product_quantity']}")
                        ]);
                    }
                    
                    DB::commit();

                    return $this->successResponse([], 'Huỷ đơn thành công');
                }
    
            } else if($request->key_partner === self::PARTNER['GHTK']){
                $ghtk_service = new Ghtk($find_order->_token, $find_order->transport_id, $find_order->api);
                $response =  $ghtk_service->cancelOrder($find_order->tracking_number);

                if(isset($response['success']) && $response['success']) {
                    $message = "";
                    if(isset($response['log_id'])) {
                        // Chưa biết GHTK bắn về cái gì nên chưa biết code chỗ này
                    } else {
                        DB::table("orders")->where("id", $request->order_id)->update([
                            'status' => self::ORDER_STATUS['requested_cancel'],
                        ]);
                        DB::table("order_shipments")
                        ->where("order_id", $request->order_id)
                        ->where("shipping_partner_id", "-1")
                        ->update([
                            'current_status' => self::ORDER_STATUS['requested_cancel'],
                        ]);

                        DB::table("order_shipment_status_logs")->insert([
                            'order_shipment_id' => $find_order->order_shipment_id,
                            'status_code' => self::ORDER_STATUS['requested_cancel'],
                            'status_text' => self::ORDER_STATUS_MESSAGE[self::ORDER_STATUS['requested_cancel']],
                            'status_time' => date("Y-m-d H:i:s"),
                            'note' => isset($request->note) ? $request->note : null,
                            'raw_payload' => json_encode($response),
                            'created_at' => date("Y-m-d H:i:s"),
                        ]);

                        $message = $response['message'];
                    }

                    DB::commit();

                    return $this->successResponse([], $message);
                }

            } else {
                return $this->errorResponse('Đơn hàng không hợp lệ');
            }
        }catch(\Throwable $th){
            DB::rollback();
            throw $th;
            return $this->errorResponse('Có lỗi xảy ra, vui lòng kiểm tra lại');
        }
    }

    public function getDataOld(Request $request){
        $search = isset($request->search) && !empty($request->search) ? $request->search : "";
        $search = ltrim($search, '?');
        parse_str($search, $parsed);

        $start = $request->get('start', 0);
        $query = DB::table("orders")
        ->leftJoin('customers', 'orders.customer_id', '=', 'customers.id')
        ->leftJoin('users', 'orders.user_id', '=', 'users.id')
        ->leftJoin('transports', function($leftJoin){
            $leftJoin->on("orders.partner_transport_id", "=", "transports.id");
            $leftJoin->whereIn('orders.partner_transport_type', ['SHIPPER', 'CHANH_XE']);
        });
        if(auth()->user()->role !== User::ROLE_ACCESS_PAGE['admin']) {
            $query = $query->where("orders.store_id", auth()->user()->store_id);
        }
        $query = $query->selectRaw("orders.*, customers.full_name as customer_full_name, users.full_name as full_name_action, transports.full_name as partner_transport_full_name")
        ->orderBy("create_date", "DESC");

        if(isset($parsed['date']) && !empty($parsed['date'])) {
            $current_date = date("Y-m-d");

            switch ($parsed['date']) {
                case '7days':
                    $filter_date = date("Y-m-d", strtotime("-7 days"));
                    break;
                case '30days':
                    $filter_date = date("Y-m-d", strtotime("-30 days"));
                    break;
                
                default:
                    $filter_date = date("Y-m-d");
                    break;
            }
            $query->where("create_date", ">=", "$filter_date 00:00:00")->where("create_date", "<=", "$current_date 23:59:59");
        }

        if(isset($parsed['staff']) && !empty($parsed['staff'])) {
            $query->where("user_create_order", $parsed['staff']);
        }

        if(isset($parsed['search'])) {
            $query->where(function($q)use($parsed){
                $q->whereExists(function($q)use($parsed){
                    $q->select(DB::raw(1))
                    ->from("order_details")
                    ->whereColumn('orders.id', 'order_details.order_id')
                    ->where("product_name", "like" , "%".trim($parsed['search'])."%");
                })
                ->orWhere("orders.full_name", trim($parsed['search']))
                ->orWhere("orders.code_order", trim($parsed['search']))
                ->orWhere("orders.code_transport", trim($parsed['search']));
            });
        }

        $datatables = DataTables::query($query)
        ->editColumn('create_date', function($data){
            $create_date = date("d/m/Y H:i:s", strtotime($data->create_date));

            return "
                <div class='text-center'>{$create_date}</div>
            ";
        })
        ->editColumn('code', function($data){
            $link = route("admin.order.detail", ['id' => $data->id]);
            return "
                <div class='colorHeader fw-bold'>
                    <a href='$link' class='link-primary'>{$data->code_order}</a>
                </div>
            ";
        })
        ->editColumn('customer_full_name', function($data){
            return "
                <div class='text-center'>{$data->customer_full_name}</div>
            ";
        })
        ->editColumn('paid_amount', function($data){
            $paid_amount = number_format($data->paid_amount, 0, ',', '.');
    
            return "
                <div class='text-end'>{$paid_amount}</div>
            ";
        })
        ->addColumn('status_payment', function($data){
            $status = "X";
            if((int) $data->customer_has_paid_total >= (int) $data->customer_paid_total) {
                if($data->delivery_method === 3 && $data->status_order === "success") {
                    $status = "Trả hết";
                } else if($data->delivery_method === 2 && $data->status_order === "success"){
                    $status = "Trả hết";
                }
            } else if((int) $data->customer_has_paid_total === 0) {
                if($data->delivery_method === 3 && $data->status_order === "success") {
                   $status = "Chưa trả";
                } else if($data->delivery_method === 2 && $data->status_order === "success"){
                   $status = "Chưa trả";
                }
            } else {

                if($data->delivery_method === 3 && $data->status_order === "success") {
                    $status = "Trả 1 phần";
                } else if($data->delivery_method === 2 && $data->status_order === "success"){
                    $status = "Trả 1 phần";
                }
            }

            return "
                <div class='text-center'>{$status}</div>
            ";
        })
        ->rawColumns(['create_date', 'code_order', 'customer_paid_total', 'status_payment', 'full_name']);
        return $datatables->toJson();
    }

    public function detail($id, Request $request) {
        $data = DB::table("orders")
        ->leftJoin('order_shipments', 'orders.id', '=', 'order_shipments.order_id')
        ->leftJoin('provinces', 'orders.customer_province', '=', 'provinces.code')
        ->leftJoin('wards', 'orders.customer_ward', '=', 'wards.code')
        ->where("orders.id", $id);

        if(auth()->user()->role !== User::ROLE_ACCESS_PAGE['admin']){
            $data = $data->where('orders.store_id', auth()->user()->store_id);
        }

        $data = $data->selectRaw("
            orders.*, order_shipments.shipping_partner_id, order_shipments.shipping_fee, 
            order_shipments.current_status, order_shipments.tracking_number, order_shipments.cod,
            order_shipments.note as note_transport, order_shipments.id as order_shipment_id, provinces.name as customer_province_name, 
            wards.name as customer_ward_name
        ")
        ->first();

        if(!$data) {
            return redirect()->back();
        }

        $data->items = DB::table("order_items")
        ->where("order_id", $id)
        ->get()->toArray();

        $agent = new Agent();

        $data->logs = DB::table("order_shipment_status_logs")
        ->where("order_shipment_id", $data->order_shipment_id)
        ->orderBy("status_time")
        ->get()->toArray();

        if($agent->isMobile() && !in_array($data->shipping_partner_id, self::GUESS_TRANSPORT)) {
            foreach($data->logs as $item) {
                $item->status_text = self::ORDER_STATUS_MESSAGE_MOBILE[$item->status_code];
            }
        }

        $data->transport_partner = 'Nhận tại cửa hàng';
        if($data->shipping_partner_id === -1) {
            $data->transport_partner = self::GUESS_TRANSPORT[-1];
        } else if($data->shipping_partner_id === -2){
            $data->transport_partner = self::GUESS_TRANSPORT[-2];
        } else if(!is_null($data->shipping_partner_id)) {
            $data->transport_partner = DB::table("transports")->where('id', $data->shipping_partner_id)->value('role');
            $data->transport_partner = Transport::ROLE_RENDER_BLADE[$data->transport_partner];
        }

        return view("admin.order.detail", [
            'data' => $data,
        ]);
    }

    public function detailOld($id, Request $request){

        $detailsSub = DB::table('order_items')
        ->join('orders', 'order_details.order_id', '=', 'orders.id');

        if(auth()->user()->role !== User::ROLE_ACCESS_PAGE['admin']){
            $detailsSub = $detailsSub->where('orders.store_id', auth()->user()->store_id);
        }

        $detailsSub = $detailsSub->selectRaw("
            order_details.order_id,
            JSON_ARRAYAGG(
                JSON_OBJECT(
                    'product_name', order_details.product_name,
                    'quantity', order_details.quantity,
                    'price', order_details.price,
                    'is_discount', order_details.is_discount,
                    'discount', order_details.discount,
                    'total_price', order_details.total_price
                )
            ) AS details
        ")
        ->groupBy('order_details.order_id');


        $order = DB::table('orders as o')
            ->joinSub($detailsSub, 'od', function($join) {
                $join->on('od.order_id', '=', 'o.id');
            })
            ->join('users as u', 'o.user_create_order', '=', 'u.id')
            ->join('stores', 'o.store_id', '=', 'stores.id')
            ->leftJoin('transports', function($join) {
                $join->on('o.partner_transport_id', '=', 'transports.id')
                    ->whereIn('o.partner_transport_type', [Transport::ROLE['SHIPPER'], Transport::ROLE['CHANH_XE']]);
            })
            ->where('o.id', $id)
            ->selectRaw("
                o.id,
                o.code_transport,
                o.code_order,
                o.store_id,
                o.customer_id,
                o.full_name,
                o.phone,
                o.address,
                o.total_product,
                o.total_price,
                o.total_discount,
                o.customer_paid_total,
                o.customer_has_paid_total,
                o.user_create_order,
                o.source,
                o.delivery_date,
                o.create_date,
                o.delivery_method,
                o.partner_transport_type,
                o.partner_transport_id,
                o.delivery_method_fee,
                o.payer_fee,
                o.cod,
                o.gam,
                o.length,
                o.height,
                o.width,
                o.require_transport_option,
                o.status_transport,
                o.status_order,
                o.note_transport,
                o.note_order,
                o.user_id,
                u.full_name as user_order_full_name,
                stores.name as store_name,
                transports.full_name as transport_full_name,
                od.details
            ")
        ->first();

        if(!$order) {
            return redirect()->back();
        }

        $order->details = json_decode($order->details, true);
        $order->delivery_method_name = self::DELIVERY_METHOD[$order->delivery_method];
        $order->partner_transport_type_name = isset(self::TRANSPORT_TYPE[$order->partner_transport_type]) ? self::TRANSPORT_TYPE[$order->partner_transport_type] : "X";

        if($order->delivery_method === 1) {
            $order->transport_full_name = self::GUESS_TRANSPORT[$order->partner_transport_id];
        }

        return view("admin.order.detail", [
            'data' => $order,
        ]);
    }

    public function create(){
        $get_transport = Transport::selectRaw("
            id, 
            CONCAT(full_name, ' - ', phone) as text,
            role
        ")->get();
        
        $get_store = Store::get();

        $get_provinces = DB::table("provinces")->selectRaw("code as id, name_with_type as text")->orderBy('text')->get();

        $shipping_fees = DB::table("shipping_fees")
        ->selectRaw("province_id, fee")->get();

        $coupons = DB::table("coupon")
        ->where('date_start_apply', '<=', date("Y-m-d"))
        ->where('date_end_apply', '>=', date("Y-m-d"))
        ->selectRaw("id, CONCAT(code, ' - ', FORMAT(fee, 0, 'vi-VN'), IF(type = 'PHAN_TRAM', '%', 'VNĐ')) as text, fee, type")
        ->orderByRaw("created_at ASC, name ASC")
        ->get();

        $get_customers  = DB::table("customers")
        ->selectRaw("id, full_name, phone, code")
        ->get();
        


        return view('admin.order.create-new', [
            'get_transport' => $get_transport,
            'get_store' => $get_store,
            'get_provinces' => $get_provinces,
            'shipping_fees' => $shipping_fees,
            'get_list_pick_add_ghtk' =>  $this->gthk_list_pick_add,
            'coupons' => $coupons,
            'get_customers' => $get_customers ,
        ]);
    }

    public function getDataCustomer(Request $request){
        $results = Customer::selectRaw("*")
        ->where(function ($q) use ($request) {
            $q->where("full_name", "like", "%{$request->search}%")
              ->orWhere("phone", "like", "%{$request->search}%")
              ->orWhere("code", "like", "%{$request->search}%");
        })->paginate(15);
        
        return $this->successResponse($results, 'Lấy dữ liệu thành công');
    }

    public function createOrder(OrderValidateRequest $request) {
        $validator = Validator::make([], []);

        $validated = $request->validated();
        
        $data_insert_order = [
            'code' => self::generateCode(),
            'customer_id' => $validated['customer']['id'],
            'customer_phone' => $validated['customer']['phone'],
            'customer_full_name' => $validated['customer']['full_name'],
            'customer_province' => $validated['customer']['province'],
            'customer_district' => '',
            'customer_ward' => $validated['customer']['ward'],
            'customer_address' => $validated['customer']['address'],
            'store_id' => $validated['pick_address_id'],
            'total_discount' => $validated['discount_total'],
            'paid_amount' => $validated['customer_has_paid_total'] * 1,
            'shipping_fee_payer' => $validated['client_request_transport']['shipping_fee_payer'],
            'source' => $validated['source'],
            'status' => self::ORDER_STATUS['processing'],
            'note' => $validated['note'],
            'user_id' => auth()->user()->id,
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => null,
            'coupon_id' => $validated['coupon'] ?  $validated['coupon'] : 0,
            'user_order' => $validated['user_order'],
            'user_consignee' => $validated['user_consignee'],
            'user_payer' => $validated['user_payer'],
        ];

        $get_products = $this->_getProducts();
        $total_price = 0;
        $data_insert_order_items = [];
        $check_stocks = [];

        # dành cho GHN
        $calculate_weight = 0;
        $items = [];
        $items_ghtk = [];
        foreach($validated['products'] as $prod) {
            # dành cho GHN
            $calculate_weight += $get_products[$prod['product_id']]->weight * (int) $prod['quantity'];
            $items[] = [
                'name' => $get_products[$prod['product_id']]->name,
                'code' => $get_products[$prod['product_id']]->code,
                'quantity' => (int) $prod['quantity'],
                'price' => (int) $get_products[$prod['product_id']]->price,
                'length' => (int)$get_products[$prod['product_id']]->length,
                'width' => (int)$get_products[$prod['product_id']]->width,
                'weight' => (int)$get_products[$prod['product_id']]->weight,
                'height' => (int)$get_products[$prod['product_id']]->height,
            ];
            $items_ghtk[] = [
                'name' => $get_products[$prod['product_id']]->name,
                'price' => (int) $get_products[$prod['product_id']]->price,
                'product_code' => $get_products[$prod['product_id']]->code,
                'quantity' => (int) $prod['quantity'],
                'weight' => (double) ($get_products[$prod['product_id']]->weight / 1000),
            ];

            $price_prod = DB::table("fee_product_province")->where("product_id", $prod['product_id'])->where("province_id", $validated['customer']['province'])->value('fee');

            if($price_prod) {
                $calculate_price_quantity = $price_prod * $prod['quantity'];
            } else {
                $calculate_price_quantity = $get_products[$prod['product_id']]->price * $prod['quantity'];
            }

            $product_total_discount = 0;
            if($prod['is_option'] === "1") { # giảm giá = GIÁ TRỊ
                $product_total_discount = $calculate_price_quantity - $prod['discount'];
            } else { # giảm giá = %
                $discount = ($calculate_price_quantity * $prod['discount']) / 100;
                $product_total_discount = $calculate_price_quantity - $discount;
            }

            $total_price += $product_total_discount;

            $data_insert_order_items[] = [
                'product_id' => $prod['product_id'],
                'product_name' => $get_products[$prod['product_id']]->name,
                'product_quantity' => $prod['quantity'],
                'product_price' => $get_products[$prod['product_id']]->price,
                'is_discount' => $prod['is_option'],
                'product_discount' => $prod['discount'],
                'product_total' => $get_products[$prod['product_id']]->price * $prod['quantity'],
                'product_total_discount' => $product_total_discount,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => null,
            ];
            
            if(isset($check_stocks[$prod['product_id']])) {
                $check_stocks[$prod['product_id']] += $prod['quantity'];
            } else {
                $check_stocks[$prod['product_id']] = $prod['quantity'];
            }
        }

        $data_insert_order['total_price'] =  $total_price;
        $find_coupon = DB::table("coupon")->where("id", $validated['coupon'])->first();
        if($find_coupon) {
            if($find_coupon->type === 'PHAN_TRAM') {
                $data_insert_order['total_apply_coupon'] = round($total_price - ( ($total_price * $find_coupon->fee) / 100 ));
            } else {
                $data_insert_order['total_apply_coupon'] = $total_price - $find_coupon->fee;
            }
        } else {
            $data_insert_order['total_apply_coupon'] =  $total_price;
        }
        $data_insert_order['total_amount'] = $data_insert_order['total_apply_coupon'] - (( $data_insert_order['total_apply_coupon'] * $data_insert_order['total_discount']) / 100);

        DB::beginTransaction();

        try {

            
            /**
             * Trường hợp nhận tại cửa hàng
             */
            if((int)$validated['client_request_transport']['type'] === 3) {
                $order_id = DB::table("orders")->insertGetId($data_insert_order);
                foreach($data_insert_order_items as $key => $item) {
                    $data_insert_order_items[$key]['order_id'] = $order_id;
                }

                DB::table("order_items")->insert($data_insert_order_items);

                $order_shipment_id = DB::table("order_shipments")->insertGetId([
                    'order_id' => $order_id,
                    'shipping_partner_id' => null,
                    'tracking_number' => null,
                    'current_status' => $data_insert_order['status'],
                    'shipping_fee' => 0,
                    'height' => 0,
                    'width' => 0,
                    'length' => 0,
                    'weight' => 0,
                    'cod' => 0,
                    'note' => null,
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => null,
                ]);
                DB::table("order_shipment_status_logs")->insert([
                    'order_shipment_id' => $order_shipment_id,
                    'status_code' =>  $data_insert_order['status'],
                    'status_text' => self::ORDER_STATUS_MESSAGE[$data_insert_order['status']],
                    'status_time' => date("Y-m-d H:i:s"),
                    'raw_payload' => null,
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => null,
                ]);

                DB::commit();
                return $this->successResponse(['link_redirect' => route('admin.order.detail', ['id' => $order_id])], 'Tạo đơn thành công');
            }

            /**
             * TH Vận chuyển ngoài (Gọi riêng shipper để ship đồ)
             */

            if((int)$validated['client_request_transport']['type'] === 2) {
                $order_id = DB::table("orders")->insertGetId($data_insert_order);
                foreach($data_insert_order_items as $key => $item) {
                    $data_insert_order_items[$key]['order_id'] = $order_id;
                }

                DB::table("order_items")->insert($data_insert_order_items);

                $order_shipment_id = DB::table("order_shipments")->insertGetId([
                    'order_id' => $order_id,
                    'shipping_partner_id' => $validated['client_request_transport']['shipping_partner_id'],
                    'tracking_number' => null,
                    'current_status' => $data_insert_order['status'],
                    'shipping_fee' => str_replace(".", "", $validated['client_request_transport']['shipping_fee']),
                    'height' => str_replace(".", "", $validated['client_request_transport']['height']),
                    'width' => str_replace(".", "", $validated['client_request_transport']['width']),
                    'length' => str_replace(".", "", $validated['client_request_transport']['length']),
                    'weight' => str_replace(".", "", $validated['client_request_transport']['gam']),
                    'cod' => str_replace(".", "", $validated['client_request_transport']['cod']),
                    'note' => $validated['client_request_transport']['require_transport_option'],
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => null,
                ]);
                DB::table("order_shipment_status_logs")->insert([
                    'order_shipment_id' => $order_shipment_id,
                    'status_code' =>  $data_insert_order['status'],
                    'status_text' => self::ORDER_STATUS_MESSAGE[$data_insert_order['status']],
                    'status_time' => date("Y-m-d H:i:s"),
                    'raw_payload' => null,
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => null,
                ]);

                DB::commit();
                return $this->successResponse(['link_redirect' => route('admin.order.detail', ['id' => $order_id])], 'Tạo đơn thành công');
            }

             /**
             * TH Đẩy hãng vận chuyển
            */
            if((int)$validated['client_request_transport']['type'] === 1){
                // đẩy đơn qua vận chuyển xong rồi lưu đơn hàng vào DB
                if($validated['client_request_transport']['shipping_partner_id'] === self::PARTNER['GHN']) {
                    $store = $this->_getStore($validated['store_id'], self::PARTNER['GHN']);

                    $ghn_service = new Ghn($store->_token, $store->shop_id, $store->api);
                    $ghn_service->setProvinceName($validated['customer']['province_text'])
                    ->setDistrictName($validated['customer']['district_text'])
                    ->setWardName($validated['customer']['ward_text'])
                    ->setDetailName($validated['customer']['address']);

                    $response = $ghn_service->createOrder([
                        'payment_type_id' => $validated['client_request_transport']['shipping_fee_payer'] === 'shop' ? 1 : 2,
                        'note' => $validated['client_request_transport']['note_transport'],
                        'required_note' => $validated['client_request_transport']['require_transport_option'],
                        'to_name' => $validated['customer']['full_name'],
                        'to_phone' => $validated['customer']['phone'],
                        'cod_amount' => (int)$validated['client_request_transport']['cod'],
                        "weight" => (int)$validated['client_request_transport']['gam'],
                        "length" => (int)$validated['client_request_transport']['length'],
                        "width" => (int)$validated['client_request_transport']['width'],
                        "height" => (int)$validated['client_request_transport']['height'],
                        "service_type_id" => (int) ($validated['client_request_transport']['gam'] > 0 ? $validated['client_request_transport']['gam'] : $calculate_weight) >= 20000 ? 5 : 2,
                        'items' => $items,
                        'client_order_code' => $data_insert_order['code'],
                    ]);

                    if(isset($response['order_code'])) {
                        $order_id = DB::table("orders")->insertGetId($data_insert_order);

                        foreach($data_insert_order_items as $key => $item) {
                            $data_insert_order_items[$key]['order_id'] = $order_id;
                        }
        
                        DB::table("order_items")->insert($data_insert_order_items);

                        $order_shipment_id = DB::table("order_shipments")->insertGetId([
                            'order_id' => $order_id,
                            'shipping_partner_id' => -1,
                            'tracking_number' => $response['order_code'],
                            'current_status' => $data_insert_order['status'],
                            'shipping_fee' => $response['total_fee'],
                            'height' => str_replace(".", "", $validated['client_request_transport']['height']),
                            'width' => str_replace(".", "", $validated['client_request_transport']['width']),
                            'length' => str_replace(".", "", $validated['client_request_transport']['length']),
                            'weight' => str_replace(".", "", $validated['client_request_transport']['gam']),
                            'cod' => str_replace(".", "", $validated['client_request_transport']['cod']),
                            'note' => $validated['client_request_transport']['require_transport_option'],
                            'created_at' => date("Y-m-d H:i:s"),
                            'updated_at' => null,
                        ]);

                        DB::table("order_shipment_status_logs")->insert([
                            'order_shipment_id' => $order_shipment_id,
                            'status_code' =>  $data_insert_order['status'],
                            'status_text' => self::ORDER_STATUS_MESSAGE[$data_insert_order['status']],
                            'status_time' => date("Y-m-d H:i:s"),
                            'raw_payload' => json_encode($response),
                            'created_at' => date("Y-m-d H:i:s"),
                            'updated_at' => null,
                        ]);

                        DB::commit();

                        return $this->successResponse(['link_redirect' => route('admin.order.detail', ['id' => $order_id])], 'Tạo đơn thành công');
                    }

                    return $this->errorReponse('Có lỗi vui lòng kiểm tra lại');
                } else if($validated['client_request_transport']['shipping_partner_id'] === self::PARTNER['GHTK']){
                    $get_list_pick_add = collect($this->gthk_list_pick_add)->keyBy('pick_address_id')->toArray();
                    $find_list_pick = $get_list_pick_add[$request->pick_address_id];

                    $ghtk_service = new Ghtk($this->config_ghtk->_token, $find_list_pick['pick_address_id'], $this->config_ghtk->api);
                    $ghtk_service->setProvinceName($validated['customer']['province_text'])
                    // ->setDistrictName($validated['customer']['district_text'])
                    ->setWardName($validated['customer']['ward_text'])
                    ->setDetailName($validated['customer']['address']);

                    $extract_address = $ghtk_service->extractAddressParts($find_list_pick['address']);

                    $response = $ghtk_service->createOrder([
                        "order" => [
                            "id" => $data_insert_order['code'],
                            "pick_name" => $find_list_pick['pick_name'],
                            "pick_address" => $find_list_pick['address'],
                            "pick_province" => $extract_address['province'],
                            "pick_district" => $extract_address['district'],
                            "pick_ward" => $extract_address['ward'],
                            "pick_tel" => $find_list_pick['pick_tel'],
                            "tel" => $validated['customer']['phone'],
                            "name" => $validated['customer']['full_name'],
                            "hamlet" => "Khác",
                            "is_freeship" => "1",
                            "pick_money" => (int)$validated['client_request_transport']['cod'],
                            "note" => $validated['client_request_transport']['note_transport'],
                            "value" => 10000,
                            "transport" => "road",
                        ],
                        "products" => $items_ghtk,
                    ]);

                    if($response['success'] && isset($response['order'])) {
                        $data_insert_order['status'] = $response['order']['status_id'];

                        $order_id = DB::table("orders")->insertGetId($data_insert_order);

                        foreach($data_insert_order_items as $key => $item) {
                            $data_insert_order_items[$key]['order_id'] = $order_id;
                        }
        
                        DB::table("order_items")->insert($data_insert_order_items);

                        $order_shipment_id = DB::table("order_shipments")->insertGetId([
                            'order_id' => $order_id,
                            'shipping_partner_id' => -2,
                            'tracking_number' => $response['order']['label'],
                            'current_status' => $data_insert_order['status'],
                            'shipping_fee' => $response['order']['fee'],
                            'height' => str_replace(".", "", $validated['client_request_transport']['height']),
                            'width' => str_replace(".", "", $validated['client_request_transport']['width']),
                            'length' => str_replace(".", "", $validated['client_request_transport']['length']),
                            'weight' => str_replace(".", "", $validated['client_request_transport']['gam']),
                            'cod' => str_replace(".", "", $validated['client_request_transport']['cod']),
                            'note' => $validated['client_request_transport']['require_transport_option'],
                            'created_at' => date("Y-m-d H:i:s"),
                            'updated_at' => null,
                        ]);

                        DB::table("order_shipment_status_logs")->insert([
                            'order_shipment_id' => $order_shipment_id,
                            'status_code' =>  $response['order']['status_id'],
                            'status_text' => $ghtk_service::STATUS_ORDER[$response['order']['status_id']],
                            'status_time' => date("Y-m-d H:i:s"),
                            'raw_payload' => json_encode($response),
                            'created_at' => date("Y-m-d H:i:s"),
                            'updated_at' => null,
                        ]);

                        DB::commit();

                        return $this->successResponse(['link_redirect' => route('admin.order.detail', ['id' => $order_id])], 'Tạo đơn thành công');
                    }
                }
            }



        } catch (\Throwable $th) {
            DB::rollback();
            throw $th;
            return $this->errorResponse('Có lỗi xảy ra, vui lòng kiểm tra lại');
        }


    }

    private function _getStore($store_id, $partner, $add_select = []){
        $data = DB::table("store_details")
                    ->join("tokens", "store_details.is_transport", "=", "tokens.is_transport")
                    ->where("store_details.store_id", $store_id)
                    ->where("store_details.is_transport",  $partner)
                    ->selectRaw("store_details.transport_id as shop_id, tokens.api, tokens._token");

        if(!empty($add_select) && is_array($add_select)) {
            $data->addSelect(implode(",", $add_select));
        }

        return $data->first();
    }

    public function createOrderOld(Request $request){
        $data_request = json_decode($request->data);

        $request_custom = new Request(json_decode(json_encode($data_request), true));
        $package_and_delivery = $data_request->package_and_delivery;
        $rules = [
            'store_id' => 'required|exists:stores,id',
            // CUSTOMER
            'customer.full_name' => 'required|string|max:255',
            'customer.phone' => 'required|string|max:15',
            'customer.address' => 'required|string',
            'customer.customer_id' => 'required|integer|exists:customers,id',
            'customer.schedule_delivery_date' => 'required|date|after_or_equal:today',
            'customer.source' => 'nullable|string|max:255',

            // PRODUCTS
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.discount' => 'nullable|numeric|min:0',
            'products.*.is_option' => 'required|in:1,2', // 1 = %, 2 = giá trị

            // RESULTS
            'results.note' => 'nullable|string',
            'results.discount_total' => 'required|numeric|min:0',
            'results.customer_has_paid_total' => 'required|numeric|min:0',

            // PACKAGE & DELIVERY
            'package_and_delivery.type' => 'required|in:1,2,3,4',
            'package_and_delivery.is_ship' => 'required',
        ];
        $messages = [
            // CUSTOMER
            'customer.full_name.required' => 'Tên khách hàng không được để trống.',
            'customer.full_name.string' => 'Tên khách hàng phải là chuỗi ký tự.',
            'customer.full_name.max' => 'Tên khách hàng không được vượt quá 255 ký tự.',
    
            'customer.phone.required' => 'Số điện thoại không được để trống.',
            'customer.phone.string' => 'Số điện thoại phải là chuỗi ký tự.',
            'customer.phone.max' => 'Số điện thoại không được vượt quá 15 ký tự.',
    
            'customer.address.required' => 'Địa chỉ không được để trống.',
            'customer.address.string' => 'Địa chỉ phải là chuỗi ký tự.',
    
            'customer.customer_id.required' => 'Mã khách hàng không được để trống.',
            'customer.customer_id.integer' => 'Mã khách hàng phải là số nguyên.',
            'customer.customer_id.exists' => 'Mã khách hàng không tồn tại.',
    
            'customer.schedule_delivery_date.required' => 'Ngày giao hàng không được để trống.',
            'customer.schedule_delivery_date.date' => 'Ngày giao hàng phải là định dạng ngày hợp lệ.',
            'customer.schedule_delivery_date.after_or_equal' => 'Ngày giao hàng phải bằng hoặc sau ngày hôm nay.',
    
            'customer.source.string' => 'Nguồn phải là chuỗi ký tự.',
            'customer.source.max' => 'Nguồn không được vượt quá 255 ký tự.',
    
            // PRODUCTS
            'products.required' => 'Danh sách sản phẩm không được để trống.',
            'products.array' => 'Danh sách sản phẩm phải là mảng.',
            'products.min' => 'Phải có ít nhất một sản phẩm trong danh sách.',
    
            'products.*.product_name.required' => 'Tên sản phẩm không được để trống.',
            'products.*.product_name.string' => 'Tên sản phẩm phải là chuỗi ký tự.',
            'products.*.product_name.max' => 'Tên sản phẩm không được vượt quá 255 ký tự.',
    
            'products.*.product_quantity.required' => 'Số lượng sản phẩm không được để trống.',
            'products.*.product_quantity.integer' => 'Số lượng sản phẩm phải là số nguyên.',
            'products.*.product_quantity.min' => 'Số lượng sản phẩm phải lớn hơn hoặc bằng 1.',
    
            'products.*.price.required' => 'Giá sản phẩm không được để trống.',
            'products.*.price.numeric' => 'Giá sản phẩm phải là số.',
            'products.*.price.min' => 'Giá sản phẩm phải lớn hơn hoặc bằng 0.',
    
            'products.*.discount.numeric' => 'Giảm giá phải là số.',
            'products.*.discount.min' => 'Giảm giá phải lớn hơn hoặc bằng 0.',
    
            'products.*.is_option.required' => 'Loại giảm giá không được để trống.',
            'products.*.is_option.in' => 'Loại giảm giá phải là 1 (phần trăm) hoặc 2 (giá trị).',
    
            // RESULTS
            'results.note.string' => 'Ghi chú phải là chuỗi ký tự.',
    
            'results.discount_total.required' => 'Tổng giảm giá không được để trống.',
            'results.discount_total.numeric' => 'Tổng giảm giá phải là số.',
            'results.discount_total.min' => 'Tổng giảm giá phải lớn hơn hoặc bằng 0.',
    
            'results.customer_has_paid_total.required' => 'Số tiền khách đã thanh toán không được để trống.',
            'results.customer_has_paid_total.numeric' => 'Số tiền khách đã thanh toán phải là số.',
            'results.customer_has_paid_total.min' => 'Số tiền khách đã thanh toán phải lớn hơn hoặc bằng 0.',
    
            // PACKAGE & DELIVERY
            'package_and_delivery.type' => 'Vui lòng chọn loại giao hàng',
            'package_and_delivery.is_ship' => 'Vui lòng chọn người vận chuyển hoặc hãng vận chuyển',
            'package_and_delivery.ship_id' => 'Vui lòng chọn đối tượng vận chuyển',
            'package_and_delivery.cod.required' => 'Phí thu hộ không được để trống.',
            'package_and_delivery.cod.numeric' => 'Phí thu hộ phải là số.',
            'package_and_delivery.cod.min' => 'Phí thu hộ phải lớn hơn hoặc bằng 0.',
    
            'package_and_delivery.gam.required' => 'Phí giao hàng không được để trống.',
            'package_and_delivery.gam.numeric' => 'Phí giao hàng phải là số.',
            'package_and_delivery.gam.min' => 'Phí giao hàng phải lớn hơn hoặc bằng 0.',
    
            'package_and_delivery.length.required' => 'Chiều dài không được để trống.',
            'package_and_delivery.length.numeric' => 'Chiều dài phải là số.',
            'package_and_delivery.length.min' => 'Chiều dài phải lớn hơn hoặc bằng 0.',
    
            'package_and_delivery.width.required' => 'Chiều rộng không được để trống.',
            'package_and_delivery.width.numeric' => 'Chiều rộng phải là số.',
            'package_and_delivery.width.min' => 'Chiều rộng phải lớn hơn hoặc bằng 0.',
    
            'package_and_delivery.height.required' => 'Chiều cao không được để trống.',
            'package_and_delivery.height.numeric' => 'Chiều cao phải là số.',
            'package_and_delivery.height.min' => 'Chiều cao phải lớn hơn hoặc bằng 0.',
    
            'package_and_delivery.require_transport_option.required' => 'Phương thức vận chuyển không được để trống.',
            'package_and_delivery.require_transport_option.in' => 'Phương thức vận chuyển không hợp lệ.',
    
            'package_and_delivery.payment_type_id.required' => 'Loại thanh toán không được để trống.',
            'package_and_delivery.payment_type_id.in' => 'Loại thanh toán phải là 1 (shop trả) hoặc 2 (khách trả).',
    
            'package_and_delivery.note_transport.string' => 'Ghi chú vận chuyển phải là chuỗi ký tự.',
    
            // STORE
            'store_id.required' => 'Mã cửa hàng không được để trống.',
            'store_id.integer' => 'Mã cửa hàng phải là số nguyên.',
            'store_id.exists' => 'Mã cửa hàng không tồn tại.',
    
            // RESPONSE TRANSPORT
            'response_transport.boolean' => 'Trạng thái phản hồi vận chuyển phải là kiểu boolean.',
        ];

        if(isset($package_and_delivery->type) === "1") {
            $rules = array_merge($rules, [
                'package_and_delivery.cod' => 'required|numeric|min:0',
                'package_and_delivery.gam' => 'required|numeric|min:0',
                'package_and_delivery.length' => 'required|numeric|min:0',
                'package_and_delivery.width' => 'required|numeric|min:0',
                'package_and_delivery.height' => 'required|numeric|min:0',
                'package_and_delivery.require_transport_option' => 'required|string|in:KHONGCHOXEMHANG,CHOXEMHANG,CHOXEMHANGKHONGTHU', // thêm nếu có nhiều giá trị hơn
                'package_and_delivery.payment_type_id' => 'required|in:1,2', // 1: shop trả, 2: khách trả
                'package_and_delivery.note_transport' => 'nullable|string',
            ]);
        } else if(isset($package_and_delivery->type) === "2"){
            $rules = array_merge($rules, [
                'package_and_delivery.cod' => 'required|numeric|min:0',
                'package_and_delivery.gam' => 'required|numeric|min:0',
                'package_and_delivery.length' => 'required|numeric|min:0',
                'package_and_delivery.width' => 'required|numeric|min:0',
                'package_and_delivery.height' => 'required|numeric|min:0',
                'package_and_delivery.require_transport_option' => 'required|string|in:KHONGCHOXEMHANG,CHOXEMHANG,CHOXEMHANGKHONGTHU', // thêm nếu có nhiều giá trị hơn
                'package_and_delivery.payment_type_id' => 'required|in:1,2', // 1: shop trả, 2: khách trả
                'package_and_delivery.note_transport' => 'nullable|string',
                'package_and_delivery.ship_id' => 'required',
            ]);
        }

        $validated = $request_custom->validate($rules, $messages);

        if(!$data_request) {
            return $this->errorResponse('Có lỗi vui lòng thử lại');
        }

        if(!isset($data_request->customer) || !isset($data_request->products) || !isset($data_request->package_and_delivery) || !isset($data_request->store_id)) {
            return $this->errorResponse('Có lỗi vui lòng thử lại');
        }

        $customer = $data_request->customer;
        $products = $data_request->products;
        $store_id = $data_request->store_id;
        $customer_payment = $data_request->results; # các trường như chiết khấu, khách đã trả
        // $response_transport = $data->response_transport->{$package_and_delivery->is_ship};
        $data = [];
        $data_send_api_transport = [];
        $transport = [];
        if($package_and_delivery->type === "1") {
            $transport = DB::table("tokens")->where("is_transport", $package_and_delivery->is_ship)->first();
            if(!$transport) {
                return $this->errorResponse('DVVC không tồn tại, vui lòng thử lại');
            }
    
            $store = DB::table("stores")->where("id", $store_id)->first();
            if(!$store) {
                return $this->errorResponse('Địa điểm lấy hàng không tồn tại');
            }
            $data_send_api_transport = [];
            if($data_request->is_transport === "GHN") {
                $store = DB::table("stores")
                ->join("store_details", "stores.id", "=", "store_details.store_id")
                ->where("id", $store_id)
                ->where("is_transport", "GHN")
                ->first();

                $from_shop = explode(",", $store->address);
                $from_shop_address = array_slice($from_shop, -3);
                $from_shop_address_detail = array_slice($from_shop, 0, count($from_shop) - 3);
        
                $to_customer = explode(",", $customer->address);
                $data_send_api_transport = [
                    'token' => $transport->_token,
                    'shop_id' => (int) $store->transport_id,
                    'from_phone' => $store->contact_phone,
                    'from_address' => $from_shop_address_detail[0],
                    'from_ward_name' => $from_shop_address[0],
                    'from_district_name' => $from_shop_address[1],
                    'from_province_name' => $from_shop_address[2],
                    'to_name' => $customer->full_name,
                    'to_phone' => $customer->phone,
                ];
        
                $to_customer = explode(",", $customer->address);
                $to_customer_address = array_slice($to_customer, -3);
                $to_customer_address_detail = array_slice($to_customer, 0, count($to_customer) - 3);
                $data_send_api_transport['to_address'] = $to_customer_address_detail[0];
                $data_send_api_transport['to_ward_name'] = $to_customer_address[0];
                $data_send_api_transport['to_district_name'] = $to_customer_address[1];
                $data_send_api_transport['to_province_name'] = $to_customer_address[2];
        
                $data_send_api_transport['return_phone'] = $store->contact_phone;
                $data_send_api_transport['return_address'] = $from_shop_address_detail[0];
                $data_send_api_transport['return_ward_name'] = $from_shop_address[0];
                $data_send_api_transport['return_district_name'] = $from_shop_address[1];
                $data_send_api_transport['return_province_name'] = $from_shop_address[2];
        
                $data_send_api_transport['cod_amount'] = (int) $package_and_delivery->cod;
                $data_send_api_transport['content'] = $package_and_delivery->note_transport;
                $data_send_api_transport['weight'] = (int) $package_and_delivery->gam;
                $data_send_api_transport['length'] = (int) $package_and_delivery->length;
                $data_send_api_transport['width'] = (int) $package_and_delivery->width;
                $data_send_api_transport['height'] = (int) $package_and_delivery->height;
                $data_send_api_transport['pick_station_id'] = null;
                $data_send_api_transport['insurance_value'] = 0;
                $data_send_api_transport['service_type_id'] = (int) $data_request->response_transport->data_send_get_fee->service_type_id;
                $data_send_api_transport['payment_type_id'] = (int) $package_and_delivery->payment_type_id;
                $data_send_api_transport['note'] = $package_and_delivery->note_transport;
                $data_send_api_transport['required_note'] = $package_and_delivery->require_transport_option;
                $data_send_api_transport['pick_shift'] = [];
                $data_send_api_transport['pickup_time'] = isset($customer->schedule_delivery_date) ? strtotime($customer->schedule_delivery_date) : strtotime(date("Y-m-d"));
                $data_send_api_transport['items'] = [];
            } else if($data_request->is_transport === "GHTK"){
                $store = DB::table("stores")
                ->join("store_details", "stores.id", "=", "store_details.store_id")
                ->where("id", $store_id)
                ->where("is_transport", "GHTK")
                ->first();

                $handle_address = $this->handleAddress($store->address);
                $handle_address_customer = $this->handleAddress($customer->address);

                $data_send_api_transport['order'] = [
                    'pick_name' => json_decode($store->response_transport)->pick_name,
                    'pick_money' => (int)$package_and_delivery->cod,
                    'pick_address_id' => json_decode($store->response_transport)->pick_address_id,
                    'pick_address' => $store->address,
                    'pick_province' => $handle_address[2],
                    'pick_district' => $handle_address[1],
                    'pick_ward' => $handle_address[0],
                    'pick_tel' => json_decode($store->response_transport)->pick_tel,

                    'name' => $customer->full_name,
                    'address' => $customer->address,
                    'province' =>  $handle_address_customer[2],
                    'district' =>  $handle_address_customer[1],
                    'ward' =>  $handle_address_customer[0],
                    'street' =>  $customer->address,
                    'hamlet' =>  'Khác',
                    'tel' => $customer->phone,
                    'note' => $package_and_delivery->note_transport,
                    'email' => DB::table("customers")->where("id", $customer->customer_id)->value('email'),

                    'return_name' => json_decode($store->response_transport)->pick_name,
                    'return_address' => $store->address,
                    'return_province' => $handle_address[2],
                    'return_district' =>  $handle_address[1],
                    'return_tel' => json_decode($store->response_transport)->pick_tel,
                    'return_email' => 'phatle1913@gmail.com',

                    'transport' => 'road',
                    'is_freeship' => 1,
                    'weight_option' => 'gram',
                    'total_weight' => (double)$package_and_delivery->gam,
                    'value' => 10000,
                ];
            }
        }

        $data_products = DB::table("products")
        ->leftJoin('categories', 'products.category_id', "=" , 'categories.id')
        ->selectRaw("products.*, categories.name as category_name")
        ->get()->keyBy('id')->toArray();

        $total_product = 0;
        $total_price = 0;
        $data_detail = [];
        foreach($products as $product) {
            $data_product = $data_products[$product->product_id];
            
            if($data_request->is_transport === "GHN"){
                $data_send_api_transport['items'][] = (object)[
                    'name' => $data_product->name,
                    'code' => $data_product->code,
                    'quantity' => (int) $product->quantity,
                    'price' => (int) $data_product->price,
                    'length' => (int) $data_product->length,
                    'width' => (int) $data_product->width,
                    'weight' => (int) $data_product->weight,
                    'height' => (int) $data_product->height,
                    'category' => (object) [
                        'level1' => $data_product->category_name,
                    ],
                ];
            } else if($data_request->is_transport === "GHTK"){
                $data_send_api_transport['products'][] = [
                    'name' => $data_product->name,
                    'product_code' => $data_product->code,
                    'quantity' => (int) $product->quantity,
                    'price' => (int) $data_product->price,
                    'weight' => (double) $data_product->weight,
                ];
            }
            
            $total_product += $product->quantity;
            $calculate_price_quantity = ($data_product->price * $product->quantity);
            $total_price_detail = 0;
            if($product->is_option === "1") { # giảm giá = GIÁ TRỊ
                $total_price_detail = $calculate_price_quantity - $product->discount;
                $total_price += $calculate_price_quantity - $product->discount;
            } else { # giảm giá = %
                $discount = ($calculate_price_quantity * $product->discount) / 100;
                $total_price_detail = $calculate_price_quantity - $discount;
                $total_price += $calculate_price_quantity - $discount;
            }

            $data_detail[] = [
                'product_id' => $product->product_id,
                'product_name' => $data_product->name,
                'quantity' => (int) $product->quantity,
                'price' => (int) $data_product->price,
                'is_discount' => $product->is_option,
                'discount' => (int) $product->discount,
                'total_price' => (int) $total_price_detail,
                'created_at' => date("Y-m-d H:i:s"),
            ];
            
        }


        // Đẩy qua vận chuyển xong -> tiến hành lưu dữ liệu vào Database
        $code_transport = null;
        $order_id = null;
        $data_create_order = [
            'code_transport' => $code_transport,
            'code_order' => self::generateCode(),
            'store_id' => $store_id,
            'customer_id' => $customer->customer_id,
            'full_name' => $customer->full_name,
            'phone' => $customer->phone,
            'address' => $customer->address,
            'total_product' => $total_product,
            'total_price' => $total_price,
            'total_discount' => $customer_payment->discount_total,
            'customer_paid_total' => $total_price - ($total_price * $customer_payment->discount_total / 100),
            'customer_has_paid_total' => (int) str_replace(".", "", $customer_payment->customer_has_paid_total),
            'user_create_order' => auth()->user()->id,
            'user_id' => auth()->user()->id,
            'source' => $customer->source,
            'delivery_date' => $customer->schedule_delivery_date,
            'create_date' => date("Y-m-d H:i:s"),
        ];

        $data_create_order['cod'] = (int) str_replace(".", "", $package_and_delivery->cod);
        $data_create_order['gam'] = (int) str_replace(".", "", $package_and_delivery->gam);
        $data_create_order['height'] = (int) str_replace(".", "", $package_and_delivery->height);
        $data_create_order['width'] = (int) str_replace(".", "", $package_and_delivery->width);
        $data_create_order['length'] = (int) str_replace(".", "", $package_and_delivery->length);
        $data_create_order['note_transport'] = $package_and_delivery->note_transport;
        $data_create_order['payer_fee'] = $package_and_delivery->payment_type_id;
        $data_create_order['require_transport_option'] = $package_and_delivery->require_transport_option;

        $data_create_order['delivery_method'] = $package_and_delivery->type;

        

        if($package_and_delivery->type === "1") {
            $data_create_order['delivery_method_fee'] = 0;
        } else if($package_and_delivery->type === "2"){
            $data_create_order['delivery_method_fee'] = (int) str_replace(".", "", $package_and_delivery->delivery_method_fee);
        } else {
            $data_create_order['delivery_method_fee'] = 0;
        }

        if($package_and_delivery->type == 1){
            $data_create_order['partner_transport_type'] = "DVVC";
        } else if($package_and_delivery->type == 2){
            $data_create_order['partner_transport_type'] = $package_and_delivery->is_ship;
            $data_create_order['partner_transport_id'] = $package_and_delivery->ship_id;
        } else {
            $data_create_order['partner_transport_type'] = null;
        }
        
        $data_create_order['note_order'] = $customer_payment->note;
        $data_create_order['created_at'] = date("Y-m-d H:i:s");

        if($package_and_delivery->type === "1") {
            if($data_request->is_transport === "GHN"){
                $response = Http::withHeaders([
                    'token' => $data_send_api_transport['token'],
                    'ShopId' => $data_send_api_transport['shop_id'],
                ])->post("{$transport->api}/shiip/public-api/v2/shipping-order/create", $data_send_api_transport); 
        
                if(!$response->successful()) {
                    throw \Illuminate\Validation\ValidationException::withMessages([$response->json()['code_message_value']]);
                }
        
                $data = $response->json();
                $code_transport = $data['data']['order_code'];
                $data_create_order['delivery_method_fee'] = $data['data']['total_fee'];
                $data_create_order['partner_transport_id'] = -1;
                $data_create_order['code_transport'] = $code_transport;
                $data_create_order['response_transport'] = json_encode($data['data']);
                
            } else if($data_request->is_transport === "GHTK"){
                $data_send_api_transport['order']['id'] = $data_create_order['code_order'];
                $response = Http::withHeaders([
                    'Token' => $transport->_token,
                ])->post("{$transport->api}/services/shipment/order", (object) $data_send_api_transport); 

                if(!$response->successful()) {
                    throw \Illuminate\Validation\ValidationException::withMessages([$response->json()['message']]);
                }
        
                $data = $response->json();

                if($data['success']) {
                    $code_transport = $data['order']['label'];
                    $data_create_order['delivery_method_fee'] = $data['order']['fee'];
                    $data_create_order['partner_transport_id'] = -2;
                    $data_create_order['code_transport'] = $code_transport;
                    $data_create_order['response_transport'] = json_encode($data['order']);
                } else {
                    throw \Illuminate\Validation\ValidationException::withMessages(["GHTK báo lỗi ".$data['message']]);
                }
            }
        }
        
        DB::beginTransaction();
        try {
            $order_id = DB::table("orders")->insertGetId($data_create_order);

            foreach($data_detail as $key => $item_detail) {
                $product_stock = DB::table("product_stocks")->where("product_id", $item_detail['product_id'])->first();
                if($product_stock){
                    $is_valid_available_qty = $product_stock->quantity_sold + $item_detail['quantity'];

                    if($is_valid_available_qty > $product_stock->available_quantity) {
                        DB::rollback();
                        return $this->errorResponse('Số lượng bán vượt quá số lượng có thể bán');
                    }
                }

                DB::table("product_stocks")->where("product_id", $item_detail['product_id'])->update(
                    [
                        'quantity_sold' =>  $is_valid_available_qty,
                    ]
                );
                $data_detail[$key]['order_id'] = $order_id;
            }
            DB::table("order_details")->insert($data_detail);

            DB::commit();

            return $this->successResponse(['link_redirect' => route('admin.order.detail', ['id' => $order_id])], 'Tạo đơn thành công');
        } catch (\Throwable $th) {
            DB::rollback();
            throw $th;
            return $this->errorResponse('Có lỗi xảy ra, vui lòng kiểm tra lại');
        }
    }

    public static function generateCode($length = 10) {
        do {
            $code = self::PREFIX_KEY_CODE . Str::upper(Str::random($length));
        } while (DB::table('orders')->where('code', $code)->exists());
    
        return $code;
    }

    public function getDataProduct(Request $request){
        if(!isset($request->search)) {
            $request->search = '';
        }
        $province_id = isset($request->province_id) ? $request->province_id : 0;
        $results = Product::with('category:id,name')
        ->leftJoin('fee_product_province', function($leftJoin)use($province_id){
            $leftJoin->on('products.id', '=', 'fee_product_province.product_id')->where("fee_product_province.province_id", $province_id);
        })
        ->where(function($q)use($request){
            $q->where("name", "like", "%{$request->search}%")
            ->orWhere("sku", "like", "%{$request->search}%");
        })
        ->selectRaw("products.*, IF(fee_product_province.id IS NULL, products.price, fee_product_province.fee) as price")
        ->paginate(15);
        
        return $this->successResponse($results, 'Lấy dữ liệu thành công');
    }

    private function _getProducts(){
        return DB::table("products")->get()->keyBy('id')->toArray();
    }

    public function apiGetFee(Request $request) {
        if(!$request->data) {
            return $this->errorResponse('Không có dữ liệu đặt hàng');
        }
        if(!$request->pick_address_id) {
            return $this->errorResponse('Chưa chọn cửa hàng');
        }
        if(!$request->customers) {
            return $this->errorResponse('Chưa chọn khách hàng');
        }

        $request_data = json_decode($request->data);
        $request_customers = json_decode($request->customers);

        if(!$request_customers->id) {
            return $this->errorResponse('Chưa chọn khách hàng');
        }

        if(empty($request_data)) {
            return $this->errorResponse('Không có dữ liệu đặt hàng');
        }

        // $transport_partner = DB::SELECT("
        //     SELECT store_details.*, tokens._token, tokens.api FROM store_details
        //     JOIN tokens ON store_details.is_transport = tokens.is_transport
        //     WHERE store_details.store_id = ?
        // ", [$request->store_id]);

        // if(empty($transport_partner)) {
        //     return $this->errorResponse('Cửa hàng này không có vận chuyển hợp lệ');
        // }

        $transport_partner = [
           (object) [
                'is_transport' => 'GHTK',
            ]
        ];

        $products = $this->_getProducts();
        $calculate_weight = 0;
        foreach($request_data as $prod) {
            $calculate_weight += $products[$prod->product_id]->weight * (int) $prod->quantity;
        }

        $client_reponse = [];

        $get_list_pick_add = collect($this->gthk_list_pick_add)->keyBy('pick_address_id')->toArray();

        foreach($transport_partner as $partner) {
            // $response_transport = json_decode($partner->response_transport);

            if($partner->is_transport === self::PARTNER['GHN']) {
                $ghn_service = new Ghn($partner->_token, $response_transport->_id, $partner->api);
                $ghn_service->setProvinceName($request_customers->province)
                ->setDistrictName($request_customers->district)
                ->setWardName($request_customers->ward)
                ->setDetailName($request_customers->address);

                $ghn_fee = $ghn_service->calculateFee([
                    'weight' => (int) ($request->weight > 0 ? $request->weight : $calculate_weight),
                    'service_type_id' => (int) ($request->weight > 0 ? $request->weight : $calculate_weight) >= 20000 ? 5 : 2,
                ]);

                $client_reponse[self::PARTNER['GHN']] = $ghn_fee;

            } else if($partner->is_transport === self::PARTNER['GHTK']){
                $find_pick = $get_list_pick_add[$request->pick_address_id];

                $ghtk_service = new Ghtk($this->config_ghtk->_token, $find_pick['pick_address_id'], $this->config_ghtk->api);

                $ghtk_service->setPickAddress($find_pick['address'])
                ->setProvinceName($request_customers->province)
                ->setWardName($request_customers->ward)
                ->setDetailName($request_customers->address);
                $ghtk_fee = $ghtk_service->calculateFee([
                    "weight" => (int) ($request->weight > 0 ? $request->weight : $calculate_weight),
                    "deliver_option" => "none",
                    "transport" => "road",
                ]);

                $client_reponse[self::PARTNER['GHTK']] = $ghtk_fee;
            }
        }

        return $this->successResponse($client_reponse, 'Lấy dữ liệu thành công');
    }

    public function apiGetFeeOld(Request $request) {
        if(!$request->data) {
            return $this->errorResponse('Không có dữ liệu đặt hàng');
        }
        if(!$request->store_id) {
            return $this->errorResponse('Chưa chọn cửa hàng');
        }
        if(!$request->customer_id) {
            return $this->errorResponse('Chưa chọn khách hàng');
        }

        $data = json_decode($request->data);

        $stores = DB::SELECT("SELECT * FROM store_details WHERE store_id = {$request->store_id}");
        $data_response = [];

        foreach($stores as $store){

            if($store->is_transport === "GHN") {
                // $customer = DB::SELECT("SELECT * FROM customers WHERE id = {$request->customer_id} LIMIT 1");
                // $customer = $customer[0];
                $customer_address_detail = $this->_getAddressCustomer($request->address);

                $calculate_weight = 0;
                $service_type_id = 2;

                $data_send_get_fee = [
                    "service_type_id" => 0,
                    "from_district_id" => json_decode($store->response_transport)->district_id,
                    "from_ward_code" =>  json_decode($store->response_transport)->ward_code,
                    "to_district_id" => $customer_address_detail['district_id'],
                    "to_ward_code" => $customer_address_detail['ward_code'],
                    "length" => isset($request->length) && !empty($request->length) ?  (int) $request->length : 30,
                    "width" => isset($request->width) && !empty($request->width) ? (int) $request->width : 40,
                    "height" => isset($request->height) && !empty($request->height) ? (int) $request->height : 20,
                    "insurance_value" => 0,
                    "coupon" =>  null,
                    "items" => [],
                ];

                foreach($data as $item) {
                    $product = DB::SELECT("SELECT length, width, height, weight, name FROM products WHERE id = {$item->product_id} LIMIT 1");
                    $product = $product[0];
                    $calculate_weight += $product->weight * (int) $item->quantity;

                    if($calculate_weight >= 20000) {
                        $service_type_id = 5;
                    }
                    $product->quantity = (int)$item->quantity;
                    $data_send_get_fee['items'][] = $product;
                }

                $data_send_get_fee["weight"] = isset($request->weight) && !empty($request->weight) ? (int) $request->weight : $calculate_weight;

                $available_services = $this->_getAvailableServices(json_decode($store->response_transport)->district_id, $customer_address_detail['district_id'], json_decode($store->response_transport)->_id);

                $service_id = collect($available_services)->keyBy('service_type_id')[$service_type_id]['service_id'];
                $data_send_get_fee['service_type_id'] = $service_type_id;
                $get_fee = $this->_getFee($data_send_get_fee, json_decode($store->response_transport)->_id);
                $get_leadtime = $this->_getLeadtime(json_decode($store->response_transport)->district_id, json_decode($store->response_transport)->ward_code, $customer_address_detail['district_id'], $customer_address_detail['ward_code'], $service_id, json_decode($store->response_transport)->_id);

                // $data = [
                //     'GHN' => [
                //         'fee' => $get_fee,
                //         'get_leadtime' => $get_leadtime,
                //     ],
                //     'data_send_get_fee' => $data_send_get_fee,
                // ];

                $data_response['GHN'] = [
                    'fee' => $get_fee,
                    'get_leadtime' => $get_leadtime,
                    'data_send_get_fee' => $data_send_get_fee,
                ];

            }

            if($store->is_transport === "GHTK"){
                $response_transport = json_decode($store->response_transport);
                $handle_address = $this->handleAddress($response_transport->address);
                $handle_address_customer = $this->handleAddress($request->address);

                $data_send_get_fee = [
                    'pick_address_id' => $response_transport->pick_address_id,
                    'pick_province' => $handle_address[2],
                    'pick_district' => $handle_address[1],
                    'pick_ward' => $handle_address[0],
                    'province' => $handle_address_customer[2],
                    'district' => $handle_address_customer[1],
                    'deliver_option' => 'none',
                    'transport' => 'road',
                ];

                $calculate_weight = 0;

                foreach(json_decode($request->data) as $item) {
                    $product = DB::SELECT("SELECT length, width, height, weight, name FROM products WHERE id = {$item->product_id} LIMIT 1");
                    $product = $product[0];
                    $calculate_weight += $product->weight * (int) $item->quantity;
                    $product->quantity = (int)$item->quantity;
                }

                $data_send_get_fee["weight"] = isset($request->weight) && !empty($request->weight) ? (int) $request->weight : $calculate_weight;

                $get_fee = $this->_getFeeGHTK($data_send_get_fee);

                $data_response['GHTK'] = [
                    'get_fee' => $get_fee,
                ];
            }
        }

        return $this->successResponse($data_response, 'Lấy dữ liệu thành công');
    }

    private function _getLeadtime($from_district_id, $from_ward_code, $to_district_id, $to_ward_code, $service_id, $shop_id){
        $data_token = DB::table("tokens")->where("is_transport", "GHN")->first();
        $response = Http::withHeaders([
            'token' => $data_token->_token,
            'shop_id' => $shop_id
        ])->post("{$data_token->api}/shiip/public-api/v2/shipping-order/leadtime", [
            "from_district_id" => $from_district_id,
            "from_ward_code" => $from_ward_code,
            "to_district_id" => $to_district_id,
            "to_ward_code" => $to_ward_code,
            "service_id" => $service_id,
        ]); 

        if(!$response->successful()) {
            throw \Illuminate\Validation\ValidationException::withMessages([$response->json()['code_message_value']]);
        }

        $data = $response->json();

        if(!isset($data['data'])){
            throw \Illuminate\Validation\ValidationException::withMessages(["Có lỗi vui lòng kiểm tra lại"]);
        }

        return $data['data'];
    }

    private function _getAvailableServices($from_district_id, $to_district_id, $shop_id){
        $data_token = DB::table("tokens")->where("is_transport", "GHN")->first();
        $response = Http::withHeaders([
            'token' => $data_token->_token,
        ])->post("{$data_token->api}/shiip/public-api/v2/shipping-order/available-services", [
            "shop_id" => $shop_id,
            "from_district" => $from_district_id,
            "to_district" => $to_district_id
        ]); 

        if(!$response->successful()) {
            throw \Illuminate\Validation\ValidationException::withMessages([$response->json()['code_message_value']]);
        }

        $data = $response->json();

        if(!isset($data['data'])){
            throw \Illuminate\Validation\ValidationException::withMessages(["Có lỗi vui lòng kiểm tra lại"]);
        }

        return $data['data'];
    }

    private function _getFee($data, $shop_id){
        $data_token = DB::table("tokens")->where("is_transport", "GHN")->first();
        $response = Http::withHeaders([
            'token' => $data_token->_token,
            'ShopId' => $shop_id,
        ])->post("{$data_token->api}/shiip/public-api/v2/shipping-order/fee", $data); 

        if(!$response->successful()) {
            throw \Illuminate\Validation\ValidationException::withMessages([$response->json()['code_message_value']]);
        }

        $data = $response->json();

        if(!isset($data['data'])){
            throw \Illuminate\Validation\ValidationException::withMessages(["Có lỗi vui lòng kiểm tra lại"]);
        }

        return $data['data'];
    }

    private function _getFeeGHTK($data){
        $data_token = DB::table("tokens")->where("is_transport", "GHTK")->first();
        $response = Http::withHeaders([
            'token' => $data_token->_token,
        ])->post("{$data_token->api}/services/shipment/fee", $data); 

        if(!$response->successful()) {
            throw \Illuminate\Validation\ValidationException::withMessages([$response->json()['code_message_value']]);
        }

        $data = $response->json();

        if(!isset($data['fee'])){
            throw \Illuminate\Validation\ValidationException::withMessages(["Có lỗi vui lòng kiểm tra lại"]);
        }

        return $data['fee'];
    }

    private function handleAddress($address){
        $explode = explode(",", $address);
        $count = count($explode);
        if($count < 4) {
            throw \Illuminate\Validation\ValidationException::withMessages(["Địa chỉ không hợp lệ"]);
        }

        $address_province = array_slice($explode, -3);
        return $address_province;
    }

    private function _getAddressCustomer($address){
        $data_token = DB::table("tokens")->where("is_transport", "GHN")->first();
        $data_province = $this->callApiGhn("{$data_token->api}/shiip/public-api/master-data/province", $data_token->_token);

        $explode = explode(",", $address);
        $count = count($explode);
        if($count < 4) {
            throw \Illuminate\Validation\ValidationException::withMessages(["Địa chỉ không hợp lệ"]);
        }

        $address_province = array_slice($explode, -3);
        $find_province = $this->_findDataAddress($data_province,  $address_province[2], 'ProvinceName');

        $data_distrcit = $this->callApiGhn("{$data_token->api}/shiip/public-api/master-data/district?province_id={$find_province['ProvinceID']}", $data_token->_token);
        $find_district = $this->_findDataAddress($data_distrcit,  $address_province[1], 'DistrictName');

        $data_ward = $this->callApiGhn("{$data_token->api}/shiip/public-api/master-data/ward?district_id={$find_district['DistrictID']}", $data_token->_token);
        $find_ward = $this->_findDataAddress($data_ward,  $address_province[0], 'WardName');

        return [
            'province_id' => $find_province['ProvinceID'],
            'district_id' => $find_district['DistrictID'],
            'ward_code' => $find_ward['WardCode'],
        ];
    }

    private function callApiGhn($api, $token){
        $response = Http::withHeaders([
            'token' => $token,
        ])->get($api); 

        if(!$response->successful()) {
            throw \Illuminate\Validation\ValidationException::withMessages([$response->json()['code_message_value']]);
        }

        $data = $response->json();

        if(!isset($data['data'])){
            throw new \ErrorException("Có lỗi vui lòng kiểm tra lại");
        }

        return $data['data'];
    }

    private function _findDataAddress($array, $string, $extension = ''){
        foreach($array as $index => $items) {
            if(!isset($items['NameExtension'])) {
                continue;
            }
            isset($items[$extension]) ?  $items['NameExtension'][] = $items[$extension] : $items['NameExtension'];
            foreach($items['NameExtension'] as $item) {
                if(mb_strtolower($item) === mb_trim(mb_strtolower($string))) {
                    return $array[$index];
                }
            }
        }

        return false;
    }
}
