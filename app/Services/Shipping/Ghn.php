<?php
namespace App\Services\Shipping;
use Illuminate\Support\Facades\Http;

class Ghn extends Base {
    private $_token;
    private $_base_url;
    private $_shop_id;

    private $_province;
    private $_district;
    private $_ward;
    private $_detail;

    const REQUIRED_NOTE = [
        'CHOTHUHANG' => 'CHOTHUHANG',
        'CHOXEMHANGKHONGTHU' => 'CHOXEMHANGKHONGTHU',
        'KHONGCHOXEMHANG' => 'KHONGCHOXEMHANG',
    ];

    public function __construct(string $token, string $shop_id, string $baseUrl = null)
    {
        $this->_token = $token;
        $this->_shop_id = $shop_id;
        $this->_base_url = $baseUrl ?? 'https://online-gateway.ghn.vn/shiip/public-api';
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

    protected function headers()
    {
        return [
            'Token' => $this->_token,
            'shop_id' => $this->_shop_id,
            'Content-Type' => 'application/json',
        ];
    }

    private function _request($method, $endpoint, $data = []) {
        $headers = $this->headers();

        $response = Http::withHeaders($headers)->$method("{$this->_base_url}{$endpoint}", $data);

        if ($response->successful()) {
            return $response->json()['data'] ?? $response->json();
        }

        $message = "GHN API báo lỗi: ";
        $response_json = $response->json();
        if(isset($response_json['code_message_value'])) {
            $message .= $response_json['code_message_value'];
        } else if($response_json['code'] == 400 && isset($response_json['message'])){
            $message .= $response_json['message'];
        } else {
            $message .=  'vui lòng thử lại';
        }

        throw new \Exception($message);
    }

    public function getProvinces()
    {
        return $this->_request('get', '/shiip/public-api/master-data/province');
    }

    public function findProvinceIdByName($province_name)
    {
        $provinces = $this->getProvinces();
    
        foreach ($provinces as $province) {

            if (stripos($province['ProvinceName'], $province_name) !== false) {
                return $province['ProvinceID'];
            }

            if(isset($province['NameExtension'])) {
                foreach ($province['NameExtension'] as $alt) {
                    if (stripos($alt, $province_name) !== false || stripos($province_name, $alt) !== false) {
                        return $province['ProvinceID'];
                    }
                }
            }
        }
    
        throw new \Exception("Không tìm thấy tỉnh: $province_name");
    }

    public function getDistricts($provinceId)
    {
        return $this->_request('post', '/shiip/public-api/master-data/district', [
            'province_id' => $provinceId,
        ]);
    }

    public function findDistrictIdByName($province_name, $district_name)
    {
        $provinceId = $this->findProvinceIdByName($province_name);
        $districts = $this->getDistricts($provinceId);

        foreach ($districts as $district) {
            if (stripos($district['DistrictName'], $district_name) !== false) {
                return $district['DistrictID'];
            }

            if(isset($district['NameExtension'])) {
                foreach ($district['NameExtension'] as $alt) {
                    if (stripos($alt, $district_name) !== false || stripos($district_name, $alt) !== false) {
                        return $district['DistrictID'];
                    }
                }
            }

        }

        throw new \Exception("Không tìm thấy quận: $district_name trong tỉnh $province_name");
    }

    public function getWards($districtId)
    {
        return $this->_request('post', '/shiip/public-api/master-data/ward', [
            'district_id' => $districtId,
        ]);
    }

    public function findWardCodeByName($province_name, $district_name, $ward_name)
    {
        $districtId = $this->findDistrictIdByName($province_name, $district_name);
        $wards = $this->getWards($districtId);

        foreach ($wards as $ward) {
            if (stripos($ward['WardName'], $ward_name) !== false) {
                return $ward['WardCode'];
            }

            if(isset($ward['NameExtension'])){
                foreach ($ward['NameExtension'] as $alt) {
                    if (stripos($alt, $ward_name) !== false || stripos($ward_name, $alt) !== false) {
                        return $ward['WardCode'];
                    }
                }
            }
        }

        throw new \Exception("Không tìm thấy phường: $ward_name trong quận $district_name");
    }

    public function calculateFee($payload)
    {
        $payload = array_merge($payload, [
            'to_ward_code' => $this->findWardCodeByName($this->_province, $this->_district, $this->_ward),
            'to_district_id' => $this->findDistrictIdByName($this->_province, $this->_district),
        ]);

        return $this->_request('post', '/shiip/public-api/v2/shipping-order/fee', $payload);
    }

    public function createOrder($payload)
    {
        $payload = array_merge($payload, [
           'to_address' => $this->_detail,
           'to_ward_name' => $this->_ward,
           'to_district_name' => $this->_district,
           'to_province_name' => $this->_province,
        ]);

        return $this->_request('post', '/shiip/public-api/v2/shipping-order/create', $payload);
    }

    public function cancelOrder(string $code){
        $payload = [
            'order_codes' => [$code]
        ];
        return $this->_request('post', '/shiip/public-api/v2/switch-status/cancel', $payload);
    }
}