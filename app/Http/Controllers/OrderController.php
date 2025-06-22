<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use App\Models\Transport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use DataTables;
class OrderController extends Controller
{
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
    private const TRANSPORT_TYPE = [
        ...Transport::ROLE_RENDER_BLADE,
        'DVVC' => 'Hãng vận chuyển',
    ];
    public function index(){
        $staffs = DB::table("users")
        ->selectRaw("id, full_name")
        ->get();

        return view("admin.order.index", [
            'staffs' => $staffs,
        ]);
    }

    public function getData(Request $request){
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
        ->editColumn('code_order', function($data){
            $link = route("admin.order.detail", ['id' => $data->id]);
            return "
                <div class='colorHeader fw-bold'>
                    <a href='$link' class='link-primary'>{$data->code_order}</a>
                </div>
            ";
        })
        ->editColumn('full_name', function($data){
            return "
                <div class='text-center'>{$data->full_name}</div>
            ";
        })
        ->editColumn('customer_paid_total', function($data){
            $customer_paid_total = number_format($data->customer_paid_total, 0, ',', '.');
    
            return "
                <div class='text-end'>{$customer_paid_total}</div>
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

    public function detail($id, Request $request){

        $detailsSub = DB::table('order_details')
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

        return view('admin.order.create', [
            'get_transport' => $get_transport,
            'get_store' => $get_store,
        ]);
    }
    public function getDataCustomer(Request $request){
        $results = Customer::selectRaw("*, CONCAT(address, ', ', ward_text, ', ', district_text, ', ', province_text) as full_address")
        ->where(function ($q) use ($request) {
            $q->where("full_name", "like", "%{$request->search}%")
              ->orWhere("phone", "like", "%{$request->search}%")
              ->orWhere("code", "like", "%{$request->search}%");
        })->paginate(15);
        
        return $this->successResponse($results, 'Lấy dữ liệu thành công');
    }


    public function createOrder(Request $request){
        $data_request = json_decode($request->data);

        $request_custom = new Request(json_decode(json_encode($data_request), true));
        $package_and_delivery = $data_request->package_and_delivery;
        $rules = [
            'store_id' => 'required|integer|exists:stores,id',
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
        } while (DB::table('orders')->where('code_order', $code)->exists());
    
        return $code;
    }

    public function getDataProduct(Request $request){
        $results = Product::with('productStock', 'category:id,name')->where(function($q)use($request){
            $q->where("name", "like", "%{$request->search}%")
            ->orWhere("sku", "like", "%{$request->search}%");
        })
        ->whereHas('productStock', function($q)use($request) {
            $q->where('store_id', $request->store_id);
        })
        ->paginate(15);
        
        return $this->successResponse($results, 'Lấy dữ liệu thành công');
    }

    public function apiGetFee(Request $request) {
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

            if($store->is_transport === "VTP") {

                $res_fee_vtp = $this->apiGetFeeViettelPost($request);

                $data_response['VTP'] = [
                    'get_fee' => $res_fee_vtp,
                ];
            }
        }

        return $this->successResponse($data_response, 'Lấy dữ liệu thành công');
    }


    public function apiGetFeeViettelPost(Request $request) {
        if(!$request->data) {
            return $this->errorResponse('Không có dữ liệu đặt hàng');
        }
        if(!$request->store_id) {
            return $this->errorResponse('Chưa chọn cửa hàng');
        }
        if(!$request->customer_id) {
            return $this->errorResponse('Chưa chọn khách hàng');
        }

        $store = DB::SELECT("SELECT * FROM store_details WHERE store_id = {$request->store_id} AND is_transport = 'VTP' LIMIT 1");
        $store = $store[0];

        $data_post = [
            "SENDER_ADDRESS"   => $store->address,
            "RECEIVER_ADDRESS" => $request->address,
            "PRODUCT_TYPE"     => "HH",
            "PRODUCT_WEIGHT"   => $request->weight ?? 0,
            "PRODUCT_PRICE"    => 0,
            "MONEY_COLLECTION" => "0",
            "PRODUCT_LENGTH"   => isset($request->length) && !empty($request->length) ?  (int) $request->length : 0,
            "PRODUCT_WIDTH"    => isset($request->width) && !empty($request->width) ?  (int) $request->width : 0,
            "PRODUCT_HEIGHT"   => isset($request->height) && !empty($request->height) ?  (int) $request->height : 0,
            "TYPE"             => 1
        ];

        $fee_vtp = $this->_getFeeVTP($data_post);

        return $fee_vtp;
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

    private function _getFeeVNPost($data, $shop_id) {
        
    }

    private function _getFeeVTP($data) {
        $data_token = DB::table("tokens")->where("is_transport", "VTP")->first();
        $response = Http::withHeaders([
            'token' => $data_token->_token,
        ])->post("{$data_token->api}/v2/order/getPriceAllNlp", $data);

        if(!$response->successful()) {
            throw \Illuminate\Validation\ValidationException::withMessages([$response->json()['code_message_value']]);
        }

        $data = $response->json();

        if($response->status() != 200){
            throw \Illuminate\Validation\ValidationException::withMessages(["Có lỗi vui lòng kiểm tra lại"]);
        }
        $result = $data['RESULT'];

        $fee_vtp = array_map(function ($item) {
            $info_time          = explode(' ',$item['THOI_GIAN']);
            $hour               = $info_time[0];
            $text_hour          = $info_time[1];
            $day                = floor($hour / 24);
            $hour               = $hour % 24;
            $text_delivery_time = $day > 0 ? "$day ngày $hour $text_hour" : $item['THOI_GIAN'];

            return [
                'service_id'    => $item['MA_DV_CHINH'],     // "MA_DV_CHINH"    
                'service_name'  => $item['TEN_DICHVU'],      // "TEN_DICHVU"     
                'service_cost'  => $item['GIA_CUOC'],        // "GIA_CUOC"       
                'delivery_time' => $text_delivery_time,       // "THOI_GIAN"      
                'weight'        => $item['EXCHANGE_WEIGHT']  // "EXCHANGE_WEIGHT"
            ];
        }, $result);

        return $fee_vtp;
    }
}
