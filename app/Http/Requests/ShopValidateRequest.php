<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShopValidateRequest extends FormRequest
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
                'unique:stores'
            ],
            'address' => 'required|string',
            'contact_phone' => [
                'required',
                'string',
                'regex:/^[0-9]+$/',
                'unique:stores'
            ],
        ];

        if($this->get('method') === "PUT") {
            $rules['name'][1] = "unique:stores,name,{$this->id}";
            $rules['contact_phone'][3] = "unique:stores,contact_phone,{$this->id}";
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'required' => 'không bỏ trống',
            'string' => 'không hợp lệ',
            'regex' => 'không hợp lệ',
            'unique' => 'dữ liệu đã tồn tại',
        ];
    }
}
