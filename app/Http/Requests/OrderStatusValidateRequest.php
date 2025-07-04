<?php

namespace App\Http\Requests;

use App\Http\Controllers\OrderController;
use Illuminate\Foundation\Http\FormRequest;

class OrderStatusValidateRequest extends FormRequest
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
            'note_logs' => [
                'required',
            ],
            'status_code' => [
                'required',
                'in:'.implode(",", array_keys(OrderController::ORDER_STATUS_MESSAGE)),
            ],
        ];
    }

    public function messages()
    {
        return [
            'required' => 'không bỏ trống',
            'in' => 'không hợp lệ',
        ];
    }
}
