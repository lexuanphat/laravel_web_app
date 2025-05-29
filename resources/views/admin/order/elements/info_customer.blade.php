<div class="col-md-8">
    <div class="card" id="card_info_customer" data-route='{{route('admin.order.get_data_customer')}}'>
        <div class="card-body p-2">
            <h5>Thông tin khách hàng</h5>
            <select class="form-control select2" data-toggle="select2" data-placeholder="Tìm theo tên, SĐT, mã khách hàng...(F4)" id="info_customer">
            </select>
            <div class="result">
                <div class="empty py-5 text-center" id="empty">
                    <i class="ri-account-box-line fs-1"></i>
                    <p>Chưa có thông tin khách hàng</p>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- <script> --}}
@push('js_ready')
@php echo file_get_contents(asset('assets/js/order/info_customer.js')) @endphp
@endpush
{{-- </script> --}}