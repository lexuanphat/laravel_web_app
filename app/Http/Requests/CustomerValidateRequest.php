<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerValidateRequest extends FormRequest
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
            'full_name' => [
                'required',
            ],
            'phone' => [
                'required',
                'unique:customers',
                'regex:/^[0-9()+]*$/'
            ],
            'email' => [
                'nullable',
                'unique:customers',
                'email:rfc,filter'
            ],
            'gender' => [
                'required',
                'in:0,1'
            ],
            'date_of_birth' => [
                'nullable',
                'date'
            ],
            'address' => [
                'required',
                'string',
            ],
            'ward_code' => [
                'required',
                'numeric',
                'gt:0',
            ],
            'district_code' => [
                'required',
                'numeric',
                'gt:0',
            ],
            'province_code' => [
                'required',
                'numeric',
                'gt:0',
            ],
        ];

        if($this->get('method') === "PUT") {
            $rules['phone'][1] = "unique:customers,phone,{$this->id}";
            $rules['email'][1] = "unique:customers,email,{$this->id}";
        }

        return $rules;
    }

    public function messages(){
        return [
            'required' => 'không bỏ trống',
            'regex' => 'không hợp lệ',
            'email' => 'không hợp lệ',
            'in' => 'không hợp lệ',
            'gt' => 'không hợp lệ',
            'date' => 'không hợp lệ',
            'numeric' => 'không hợp lệ',
            'unique' => 'dữ liệu đã tồn tại',
        ];
    }
}
