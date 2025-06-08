<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Store;
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
    ];
    private const TRANSPORT_TYPE = [
        ...Transport::ROLE_RENDER_BLADE,
        'DVVC' => 'Hãng vận chuyển',
    ];
    public function index(){
        return view("admin.order.index");
    }

    public function getData(Request $request){
        $start = $request->get('start', 0);
        $query = DB::table("orders")
        ->leftJoin('customers', 'orders.customer_id', '=', 'customers.id')
        ->leftJoin('users', 'orders.user_id', '=', 'users.id')
        ->leftJoin('transports', function($leftJoin){
            $leftJoin->on("orders.partner_transport_id", "=", "transports.id");
            $leftJoin->whereIn('orders.partner_transport_type', ['SHIPPER', 'CHANH_XE']);
        })
        ->selectRaw("orders.*, customers.full_name as customer_full_name, users.full_name as full_name_action, transports.full_name as partner_transport_full_name")
        ->orderBy("create_date", "DESC");

        $datatables = DataTables::query($query)
        ->addColumn('no', function($data)use(&$start){
            $stt = ++$start;
            return "<div class='colorHeader'>{$stt}</div>";
        })
        ->addColumn('col_1', function($data){
            $delivery_date = date("d/m/Y", strtotime($data->delivery_date));
            $create_date = date("d/m/Y H:i:s", strtotime($data->create_date));
            $customer_full_name = $data->customer_full_name;
            $full_name = $data->full_name;

            return "
                <div class='colorHeader text-center'>{$delivery_date}</div>
                <div class='colorHeader text-center'>{$create_date}</div>
                <div class='colorHeader text-center'>{$customer_full_name}</div>
                <div class='colorHeader text-center'>{$full_name}</div>
            ";
        })
        ->addColumn('col_2', function($data){
            $code_transport = $data->code_transport ? $data->code_transport : "X";
            $delivery_method = self::DELIVERY_METHOD[$data->delivery_method];

            return "
                <div class='colorHeader fw-bold'>{$data->code_order}</div>
                <div class='colorHeader fw-bold'>{$code_transport}</div>
                <div class='colorHeader fw-bold text-decoration-underline'>{$delivery_method}</div>
                <div class='colorHeader '>{$data->address}</div>
            ";
        })
        ->addColumn('col_3', function($data){
            $total_product = number_format($data->total_product, 0, ',', '.');
            $total_price = number_format($data->total_price, 0, ',', '.');
            $total_discount = number_format($data->total_discount, 0, ',', '.');
            return "
                <div class='colorHeader text-end'>{$total_product}</div>
                <div class='colorHeader text-end'>{$total_price}</div>
                <div class='colorHeader text-end'>{$total_discount}%</div>
            ";
        })
        ->addColumn('col_4', function($data){
            $customer_paid_total = number_format($data->customer_paid_total, 0, ',', '.');
            $customer_has_paid_total = number_format($data->customer_has_paid_total, 0, ',', '.');

            $total = number_format((int) $data->customer_paid_total - (int) $data->customer_has_paid_total, 0, ',', '.');
            return "
                <div class='colorHeader text-end'>{$customer_paid_total}</div>
                <div class='colorHeader text-end'>{$customer_has_paid_total}</div>
                <div class='colorHeader text-end'>{$total}</div>
            ";
        })
        ->addColumn('col_5', function($data){
            $partner_transport_type = $data->partner_transport_type ? self::TRANSPORT_TYPE[$data->partner_transport_type] : "X";

            if($data->partner_transport_type === "DVVC"){
                $partner_transport_id = self::GUESS_TRANSPORT[$data->partner_transport_id];
            } else if($data->partner_transport_type !== "DVVC" && !is_null($data->partner_transport_type)){
                $partner_transport_id = $data->partner_transport_full_name ? $data->partner_transport_full_name : "X";
            } else {
                $partner_transport_id = "X";
            }

            $delivery_method_fee = number_format((int) $data->delivery_method_fee, 0, ',', '.');
            $payer_fee = $data->payer_fee ? self::PAYER_FEE[$data->payer_fee] : "X";
            $cod = number_format((int) $data->cod, 0, ',', '.');

            return "
                <div class='colorHeader text-center'>{$partner_transport_type}</div>
                <div class='colorHeader text-center'>{$partner_transport_id}</div>
                <div class='colorHeader text-end'>{$delivery_method_fee}</div>
                <div class='colorHeader text-center'>{$payer_fee}</div>
                <div class='colorHeader text-end'>{$cod}</div>
            ";
        })
        ->addColumn('col_6', function($data){
            $units = "Cao: {$data->height}(cm) - Rộng: {$data->width}(cm) - Dài: {$data->length}(cm) - Trọng lượng: {$data->gam}(g)";
            $note_order = $data->note_order ? $data->note_order : "X";
            $note_transport = $data->note_transport ? $data->note_transport : "X";

            return "
                <div class='colorHeader text-center'<h5><span class='shadow-sm p-2 mb-2 rounded badge badge-warning-lighten'>Chờ GHN</span></h5></div>
                <div class='colorHeader text-center'>{$units}</div>
                <div class='colorHeader text-start'>{$note_order }</div>
                <div class='colorHeader text-start'>{$note_transport}</div>
            ";
        })
        ->addColumn('col_7', function($data){
            $created_at = date("d/m/Y", strtotime($data->created_at));
            $updated_at = $data->updated_at ? date("d/m/Y", strtotime($data->updated_at)) : "X";

            return "
                <div class='colorHeader text-center'>{$created_at}</div>
                <div class='colorHeader text-center'>{$updated_at}</div>
                <div class='colorHeader text-center'>{$data->full_name_action}</div>
            ";
        })
        ->addColumn('col_8', function($data){
            return "
                <a href='javascript:void(0);' class='action-icon'> <i class='mdi mdi-eye'></i></a>
                <a href='javascript:void(0);' class='action-icon'> <i class='mdi mdi-delete'></i></a>
            ";
        })
        ->rawColumns(['no', 'col_1', 'col_2', 'col_3', 'col_4', 'col_5', 'col_6', 'col_7', 'col_8']);
        return $datatables->toJson();
    }

    public function detail(){
        return view("admin.order.detail");
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
        $results = Customer::where(function ($q) use ($request) {
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

        if($package_and_delivery->type === "1") {
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
        } else if($package_and_delivery->type === "2"){
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
        if($package_and_delivery->type === "1") {
            $transport = DB::table("tokens")->where("is_transport", $package_and_delivery->is_ship)->first();
            if(!$transport) {
                return $this->errorResponse('DVVC không tồn tại, vui lòng thử lại');
            }
    
            $store = DB::table("stores")->where("id", $store_id)->first();
            if(!$store) {
                return $this->errorResponse('Địa điểm lấy hàng không tồn tại');
            }
    
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

        if($package_and_delivery->type === "1") {
            $response = Http::withHeaders([
                'token' => $data_send_api_transport['token'],
                'ShopId' => $data_send_api_transport['shop_id'],
            ])->post("{$transport->api}/shiip/public-api/v2/shipping-order/create", $data_send_api_transport); 
    
            if(!$response->successful()) {
                throw \Illuminate\Validation\ValidationException::withMessages([$response->json()['code_message_value']]);
            }
    
            $data = $response->json();
        }
        // Đẩy qua vận chuyển xong -> tiến hành lưu dữ liệu vào Database
        $data_create_order = [
            'code_transport' => $data ? $data['data']['order_code'] : null,
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
            $data_create_order['delivery_method_fee'] = $data['data']['total_fee'];
        } else if($package_and_delivery->type === "2"){
            $data_create_order['delivery_method_fee'] = (int) str_replace(".", "", $package_and_delivery->delivery_method_fee);
        } else {
            $data_create_order['delivery_method_fee'] = 0;
        }

        if($package_and_delivery->type == 1){
            $data_create_order['partner_transport_type'] = "DVVC";
            $data_create_order['partner_transport_id'] = self::GUESS_TRANSPORT[$package_and_delivery->is_ship];
        } else if($package_and_delivery->type == 2){
            $data_create_order['partner_transport_type'] = $package_and_delivery->is_ship;
            $data_create_order['partner_transport_id'] = $package_and_delivery->ship_id;
        } else {
            $data_create_order['partner_transport_type'] = null;
        }
        
        $data_create_order['note_order'] = $customer_payment->note;
        $data_create_order['created_at'] = date("Y-m-d H:i:s");
        
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

            return $this->successResponse([], 'Tạo đơn thành công');
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

        $store = DB::SELECT("SELECT * FROM stores WHERE id = {$request->store_id} LIMIT 1");
        $store = $store[0];
        // $customer = DB::SELECT("SELECT * FROM customers WHERE id = {$request->customer_id} LIMIT 1");
        // $customer = $customer[0];
        $customer_address_detail = $this->_getAddressCustomer($request->address, $store);

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

        $data = [
            'GHN' => [
                'fee' => $get_fee,
                'get_leadtime' => $get_leadtime,
            ],
            'data_send_get_fee' => $data_send_get_fee,
        ];

        return $this->successResponse($data, 'Lấy dữ liệu thành công');
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
