<?php

namespace App\Services\Shipping;
use Illuminate\Support\Facades\Http;

class Ghtk extends Base {
    private $_token;
    private $_pick_address_id;
    private $_base_url;

    private $_province;
    private $_district;
    private $_ward;
    private $_detail;

    private $_pick_province_name;
    private $_pick_district_name;
    private $_pick_ward_name;
    private $_pick_address_name;

    const STATUS_ORDER = [
        -1 => "Hủy đơn hàng",
        1 => "Chưa tiếp nhận",
        2 => "Đã tiếp nhận",
        3 => "Đã lấy hàng/Đã nhập kho",
        4 => "Đã điều phối giao hàng/Đang giao hàng",
        5 => "Đã giao hàng/Chưa đối soát",
        6 => "Đã đối soát",
        7 => "Không lấy được hàng",
        8 => "Hoãn lấy hàng",
        9 => "Không giao được hàng",
        10 => "Delay giao hàng",
        11 => "Đã đối soát công nợ trả hàng",
        12 => "Đã điều phối lấy hàng/Đang lấy hàng",
        13 => "Đơn hàng bồi hoàn",
        20 => "Đang trả hàng (COD cầm hàng đi trả)",
        21 => "Đã trả hàng (COD đã trả xong hàng)",
        123 => "Shipper báo đã lấy hàng",
        127 => "Shipper (nhân viên lấy/giao hàng) báo không lấy được hàng",
        128 => "Shipper báo delay lấy hàng",
        45 => "Shipper báo đã giao hàng",
        49 => "Shipper báo không giao được giao hàng",
        410 => "Shipper báo delay giao hàng",
    ];

    public function __construct(string $token, string $pick_address_id, string $baseUrl = null)
    {
        $this->_token = $token;
        $this->_pick_address_id = $pick_address_id;
        $this->_base_url = $baseUrl ?? 'https://services-staging.ghtklab.com';
    }

    protected function headers()
    {
        return [
            'Token' => $this->_token,
            'Content-Type' => 'application/json',
        ];
    }

    private function _request($method, $endpoint, $data = []){
        $headers = $this->headers();

        $response = Http::withHeaders($headers)->$method("{$this->_base_url}{$endpoint}", $data);

        if ($response->successful() && $response->json()['success'] === true) {
            return $response->json()['data'] ?? $response->json();
        }

        $message = "GHTK API báo lỗi: ";
        $response_json = $response->json();
        
        if($response_json['message']) {
            $message .= $response_json['message'];
        } else {
            $message .= "Vui lòng kiểm tra lại";
        }

        throw new \Exception($message);
    }

    public function setPickAddress($pick_address){
        $parts = $this->extractAddressParts($pick_address);
        $this->_pick_province_name = $parts['province'];
        $this->_pick_district_name = $parts['district'];
        $this->_pick_ward_name = $parts['ward'];
        $this->_pick_address_name = $parts['detail'];

        return $this;
    }



    public function setProvinceName($province_name) {
        $this->_province = $province_name;
        return $this;
    }

    public function setDistrictName($district_name) {
        $this->_district = $district_name;
        return $this;
    }

    public function setWardName($ward_name) {
        $this->_ward = $ward_name;
        return $this;
    }

    public function setDetailName($detail_name) {
        $this->_detail = $detail_name;
        return $this;
    }

    public function calculateFee($payload) {
        $payload = array_merge($payload, [
            'pick_address_id' => $this->_pick_address_id,
            'pick_province' => $this->_pick_province_name,
            'pick_district' => $this->_pick_district_name,
            'pick_ward' => $this->_pick_ward_name,
            'pick_address' => $this->_pick_address_name,
            'province' => $this->_province,
            'district' => $this->_district,
            'ward' => $this->_ward,
            'address' => $this->_detail,
        ]);

        return $this->_request('post', '/services/shipment/fee', $payload);
    }

    public function createOrder($payload)
    {
        $payload['order'] += [
            "address" => $this->_detail,
            "province" => $this->_province,
            "district" => $this->_district,
            "ward" => $this->_ward,
            "pick_address_id" => $this->_pick_address_id
        ];

        // dd($payload);

        return $this->_request('post', '/services/shipment/order', $payload);
    }

    public function cancelOrder(string $code){
        return $this->_request('post', '/services/shipment/cancel/'.$code);
    }
}