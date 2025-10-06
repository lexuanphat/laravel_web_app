<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CouponRequest extends FormRequest
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
            'date_start_apply' => [
                'required',
                'date',
                'before:date_end_apply',
            ],
            'date_end_apply' => [
                'required',
                'date',
                'after:date_start_apply',
            ],
            'name' => [
                'required',
            ],
            'type' => [
                'required',
                'in:PHAN_TRAM,TIEN',
            ],
            'fee' => [
                'required',
                'numeric',
                'min:1',
            ],
        ];

        // if($this->get('method') === "PUT") {
        //     $rules['province_id'][2] = "unique:shipping_fees,province_id,{$this->id}";
        //     $rules['province_id'][1] = function ($attribute, $value, $fail) {
        //         if ($value != -1 && !\DB::table('provinces')->where('id', $value)->exists()) {
        //             $fail($attribute . "không tồn tại");
        //         }
        //     };
        // }

        return $rules;
    }

    public function messages(){
        return [
            'required' => ':attribute không bỏ trống',
            'date' => ':attribute phải thuộc ngày',
            'before' => ':attribute không lớn hơn :date',
            'after' => ':attribute không nhỏ hơn :date',
            'min' => ':attribute lớn hơn :min',
        ];
    }

    public function attributes(){
        return [
            "date_start_apply" => "Ngày bắt đầu",
            "date_end_apply" => "Ngày kết thúc",
            "name" => "Tên",
            "type" => "Loại",
            "fee" => "Giá trị",
        ];
    }
}
