<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderValidateRequest extends FormRequest
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
        $this->merge([
            'customer' => json_decode($this->customer, true),
            'products' => json_decode($this->products, true),
            'client_request_transport' => json_decode($this->client_request_transport, true),
        ]);

        $rules = [
            'store_id' => 'required|integer|exists:stores,id',

            'customer.id' => 'required',
            'customer.full_name' => 'required|string|max:255',
            'customer.phone' => 'required|string|regex:/^0\d{9}$/',
            'customer.province' => 'required|string',
            'customer.district' => 'required|string',
            'customer.ward' => 'required|string',
            'customer.province_text' => 'required|string',
            'customer.district_text' => 'required|string',
            'customer.ward_text' => 'required|string',
            'customer.address' => 'required|string',

            'source' => 'required|in:facebook,shoppe,tiktok,other',

            'note' => 'nullable',
            'discount_total' => 'required',
            'customer_has_paid_total' => 'required',

            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|numeric|exists:products,id',
            'products.*.quantity' => 'required|numeric|min:1',
            'products.*.is_option' => 'required|in:1,2',
            'products.*.discount' => 'required|numeric|min:0',


            'client_request_transport.type' => 'required',
            'client_request_transport.cod' => 'required|numeric|min:0',
            'client_request_transport.gam' => 'required|numeric|min:0',
            'client_request_transport.length' => 'required|numeric|min:1',
            'client_request_transport.width' => 'required|numeric|min:1',
            'client_request_transport.height' => 'required|numeric|min:1',
        
            'client_request_transport.require_transport_option' => 'nullable|in:CHOTHUHANG,KHONGCHOXEMHANG,CHOXEMHANGKHONGTHU',
            'client_request_transport.shipping_fee_payer' => 'nullable|in:shop,customer',
            'client_request_transport.note_transport' => 'nullable',
            'client_request_transport.shipping_partner_id' => 'required',
            'client_request_transport.shipping_fee' => 'required',
        ];

        if($this->client_request_transport['type'] == 3) { // NHẬN TẠI CỬA HÀNG
            $rules['client_request_transport.length'] = 'nullable';
            $rules['client_request_transport.height'] = 'nullable';
            $rules['client_request_transport.width'] = 'nullable';
            $rules['client_request_transport.gam'] = 'nullable';
            $rules['client_request_transport.cod'] = 'nullable';
            $rules['client_request_transport.shipping_partner_id'] = 'nullable';
            $rules['client_request_transport.shipping_fee'] = 'nullable';
        }

        return $rules;
    }

    public function messages()
    {
        $messages = [
            'store_id.required' => 'Vui lòng chọn cửa hàng.',
            'store_id.exists' => 'Cửa hàng không tồn tại.',
        
            'customer.id.required' => 'Thiếu thông tin khách hàng.',
            'customer.full_name.required' => 'Vui lòng nhập họ tên khách hàng.',
            'customer.full_name.string' => 'Họ tên phải là chuỗi ký tự.',
            'customer.full_name.max' => 'Họ tên không được vượt quá 255 ký tự.',
            'customer.phone.required' => 'Vui lòng nhập số điện thoại.',
            'customer.phone.string' => 'Số điện thoại không hợp lệ.',
            'customer.phone.regex' => 'Số điện thoại phải đúng định dạng 10 số bắt đầu bằng 0.',
            'customer.province.required' => 'Vui lòng chọn tỉnh/thành.',
            'customer.district.required' => 'Vui lòng chọn quận/huyện.',
            'customer.ward.required' => 'Vui lòng chọn phường/xã.',
            'customer.address.required' => 'Vui lòng nhập địa chỉ.',
            'customer.address.max' => 'Địa chỉ không được vượt quá 500 ký tự.',
        
            'source.required' => 'Vui lòng chọn nguồn đơn hàng.',
            'source.in' => 'Nguồn đơn hàng không hợp lệ.',
        
            'discount_total.required' => 'Vui lòng nhập tổng giảm giá.',
            'customer_has_paid_total.required' => 'Vui lòng nhập số tiền khách đã thanh toán.',
        
            'products.required' => 'Vui lòng thêm ít nhất một sản phẩm.',
            'products.array' => 'Danh sách sản phẩm không hợp lệ.',
            'products.*.product_id.required' => 'Vui lòng chọn sản phẩm.',
            'products.*.product_id.numeric' => 'ID sản phẩm phải là số.',
            'products.*.product_id.exists' => 'Sản phẩm không tồn tại.',
            'products.*.quantity.required' => 'Vui lòng nhập số lượng sản phẩm.',
            'products.*.quantity.numeric' => 'Số lượng phải là số.',
            'products.*.quantity.min' => 'Số lượng phải ít nhất là 1.',
            'products.*.is_option.required' => 'Vui lòng chọn loại sản phẩm.',
            'products.*.is_option.in' => 'Loại sản phẩm không hợp lệ.',
            'products.*.discount.required' => 'Vui lòng nhập giảm giá sản phẩm.',
            'products.*.discount.numeric' => 'Giảm giá phải là số.',
            'products.*.discount.min' => 'Giảm giá không được nhỏ hơn 0.',
        
            'client_request_transport.cod.required' => 'Vui lòng nhập số tiền COD.',
            'client_request_transport.cod.numeric' => 'Số tiền COD phải là số.',
            'client_request_transport.cod.min' => 'Số tiền COD không được âm.',
        
            'client_request_transport.gam.required' => 'Vui lòng nhập khối lượng đơn hàng.',
            'client_request_transport.gam.numeric' => 'Khối lượng phải là số.',
            'client_request_transport.gam.min' => 'Khối lượng không được âm.',
        
            'client_request_transport.length.required' => 'Vui lòng nhập chiều dài kiện hàng.',
            'client_request_transport.length.numeric' => 'Chiều dài phải là số.',
            'client_request_transport.length.min' => 'Chiều dài phải lớn hơn 0.',
        
            'client_request_transport.width.required' => 'Vui lòng nhập chiều rộng kiện hàng.',
            'client_request_transport.width.numeric' => 'Chiều rộng phải là số.',
            'client_request_transport.width.min' => 'Chiều rộng phải lớn hơn 0.',
        
            'client_request_transport.height.required' => 'Vui lòng nhập chiều cao kiện hàng.',
            'client_request_transport.height.numeric' => 'Chiều cao phải là số.',
            'client_request_transport.height.min' => 'Chiều cao phải lớn hơn 0.',
        
            'client_request_transport.require_transport_option.in' => 'Tuỳ chọn giao hàng không hợp lệ.',
        
            'client_request_transport.shipping_fee_payer.required' => 'Vui lòng chọn người trả phí vận chuyển.',
            'client_request_transport.shipping_fee_payer.in' => 'Giá trị người trả phí không hợp lệ.',

            'client_request_transport.shipping_partner_id.required' => 'Vui lòng chọn đối tác giao hàng',
            'client_request_transport.shipping_fee.required' => 'Vui lòng nhập phí giao hàng',
    
        ];

        return $messages;
    }
}
