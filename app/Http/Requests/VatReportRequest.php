<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VatReportRequest extends FormRequest
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
            'vat_id' => 'required',
            'protein_level' => 'required',
            'salt_level' => 'required',
            'histamine_level' => 'required',
            'acid_level' => 'required',
            'amon_level' => 'required',
            'color' => 'required',
        ];

        

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
