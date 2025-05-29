<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductStockValidateRequest extends FormRequest
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
        return [
            "product_id" => [
                'required',
                'exists:products,id'
            ],
            "store_id" => [
                'required',
                'exists:stores,id'
            ],
            "stock_quantity" => [
                'required',
                'regex:/^\d+$/',
            ],
            "available_quantity" => [
                'required',
                'regex:/^\d+$/',
                'lte:stock_quantity',
            ],
            "stock_price" => [
                'required',
                'regex:/^\d+$/',
            ],
        ];
    }

    public function messages(){
        return [
            'required' => 'không bỏ trống',
            'regex' => 'không hợp lệ',
            'exists' => 'không tồn tại',
            'lte' => 'không lớn hơn :value'
        ];
    }

    public function prepareForValidation(){
        $list_replace = [
            'stock_price', 'available_quantity', 'stock_quantity'
        ];

        foreach($list_replace as $key) {
            if(isset($this->$key)) {
                $this->merge([
                    $key => str_replace('.', "", $this->$key),
                ]);
            }
        }

        return $this;
    }
}
