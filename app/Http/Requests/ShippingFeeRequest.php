<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShippingFeeRequest extends FormRequest
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
        $rules = [
            'province_id' => [
                'required',
                'exists:provinces,id',
                'unique:shipping_fees,province_id'
            ],
            'fee' => [
                'required',
                'numeric',
            ],
        ];

        if($this->get('method') === "PUT") {
            $rules['province_id'][2] = "unique:shipping_fees,province_id,{$this->id}";
            $rules['province_id'][1] = function ($attribute, $value, $fail) {
                if ($value != -1 && !\DB::table('provinces')->where('id', $value)->exists()) {
                    $fail($attribute . "không tồn tại");
                }
            };
        }

        return $rules;
    }

    public function messages(){
        return [
            'required' => ':attribute không bỏ trống',
            'exists' => ':attribute không tồn tại',
            'unique' => ':attribute đã tồn tại',
        ];
    }

    public function attributes(){
        return [
            'province_id' => 'Tỉnh thành',
            'fee' => 'Phí vận chuyển'
        ];
    }
}
