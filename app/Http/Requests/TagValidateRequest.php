<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TagValidateRequest extends FormRequest
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
            'tag_name' => [
                'required',
                'unique:tags'
            ],
        ];

        if($this->get('method') === "PUT") {
            $rules['tag_name'][1] = "unique:tags,name,{$this->id}";
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'required' => 'không bỏ trống',
            'unique' => 'dữ liệu đã tồn tại',
        ];
    }
}
