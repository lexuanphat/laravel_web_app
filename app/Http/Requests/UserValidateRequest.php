<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class UserValidateRequest extends FormRequest
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
        $list_role = array_values(User::ROLE_VALUE);
        $list_role = implode(",", $list_role);
        $current_user = auth()->user();

        if($current_user->role === User::ROLE_ACCESS_PAGE['manage_sale']) {
            $list_role = [User::ROLE_VALUE['staff_sale'], User::ROLE_ACCESS_PAGE['manage_sale']];
            $list_role = implode(",", $list_role);
        } else if($current_user->role === User::ROLE_ACCESS_PAGE['manage_producttion']){
            $list_role = [User::ROLE_VALUE['staff_producttion'], User::ROLE_ACCESS_PAGE['manage_producttion']];
            $list_role = implode(",", $list_role);
        }

        $rules = [
            'full_name' => [
                'required',
            ],
            'email' => [
                'required',
                'unique:users',
                'email:rfc,dns',
            ],
            'phone' => [
                'required',
                'unique:users',
                'min:10',
                'numeric',
            ],
            'password' => [
                'required',
                'min:8',
            ],
            'role' => [
                'required',
                'in:'.$list_role
            ],
        ];

        if($this->get('method') === "PUT") {
            $rules['email'][1] = "unique:users,email,{$this->id}";
            $rules['phone'][1] = "unique:users,phone,{$this->id}";
            $rules['password'][0] = 'nullable';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'required' => 'không bỏ trống',
            'unique' => 'dữ liệu đã tồn tại',
            'email' => 'không hợp lệ',
            'phone.min' => 'không hợp lệ',
            'numeric' => 'không hợp lệ',
            'password.min' => 'Nhập phải lớn hơn hoặc bằng 8 kí tự',
            'in' => 'không hợp lệ',
        ];
    }
}
