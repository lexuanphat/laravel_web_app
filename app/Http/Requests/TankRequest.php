<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TankRequest extends FormRequest
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
            'type' => 'required',
            'code' => [
                'required',
                'unique:tanks'
            ],
            'max_capacity' => [
                'required',
            ],
            'current_capacity' => [
                'required',
                'lte:max_capacity'
            ],

        ];

        if($this->get('method') === "PUT") {
            $rules['code'][1] = "unique:tanks,code,{$this->id}";
        }

        return $rules;
    }

    public function messages() 
    {
        return [
            'required' => 'không bỏ trống',
            'unique' => 'dữ liệu đã tồn tại',
            'lte' => 'Dung tích hiện tại không được vượt quá dung tích tối đa.'
        ];
    }
}
