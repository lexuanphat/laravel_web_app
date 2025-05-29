<?php

namespace App\Http\Requests;

use App\Models\Transport;
use Illuminate\Foundation\Http\FormRequest;

class TransportValidateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $chanh_xe = Transport::ROLE['CHANH_XE'];
        $shipper = Transport::ROLE['SHIPPER'];
        $rules = [
            'full_name' => [
                'required',
                'unique:transports'
            ],
            'phone' => [
                'required',
                'unique:transports'
            ],
            'role' => [
                'required',
                "in:{$chanh_xe},{$shipper}",
            ],
        ];

        if($this->get('method') === "PUT") {
            $rules['full_name'][1] = "unique:transports,full_name,{$this->id}";
            $rules['phone'][1] = "unique:transports,phone,{$this->id}";
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'required' => 'không bỏ trống',
            'unique' => 'dữ liệu đã tồn tại',
            'in' => 'không có trong hệ thống',
        ];
    }
}
