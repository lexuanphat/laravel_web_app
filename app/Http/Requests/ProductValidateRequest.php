<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductValidateRequest extends FormRequest
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
            'name' => [
                'required',
                'unique:products',
            ],
            'sku' => [
                'nullable',
                'unique:products',
            ],
            'category_id' => [
                'required',
                'exists:categories,id',
            ],
            'price' => [
                'required',
                'regex:/^\d+$/',
                'gt:0',
            ],
            'desc' => [
                'nullable',
            ],
            'image_url' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,gif',
                'max:2048',
            ],
            'length' => [
                'required',
                'regex:/^\d+$/',
                'gt:0',
            ],
            'width' => [
                'required',
                'regex:/^\d+$/',
                'gt:0',
            ],
            'height' => [
                'required',
                'regex:/^\d+$/',
                'gt:0',
            ],
            'weight' => [
                'required',
                'regex:/^\d+$/',
                'gt:0',
            ],
            'tag_id' => [
                'nullable',
                'exists:tags,id',
            ],
        ];

        if($this->_method === 'PUT') {
            $rules['name'][1] = "unique:products,name,$this->id";
            $rules['sku'][1] = "unique:products,sku,$this->id";
        }

        return $rules;
    }

    public function prepareForValidation(){
        if(isset($this->price)) {
            $this->merge([
                'price' => str_replace('.', "", $this->price),
            ]);
        }

        return $this;
    }

    public function messages(){
        return [
            'required' => 'không bỏ trống',
            'regex' => 'không hợp lệ',
            'exists' => 'không tồn tại',
            'unique' => 'dữ liệu đã tồn tại',
            'image' => 'chỉ chấp nhận hình ảnh',
            'mimes' => 'chỉ chấp nhận file có đuôi: :values',
            'max' => 'kích thước không lớn hơn :max',
            'gt' => 'lớn hơn :value',
        ];
    }
}
