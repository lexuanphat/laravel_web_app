<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FeeProductProvinceRequest extends FormRequest
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
            ],
            'product_id' => [
                'required',
                'exists:products,id',
                Rule::unique('fee_product_province') // bảng mà bạn muốn unique
                ->where(fn ($query) => $query->where('province_id', $this->province_id)),
                ],
            'fee' => [
                'required',
                'numeric',
            ],
        ];

        if($this->get('method') === "PUT") {
            $rules['product_id'][2] = Rule::unique('fee_product_province') // bảng mà bạn muốn unique
            ->where(fn ($query) => $query->where('province_id', $this->province_id))->ignore($this->id);
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
            'province_id' => 'Khu vực',
            'fee' => 'Phí vận chuyển',
            'product_id' => 'Sản phẩm',
        ];
    }
}
