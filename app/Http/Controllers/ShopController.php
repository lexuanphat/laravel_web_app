<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShopValidateRequest;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class ShopController extends Controller
{
    public function index(){
        return view('admin.shop.index');
    }

    public function getData(Request $request) {
        $search = isset($request->search) && !empty($request->search) ? $request->search : "";
        $search = ltrim($search, '?');
        parse_str($search, $parsed);

        $model = Store::with('user:id,full_name');

        if(auth()->user()->role !== User::ROLE_ACCESS_PAGE['admin']) {
            $model = $model->where("id", auth()->user()->store_id);
        }

        $model = $model->orderBy('id', 'desc');

        if(isset($parsed['search'])) {
            $model->where("name", "LIKE" , "%".trim($parsed['search'])."%");
        }

        $datatables = DataTables::eloquent($model)
        ->order(function($query){
            if(request()->has('order')) {
                $query->orderBy('name', request()->order[0]['dir']);
            }
        })
        ->addIndexColumn()
        ->addColumn(
            'action', function($store){
                $view_loading = view("admin._partials.loading");
                $action_edit = route('admin.shop.detail', ['id' => $store->id]);
                $action_delete = route('admin.shop.delete', ['id' => $store->id]);

                if(auth()->user()->role !== User::ROLE_ACCESS_PAGE['admin']) {
                    return "X";
                }

                return "
                    <div class='button-list'>
                        <button class='btn btn-warning edit-record' data-action='{$action_edit}' data-record='{$store->id}'><i class='ri-edit-box-fill'></i>{$view_loading}</button>
                        <button class='btn btn-danger remove-record' data-action='{$action_delete}' data-record='{$store->id}'><i class='ri-delete-bin-fill'></i></button>
                    </div>
                ";
            }
        )
        ->addColumn('date_action', function($store){
            $created_at = $store->created_at ? date("d/m/Y H:i", strtotime($store->created_at)) : 'X';
            $updated_at = $store->updated_at ? date("d/m/Y H:i", strtotime($store->updated_at)) : 'X';
            $user_action = $store->user->full_name;
            return "
                <div>{$created_at}</div>
                 <div>{$updated_at}</div>
                 <div>{$user_action}</div>
            ";
        })
        ->rawColumns(['action', 'name', 'date_action']);
        return $datatables->toJson();
    }

    public function AsyncStoreTransport(Request $request){
        $data_tokens = DB::table("tokens")->get()->keyBy('is_transport')->toArray();
        // $stores_database = DB::table("stores")->get()->keyBy('transport_id');
        $user_id = auth()->user()->id;
        if(isset($data_tokens[$request->type_transport])) {
            $data_tokens = $data_tokens[$request->type_transport];
            if($request->type_transport === 'GHN') {
                $stores = $this->_apiAddress("{$data_tokens->api}/shiip/public-api/v2/shop/all", $data_tokens->_token);
                $store_created = 0;
                if($stores['shops']) {
                    $districts = null;
                    $provinces = null;
                    foreach($stores['shops'] as $store) {
                        if(is_null($districts)){
                            $districts = $this->_apiAddress("{$data_tokens->api}/shiip/public-api/master-data/district", $data_tokens->_token);
                        }
                        $find_district = null;
                        foreach($districts as $district) {
                            if($district['DistrictID'] === $store['district_id']) {
                                $find_district = $district;
                                break;
                            }
                        }
                        $wards = $this->_apiAddress("{$data_tokens->api}/shiip/public-api/master-data/ward?district_id={$store['district_id']}", $data_tokens->_token);
                        $find_ward = null;
                        foreach($wards as $ward) {
                            if($ward['WardCode'] === $store['ward_code']) {
                                $find_ward = $ward;
                                break;
                            }
                        }
                        if(is_null($provinces)){
                            $provinces = $this->_apiAddress("{$data_tokens->api}/shiip/public-api/master-data/province", $data_tokens->_token);
                        }
                        $find_province = null;
                        foreach($provinces as $province) {
                            if($province['ProvinceID'] === $find_district['ProvinceID']) {
                                $find_province = $province;
                                break;
                            }
                        }

                        $find_store = DB::table("stores")
                        ->where("name", $store['name'])
                        ->where("contact_phone", $store['phone'])
                        ->first();

                        if($find_store) {
                            $find_store_detail = DB::table("store_details")
                            ->where("store_id", $find_store->id)->where("is_transport", "GHN")->first();
                            
                            if($find_store_detail) {
                                DB::table("store_details")
                                ->where("store_id", $find_store->id)
                                ->where("is_transport", "GHN")
                                ->update([
                                    'address' => "{$store['address']}, {$find_ward['WardName']}, {$find_district['DistrictName']}, {$find_province['ProvinceName']}",
                                    'response_transport' => json_encode($store),
                                ]);
                            } else {
                                DB::table("store_details")->insert([
                                    'store_id' => $find_store->id,
                                    'address' => "{$store['address']}, {$find_ward['WardName']}, {$find_district['DistrictName']}, {$find_province['ProvinceName']}",
                                    'transport_id' => $store['_id'],
                                    'response_transport' => json_encode($store),
                                    'is_transport' => 'GHN',
                                ]);
                            }

                            DB::table("stores")
                            ->where("id", $find_store->id)
                            ->update(
                                [
                                    'name' => $store['name'],
                                    'contact_phone' => $store['phone'],
                                    'user_id' => $user_id,
                                    'updated_at' => date("Y-m-d H:i:s"),
                                    'address' => "{$store['address']}, {$find_ward['WardName']}, {$find_district['DistrictName']}, {$find_province['ProvinceName']}",
                                ]
                            );

                        } else {
                            $id = DB::table("stores")->insertGetId([
                                'name' => $store['name'],
                                'contact_phone' => $store['phone'],
                                'user_id' => $user_id,
                                'created_at' => date("Y-m-d H:i:s"),
                                'updated_at' => null,
                                'address' => "{$store['address']}, {$find_ward['WardName']}, {$find_district['DistrictName']}, {$find_province['ProvinceName']}",
                            ]);

                            DB::table("store_details")->insert([
                                'store_id' => $id,
                                'address' => "{$store['address']}, {$find_ward['WardName']}, {$find_district['DistrictName']}, {$find_province['ProvinceName']}",
                                'transport_id' => $store['_id'],
                                'response_transport' => json_encode($store),
                                'is_transport' => 'GHN',
                            ]);
                        }
                        $store_created++;
                    }

                    return $this->successResponse([], "Cập nhật thành công {$store_created} cửa hàng GHN");
                }
            }
            else if($request->type_transport === 'GHTK'){
                $stores = $this->_apiAddress("{$data_tokens->api}/services/shipment/list_pick_add", $data_tokens->_token);
                $store_created = 0;
                if($stores && !empty($stores)) {
                    foreach($stores as $store) {

                        $find_store = DB::table("stores")
                        ->where("name", $store['pick_name'])
                        ->where("contact_phone", $store['pick_tel'])
                        ->first();

                        if($find_store) {
                            $find_store_detail = DB::table("store_details")
                            ->where("store_id", $find_store->id)->where("is_transport", "GHTK")->first();
                            
                            if($find_store_detail) {
                                DB::table("store_details")
                                    ->where("store_id", $find_store->id)
                                    ->where("is_transport", "GHTK")
                                    ->update([
                                        'address' =>  $store['address'],
                                        'response_transport' => json_encode($store),
                                    ]);
                            } else {
                                DB::table("store_details")->insert([
                                    'store_id' => $find_store->id,
                                    'address' =>  $store['address'],
                                    'transport_id' => $store['pick_address_id'],
                                    'response_transport' => json_encode($store),
                                    'is_transport' => 'GHTK',
                                ]);
                            }

                            DB::table("stores")
                            ->where("id", $find_store->id)->update(
                                [
                                    'name' => $store['pick_name'],
                                    'contact_phone' => $store['pick_tel'],
                                    'user_id' => $user_id,
                                    'updated_at' => date("Y-m-d H:i:s"),
                                    'address' =>  $store['address'],
                                ]
                            );

                        } else {
                            $id = DB::table("stores")->insertGetId([
                                'name' => $store['pick_name'],
                                'contact_phone' => $store['pick_tel'],
                                'user_id' => $user_id,
                                'created_at' => date("Y-m-d H:i:s"),
                                'updated_at' => null,
                                'address' =>  $store['address'],
                            ]);

                            DB::table("store_details")->insert([
                                'store_id' => $id,
                                'address' =>  $store['address'],
                                'transport_id' => $store['pick_address_id'],
                                'response_transport' => json_encode($store),
                                'is_transport' => 'GHTK',
                            ]);
                        }
                        $store_created++;
                    }

                    return $this->successResponse([], "Cập nhật thành công {$store_created} cửa hàng GHTK");
                }
            }
        }
    }

    public function store(ShopValidateRequest $request) {
        $validated = $request->validated();
        $validated['user_id'] = auth()->user()->id;
        $validated['created_at'] = date("Y-m-d H:i:s");
        $validated['updated_at'] = null;
        $this->createShopTransport($validated);
        Store::create($validated);

        return $this->successResponse([], 'Thêm mới thành công');
    }

    public function detail($id, Request $request){
        $data = Store::find($id);

        if(!$data) {
            return $this->errorResponse('Không tìm thấy dữ liệu', 404);
        }

        return $this->successResponse($data, 'Thành công');
    }

    public function update($id, ShopValidateRequest $request) {
        $data = Store::find($id);

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
        $data = Store::find($id);

        if(!$data) {
            return $this->errorResponse('Không tìm thấy dữ liệu, vui lòng F5 thử lại', 404);
        }
        $data->delete();
        return $this->successResponse($data, 'Đã xoá dữ liệu');
    }

    private function createShopTransport($data_request){
        $validator = Validator::make([], []);
        $explode = explode(",", $data_request['address']);
        $count = count($explode);
        if($count < 4) {
            $validator->errors()->add('address', 'Địa chỉ không hợp lệ hoặc không thể tạo transport.');
            throw new \Illuminate\Validation\ValidationException($validator);
        }
        $address_province = array_slice($explode, -3);
        $address_detail = array_slice($explode, 0, $count - 3);
        $data_request['address_detail'] = implode(", ", $address_detail);
        
        $data_token_transport = DB::SELECT("
            SELECT * FROM tokens
        ");

        if(!$data_token_transport) {
            $validator->errors()->add('address', 'Vui lòng cấu hình Token API GHN');
            throw new \Illuminate\Validation\ValidationException($validator);
        }

        foreach($data_token_transport as $transport) {
            switch ($transport->is_transport) {
                case 'GHN':
                    $provinces = $this->_apiAddress("$transport->api/shiip/public-api/master-data/province", $transport->_token);
                    $find_province = $this->_findDataAddress($provinces, $address_province[2], 'ProvinceName');
                    $districts = $this->_apiAddress("{$transport->api}/shiip/public-api/master-data/district?province_id={$find_province['ProvinceID']}", $transport->_token);
                    $find_district = $this->_findDataAddress($districts, $address_province[1], 'DistrictName');
                    $wards = $this->_apiAddress("{$transport->api}/shiip/public-api/master-data/ward?district_id={$find_district['DistrictID']}", $transport->_token);
                    $find_ward = $this->_findDataAddress($wards, $address_province[0], 'WardName');

                    if($find_province && $find_district && $find_ward) {

                        $data_request['find_province'] = $find_province['ProvinceID'];
                        $data_request['find_district'] = $find_district['DistrictID'];
                        $data_request['find_ward'] = $find_ward['WardCode'];

                        $this->_apiRegisterShop("{$transport->api}/shiip/public-api/v2/shop/register", $transport->_token, $data_request);
                    }
                    break;
                
                default:
                    # code...
                    break;
            }
        }
    }

    private function _findDataAddress($data, $province, $extension = ''){
        foreach($data as $index => $items) {
            if(!isset($items['NameExtension'])) {
                continue;
            }

            isset($items[$extension]) ?  $items['NameExtension'][] = $items[$extension] : $items['NameExtension'];

            foreach($items['NameExtension'] as $item) {
                if(mb_strtolower($item) === mb_trim(mb_strtolower($province))) {
                    return $data[$index];
                }
            }
        }

        return false;
    }

    private function _apiAddress($api, $token){
        $validator = Validator::make([], []);

        $response = Http::timeout(10)->withHeaders([
            'token' => $token,
        ])->get($api);

        if($response->successful()) { 
            $data = $response->json();
            if (isset($data['code']) && $data['code'] == 200) {
                return $data['data'];
            } else {
                $validator->errors()->add('address', 'GHN API trả về lỗi: ' . ($data['message'] ?? 'Không rõ lỗi'));
                throw new \Illuminate\Validation\ValidationException($validator);
            }
        }else {
            $validator->errors()->add('address', 'Không thể kết nối tới GHN API');
            throw new \Illuminate\Validation\ValidationException($validator);
        }
    }

    private function _apiRegisterShop($api, $token, $params){
        $validator = Validator::make([], []);

        $response = Http::withHeaders([
            'token' => $token,
        ])->post($api, [
            "district_id" => $params['find_district'],
            "ward_code" => $params['find_ward'],
            "name" =>  $params['name'],
            "phone" => $params['contact_phone'],
            "address" => $params['address_detail'],
        ]);

        if($response->failed()) {
            $errors =  $response->json();
            $messages = 'GHN API trả về lỗi: ';
            $key = "address";
            if($errors['code_message'] === "PHONE_INVALID") {
                $key = "contact_phone";
                $messages .= $errors['code_message_value'];
            }
            $validator->errors()->add($key, $messages);
            throw new \Illuminate\Validation\ValidationException($validator);
        }

        if($response->successful()) { 
            $data = $response->json();
            if (isset($data['code']) && $data['code'] == 200) {
                return $data['data'];
            } else {
                $validator->errors()->add('address', 'GHN API trả về lỗi: ' . ($data['message'] ?? 'Không rõ lỗi'));
                throw new \Illuminate\Validation\ValidationException($validator);
            }
        }else {
            $validator->errors()->add('address', 'Không thể kết nối tới GHN API');
            throw new \Illuminate\Validation\ValidationException($validator);
        }
    }
}
