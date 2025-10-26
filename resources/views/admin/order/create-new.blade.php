@extends('_blank')
@push('style')
<style>
    .table-responsive {
        max-height:300px;
    }
    #select2-customer-results li:has(#div-create-new) {
        background-color: #C01415;
    }
</style>
@endpush
@section('content')

<!-- Large modal -->
<div class="modal fade" id="modal_add_new_customer" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-bs-focus="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myLargeModalLabel">Thêm mới khách hàng</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="modal-body">
                <form action="" method="POST" id="form_action">
                    @csrf
                    <div class="mb-2">
                        <label for="full_name" class="required">Tên<span class="text-danger">(*)</span></label>
                        <input type="text" id="full_name" name="full_name" placeholder="-- Nhập tên khách hàng --" class="form-control" required>
                        @include('admin.shop.modals.div-error')
                    </div>
                    <div class="mb-2">
                        <label for="phone" class="required">Số điện thoại <span class="text-danger">(*)</span></label>
                        <input type="text" oninput="this.value = this.value.replace(/[^0-9]/g, '')" id="phone" name="phone" placeholder="-- Nhập số điện thoại khách hàng --" class="form-control" required>
                        @include('admin.shop.modals.div-error')
                    </div>
                    <div class="mb-2">
                        <label for="email"  class="required">Email</label>
                        <input type="email" id="email" name="email" placeholder="-- Nhập địa chỉ email khách hàng --" class="form-control">
                        @include('admin.shop.modals.div-error')
                    </div>
                    <div class="mb-2">
                        <label for="date_of_birth"  class="required">Ngày tháng năm sinh</label>
                        <input type="date" id="date_of_birth" name="date_of_birth" class="form-control">
                        @include('admin.shop.modals.div-error')
                    </div>
                    <div class="mb-2">
                        <label for=""  class="required">Địa chỉ nhận hàng <span class="text-danger">(*)</span></label>
                        <div class="row g-2">
                            <div class="col-6">
                                <select class="form-control select2" data-toggle="select2" name="province_code" id="province_code">
                                    <option value=''>Chọn tỉnh thành</option>
                                    @foreach($get_provinces as $item)
                                    <option value="{{$item->id}}">{{$item->text}}</option>
                                    @endforeach
                                </select>
                                @include('admin.shop.modals.div-error')
                            </div>
                            <div class="col-6">
                                <select class="form-control select2" data-toggle="select2" name="ward_code" id="ward_code">
                                    <option value=''>Chọn phường xã</option>
                                </select>
                                @include('admin.shop.modals.div-error')
                            </div>
                            <div class="col-12">
                                <input type="text" id="address" name="address" class="form-control" placeholder="-- Nhập địa chỉ --">
                                @include('admin.shop.modals.div-error')
                            </div>
                        </div>
                    </div>
                    <div class="mb-2">
                        <div>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="male" value="0" class="custom-control-input" name="gender" checked>
                                <label class="custom-control-label" for="male">Nam</label>
                            </div>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="female" value="1" class="custom-control-input" name="gender">
                                <label class="custom-control-label" for="female">Nữ</label>
                            </div>
                        </div>
                        @include('admin.shop.modals.div-error')
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary btn_action" id="btn_add_new_customer">
                    @include('admin._partials.add-new')
                    @include('admin._partials.loading')
                </button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="row g-2 py-2 max-vh-50">
    <div class="col-md-8">
        <div class="card" id="card_customer" data-route='{{route('admin.order.get_data_customer')}}'>
            <div class="card-body p-2">
                <h5>Thông tin khách hàng <span class="text-danger">(*)</span></h5>
                <select class="form-control select2" data-toggle="select2" data-placeholder="Tìm theo tên, SĐT, mã khách hàng...(F4)" id="customer">
                    
                </select>
                <div class="result">
                    <div class="empty py-5 text-center" id="empty_customer">
                        <i class="ri-account-box-line fs-1"></i>
                        <p>Chưa có thông tin khách hàng</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card" id="addition">
            <div class="card-body p-2">
                <h5>Thông tin bổ sung</h5>
                <div class="info_addition">
                    <div class="mb-2 row">
                        <label for="colFormLabelSm" class="col-sm-4 col-form-label col-form-label-sm">
                            Bán tại <span class="text-danger">(*)</span>
                        </label>
                        <div class="col-sm-8">
                            {{-- <input type="text" class="form-control" value="{{auth()->user()->store->name}}" disabled> --}}
                            <select name="pick_address_id" id="pick_address_id" data-toggle="select2">
                                @foreach($get_list_pick_add_ghtk as $item)
                                <option value='{{$item['pick_address_id']}}'>{{$item['pick_name']}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-2 row">
                        <label for="colFormLabelSm" class="col-sm-4 col-form-label col-form-label-sm">
                            Bán bởi <span class="text-danger">(*)</span>
                        </label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" value="{{auth()->user()->full_name}}" disabled>
                        </div>
                    </div>
                    <div class="mb-2 row">
                        <label for="colFormLabelSm" class="col-sm-4 col-form-label col-form-label-sm">
                            Nguồn <span class="text-danger">(*)</span>
                        </label>
                        <div class="col-sm-8">
                            <select name="source" required id="source" class="form-control select2" data-toggle="select2">
                                <option value="tiktok">Tiktok</option>
                                <option value="facebook">Facebook</option>
                                <option value="instagram">Instagram</option>
                                <option value="other">Khác</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-2 row">
                        <label for="colFormLabelSm" class="col-sm-4 col-form-label col-form-label-sm">
                            Ngày lên đơn <span class="text-danger">(*)</span>
                        </label>
                        <div class="col-sm-8">
                            <input type="date" disabled value="{{date('Y-m-d')}}" min="{{date('Y-m-d')}}" max="{{date('Y-m-d')}}" id="schedule_delivery_date" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-md-12">
    <div class="card" id="products" data-route='{{route('admin.order.get_data_product')}}'>
        <div class="card-body">
            <h5>Thông tin sản phẩm <span class="text-danger">(*)</span></h5>
            <select class="form-control select2" data-toggle="select2" data-placeholder="Tìm theo tên, mã SKU sản phẩm...(F3)" id="info_product">
            </select>
            <div class="result-product">
                <div class="empty_prod py-5 text-center" id="empty_prod">
                    <i class="ri-gift-line fs-1"></i>
                    <p>Chưa có sản phẩm</p>
                    <button type="button" class="btn btn-outline-info" id="btn_infor_product">Thêm sản phẩm</button>
                </div>
            </div>
        </div>
        <form action="POST" id="form_table_product">
            <div class="table-responsive d-none" id="table_product">
                <table class="table table-borderless table-centered mb-0 table-fixed">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 1%" class="text-center">STT</th>
                            <th class="text-start">Tên sản phẩm</th>
                            <th style="width: 15%" class="text-center">Số lượng <span class="text-danger">(*)</span></th>
                            <th style="width: 10%" class="text-center">Đơn giá</th>
                            <th style="width: 28%" class="text-center">Chiết khấu</th>
                            <th style="width: 10%" class="text-center">Thành tiền</th>
                            <th style="width: 1%"></th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </form>
        <hr>
        <div class="card-body">
            <div class="result_info_product" id="result_info_product">
                <div class="row gx-5">
                    <div class="col-md-6">
                        <label for="">Ghi chú đơn hàng</label>
                        <textarea rows="5" id="note_total" class="form-control" placeholder="Hàng dễ vỡ, vui lòng nhẹ tay"></textarea>
                    </div>
                    <div class="col-md-6">
                        <div class="row mb-1">
                            <label for="inputEmail3" class="col-7 col-form-label">Tổng tiền (<span id="cnt_total_product">0</span> sản phẩm)</label>
                            <div class="col-5">
                                <input type="text" class="form-control border-0 text-end" readonly id="total" value="0">
                            </div>
                        </div>
                        <div class="row mb-1">
                            <label for="" class="col-6 col-form-label">Phiếu giảm giá</label>
                            <div class="col-6">
                                <select name="coupon" required id="coupon" class="form-control select2" data-toggle="select2">
                                    <option value="">Chọn phiếu giảm giá</option>
                                    @foreach($coupons as $coupon)
                                    <option data-item='{{json_encode($coupon)}}' value="{{$coupon->id}}">{{$coupon->text}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mb-1">
                            <label for="inputEmail3" class="col-7 col-form-label fw-bold">Tổng tiền sau áp dụng</label>
                            <div class="col-5">
                                <input type="text" class="form-control border-0 text-end fw-bold" readonly id="total_after_coupon" value="0">
                            </div>
                        </div>
                        <div class="row mb-1">
                            <label for="" class="col-7 col-form-label">Chiết khấu</label>
                            <div class="col-5">
                                <div class="input-group bootstrap-touchspin bootstrap-touchspin-injected">
                                    <input type="text" oninput="this.value = this.value.replace(/[^0-9]/g, '');" class="form-control border-0 border-bottom text-end input_money" id="discount_total" placeholder="Nhập chiết khấu" value="0">
                                    <span class="input-group-addon bootstrap-touchspin-postfix input-group-append">
                                        <span class="input-group-text unit border-0 border-bottom">%</span>
                                    </span>
                                </div>
                                <div class="text-end d-none" id="discount_total_money">
                                    <span class="small text-danger">0</span>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-1">
                            <label for="" class="col-7 col-form-label fw-bold">Khách phải trả</label>
                            <div class="col-5">
                                <input type="text" class="form-control border-0 text-end fw-bold" id="customer_paid_total" readonly value="0">
                            </div>
                        </div>
                        <div class="row mb-1">
                            <label for="" class="col-7 col-form-label fw-bold">Khách đã trả</label>
                            <div class="col-5">
                                <input type="text" class="form-control border-0 border-bottom text-end input_money fw-bold" id="customer_has_paid_total" placeholder="Tiền khách trả" value="0">
                            </div>
                        </div>
                        <div class="row mb-1">
                            <label for="" class="col-7 col-form-label">Còn phải trả</label>
                            <div class="col-5">
                                <input type="text" class="form-control border-0 text-end" id="total_end" value="0">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card" id="object_order">
    <div class="card-body">
        <div class="row g-2">
            <div class="mb-3 col-md-4">
                <label for="user_order" class="form-label">Người đặt hàng <span class="text-danger">(*)</span></label>
                <select class="form-control select2" id="user_order" data-toggle="select2">
                    <option value="">Chọn người đặt hàng</option>
                   @foreach($get_customers as $customer)
                    <option value="{{$customer->id}}">{{$customer->full_name}} - {{$customer->phone}}</option>
                   @endforeach
                </select>
            </div>
            <div class="mb-3 col-md-4">
                <label for="user_consignee" class="form-label">Người nhận hàng <span class="text-danger">(*)</span></label>
                <select class="form-control select2" id="user_consignee" data-toggle="select2">
                    <option value="">Chọn người nhận hàng</option>
                    @foreach($get_customers as $customer)
                    <option value="{{$customer->id}}">{{$customer->full_name}} - {{$customer->phone}}</option>
                   @endforeach
                </select>
            </div>
            <div class="mb-3 col-md-4">
                <label for="user_payer" class="form-label">Người trả tiền <span class="text-danger">(*)</span></label>
                <select class="form-control select2" id="user_payer" data-toggle="select2">
                    <option value="">Chọn người trả tiền</option>
                    @foreach($get_customers as $customer)
                    <option value="{{$customer->id}}">{{$customer->full_name}} - {{$customer->phone}}</option>
                   @endforeach
                </select>
            </div>
        </div>
    </div>
</div>

<div class="card" id="package_and_delivery">
    <form action="POST" id="form_transport">
        <div class="card-body">
            <h5>Đóng gói và giao hàng</h5>
            <div class="options d-flex flex-wrap gap-2">
                <div class="option">
                    <input type="radio" class="btn-check" value="1" name="object_transport" id="option1" autocomplete="off">
                    <label class="btn btn-outline-primary" for="option1"><i class="ri-truck-line"></i> Đẩy qua hãng vận chuyển</label>
                </div>
        
                <div class="option">
                    <input type="radio" class="btn-check" value="2" name="object_transport" id="option2" autocomplete="off">
                    <label class="btn btn-outline-primary" for="option2"><i class="ri-takeaway-line"></i> Đẩy vận chuyển ngoài</label>
                </div>
        
                <div class="option">
                    <input type="radio" class="btn-check" value="3" name="object_transport" id="option3" autocomplete="off">
                    <label class="btn btn-outline-primary" for="option3"><i class="ri-home-8-line"></i> Nhận tại cửa hàng</label>
                </div>
    
            </div>
        </div>
        <hr class="my-0">
        <div class="card-body">
            <div class="row">
                <div class="left col-md-4" id="left">
                    <div class="border border-primary rounded-3 p-2 bg-primary-subtle">
                        <h4>Thông tin giao hàng</h4>
                        <div class="mb-3">
                            <label class="form-label">Thu tiền hộ (COD)</label>
                            <input type="text" id="cod" name="cod" class="form-control text-end input_money" value="0" placeholder="Nhập tiền thu hộ">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Khối lượng (g)</label>
                            <input type="text" id="gam" name="gam" class="form-control text-end input_money" value="0" placeholder="Nhập khối lượng">
                        </div>
                        <div class="mb-3">
                            <div class="row g-3">
                                <div class="col-4">
                                    <label class="form-label">Dài (cm)</label>
                                    <input type="text" id="length" name="length" class="form-control text-end input_money" value="0" placeholder="Nhập chiều dài">
                                </div>
                                <div class="col-4">
                                    <label class="form-label">Cao (cm)</label>
                                    <input type="text" id="height" name="height" class="form-control text-end input_money" value="0" placeholder="Nhập chiều cao">
                                </div>
                                <div class="col-4">
                                    <label class="form-label">Rộng (cm)</label>
                                    <input type="text" id="width" name="width" class="form-control text-end input_money" value="0" placeholder="Nhập chiều rộng">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Yêu cầu giao hàng</label>
                            <select class="form-control select2" id="require_transport_option" name="require_transport_option" data-toggle="select2">
                                <option value="KHONGCHOXEMHANG">Không cho xem hàng</option>
                                <option value="CHOXEMHANGKHONGTHU">Cho xem hàng không cho thử</option>
                                <option value="CHOTHUHANG">Cho thử hàng</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Bên trả phí</label>
                            <select class="form-control select2" id="payment_type_id" name="payment_type_id" data-toggle="select2">
                                <option value="1">Shop trả</option>
                                <option value="2">Người nhận trả</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="example-textarea" class="form-label">Ghi chú</label>
                            <textarea class="form-control" name="note" id="note_transport" rows="5"></textarea>
                        </div>
                        <div class="mb-3">
                            <button type="button" class="btn btn-primary w-100 btn_char_fee_transport">Tính lại phí</button>
                        </div>
                    </div>
                </div>
                <div class="right col-md-8" id="right">
                    <div class="tab-content">
                        <div class="tab-pane" id="tab-option1">
                            <div class="option-transport" id="option-transport">
    
                            </div>
                        </div>
                        <div class="tab-pane" id="tab-option2">
                            <div>
                                <h4>Đối tác giao hàng</h4>
                                <hr>
                                <div class="">
                                    <h5>Loại giao hàng</h5>
                                    <div class="row align-items-center g-2 mb-3">
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input type="radio" id="shiper" value="SHIPPER" name="loai_giao_hang" class="form-check-input">
                                                <label class="form-check-label" for="shiper">Shipper tự liên hệ</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input type="radio" id="chanh_xe" value="CHANH_XE" name="loai_giao_hang" class="form-check-input">
                                                <label class="form-check-label" for="chanh_xe">Hãng vận chuyển ngoài</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="">Chọn đối tác giao hàng</label>
                                            <select name="shipping_partner_id" id="shipping_partner_id" data-toggle="select2">
                                                <option value="">-- Chọn đối tác giao hàng --</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row align-items-center g-2 justify-content-end mb-3">
                                        <div class="col-md-6 mb-3">
                                            <label for="">Phí trả đối tác vận chuyển</label>
                                            <input type="text" name="shipping_fee"  id="shipping_fee" class="form-control text-end input_money" value="0">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="tab-option3">Nội dung nhận tại cửa hàng</div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<div class="list-button mb-3" id="list_button_action">
    <div class="item d-flex flex-wrap gap-2 justify-content-end">
        <button type="button" class="btn btn-outline-primary">Thoát</button>
        <button type="button" class="btn btn-primary" id="btn_order">
            <span>Tạo đơn hàng</span>
            <span class="spinner-border spinner-border-sm" style="display:none" role="status" aria-hidden="true"></span>
        </button>
    </div>
</div>


<div id="order_modal_error" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content modal-filled bg-danger">
            <div class="modal-body p-4">
                <div class="text-center">
                    <i class="ri-close-circle-line h1"></i>
                    <h4 class="mt-2">Có lỗi, vui lòng kiểm tra lại</h4>
                    <div id="html_elements"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@push('js')
<script>
    const div_error = @json(@include('admin.shop.modals.div-error'));
    let data_prod = [];
    let data_response_transport = [];
    let get_transport = @json($get_transport);
    const SHIPPING_FEES = @json($shipping_fees);
</script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script>
    // Chứa các function
    function formatCustomer(repo) {
        if (repo.loading) {
            return repo.text;
        }
        
        let $box_item = $(`
            <div class="customer-item row">
                <div class="col-auto">
                    <div class="avatar-sm">
                        <img src="/assets/images/avatar-trang.jpg" alt="" class="rounded-circle img-thumbnail">
                    </div>    
                </div>
                <div class="col">
                    <div>${repo.full_name}</div>
                    <div>${repo.phone}</div>
                </div>
            </div>
        `);

        if(repo.id === -1) {
            $box_item = $(`
                <div class="customer-item row" id="div-create-new">
                    <div class="item-create">
                        <i class="ri-add-box-line text-white"></i>
                        <span class="text-white">${repo.full_name}</span>    
                    </div>
                </div>
            `);
        }

        return $box_item;
    }

    function formatCustomerSelection(repo) {
        return repo.text;
    }

    function renderCustomer(data) {
        return `
            <div class="data-customer">
                <div class="d-flex flex-wrap align-items-center gap-1">
                    <strong class='text-primary'>${data.full_name}</strong> - <strong>${data.phone}</strong>
                    <a href="javascript:;" id="clear_customer" class""><i class="ri-close-circle-fill fs-3"></i></a>
                </div>
                <hr>
                <div class="address">
                    <div class="title d-flex flex-wrap gap-1 align-items-center">
                        <h5>Địa chỉ giao hàng</h5>
                        <a href="javascript:;" class="d-none" id="change_info_customer">Thay đổi</a>
                    </div>
                    <div class="info mt-1">
                        <div class="d-flex flex-column gap-2">
                            <input type="text" name="customer_full_name" class="form-control" id="customer_full_name" value="${data.full_name}" />
                            <input type="text" name="customer_phone" class="form-control" id="customer_phone" value="${data.phone}" />
                            <div class="row g-1 bg-light p-1">
                                <div class="col-6">
                                    <select class="form-control select2" data-toggle="select2" name="province_code" id="province_code">
                                        <option value=''>Chọn tỉnh thành</option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <select class="form-control select2" data-toggle="select2" name="ward_code" id="ward_code">
                                        <option value=''>Chọn phường xã</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <input type="text" id="address" name="address" value="${data.address}" class="form-control" placeholder="-- Nhập địa chỉ --">
                                </div>
                                <div class="col-12 text-center">
                                    <button class="btn btn-primary w-100 btn_char_fee_transport" id="btn_char_fee_transport" disabled>Tính lại cước phí vận chuyển</button>    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    function _loadProvince(file_json, $select, value_trigger = null) {        
        $.getJSON(file_json, function(data) {
            $select.find('option[value!=""]').remove();

            $.each(data, function(index, item) {
                let selected = value_trigger && value_trigger == item.code ? 'selected' : '';
                $select.append('<option value="' + item.code + '" ' + selected + '>' + item.name + '</option>');
            });
        }).fail(function() {
            $select.empty().append('<option value="-100">Không tải được dữ liệu</option>');
        });
    }

    function _loadProvinceNew(type = 'provinces', province_id = null, ward_id = null, $select_province, $select_ward, value_trigger = null) {        
        $.ajax({
            url: @json(route('admin.province.get_province')),
            method: "GET",
            data: {
                province_id: province_id,
                ward_id: ward_id,
                type: type,
            },
            beforeSend: function(){
                if(type === 'all'){
                    $select_province.find("option[value!='']").remove();
                    $select_ward.find("option[value!='']").remove();
                } else {
                    let $select = $select_province;
                    if(type === 'wards') {
                        $select = $select_ward;
                    }
                    $select.find("option[value!='']").remove();
                }
            },
            success: function(res){
                if(res.success) {
                    if(type === 'all') {

                        let options = "<option></option>";
                        $.each(res.data.provinces, function(index, item){
                            options += `<option ${province_id && province_id == item.id ? "selected" : ''} value="${item.id}">${item.text}</option>`;
                        });
                        $select_province.html(options);

                        options = "<option></option>";
                        $.each(res.data.wards, function(index, item){
                            options += `<option ${ward_id && ward_id == item.id ? "selected" : ''} value="${item.id}">${item.text}</option>`;
                        });
                        $select_ward.html(options);


                    } else {
                        let results = res.data[type];

                        let $select = $select_province;

                        let text = "Chọn tỉnh thành"
                        if(type === 'wards') {
                            text = "Chọn phường xã";
                            $select = $select_ward;
                        }

                        results.unshift({
                            id: "",
                            text: text,
                        });
                        $select.select2({
                            data: res.data[type],
                        });
                    }
                }
            }
        });
    }

    function changeHtml(){
        let index = 1;
        let index_name_option = 0;
        ELEMENTS_PRODUCT.table_product.find('tbody tr.product_item').each(function(item){
            let $this = $(this);

            $this.find('.quantity').find('.product_quantity').data('index', index_name_option);
            console.log($this.find('.quantity').find('.product_quantity').data('index'));
            
            $this.find('td:last-child').find('.delete_item').data('index', index_name_option);
            $this.find('td:eq(0)').text(index);
            let parent_discount = $this.find('td:eq(4)').find('.parent_discount');
            parent_discount.data('name', `options_${index_name_option}`);
            parent_discount.data('index', index_name_option)
            
            let btn_left_input = parent_discount.find('div:eq(0)');
            btn_left_input.find('input.btn_discount').attr('name', `options_${index_name_option}`);
            btn_left_input.find('input.btn_discount').attr('id', `option_left_${index_name_option}`);
            btn_left_input.find('label').attr('for', `option_left_${index_name_option}`);

            let btn_right_input = parent_discount.find('div:eq(1)');
            btn_right_input.find('input.btn_discount').attr('name', `options_${index_name_option}`);
            btn_right_input.find('input.btn_discount').attr('id', `option_right_${index_name_option}`);
            btn_right_input.find('label').attr('for', `option_right_${index_name_option}`);
            
            index++;
            index_name_option++;
        })
    }

    function renderItem(data, index){    
        let image = data.image_url ? ASSETS.url_storage.replace(':image_url', data.image_url) : false;
        if(!image) {
            image = ASSETS.url_no_image;
        }   
        return `
            <tr class="product_item" data-product="${data.id}" data-units='${JSON.stringify({
                length: data.length,
                width: data.width,
                height: data.height,
                weight: data.weight,
            })}'>
                <td>
                    <div class="text-center">${index+1}</div>
                </td>
                <td>
                    <img src="${image}" alt="contact-img" title="contact-img" class="rounded me-1" height="64">
                    <p class="m-0 d-inline-block align-middle font-16">
                        <a href="apps-ecommerce-products-details.html" class="text-body">${data.name}</a>
                        <br>
                        <small><b>Danh mục:</b> ${data.category.name}
                        </small>
                        <input type="hidden" name="product_name[]" value="${data.name}"/>
                    </p>
                </td>
                <td class="quantity">
                    <input data-toggle="touchspin" name="product_quantity[]" data-index=${index} data-bts-min="1" data-bts-max="999" value="1" data-btn-vertical="true" type="text" class="form-control text-center product_quantity">
                </td>
                <td>
                    <div class="text-center">${Number(data.price).toLocaleString('vi')} <input type="hidden" name="product_price[]" value="${data.price}"/></div>
                </td>
                <td>
                    <div class="d-flex flex-wrap align-items-center parent_discount" data-index="${index}" data-name="options_${index}" data-price="${Number(data.price)}">
                        <div class="">
                            <input type="radio" class="btn-check btn_discount" name="options_${index}" id="option_left_${index}" autocomplete="off" value="1" checked>
                            <label class="btn btn-outline-primary" for="option_left_${index}">Giá trị</label>
                        </div>
                        <div class="">
                            <input type="radio" class="btn-check btn_discount" name="options_${index}" id="option_right_${index}" autocomplete="off" value="2">
                            <label class="btn btn-outline-primary" for="option_right_${index}">%</label>
                        </div>
                        <div class="col">
                            <div class="input-group bootstrap-touchspin bootstrap-touchspin-injected">
                                <input type="text" name="product_discount[]" oninput="this.value = this.value.replace(/[^0-9]/g, '');" class="form-control text-end input_discount" value="0">
                                <span class="input-group-addon bootstrap-touchspin-postfix input-group-append">
                                    <span class="input-group-text unit">đ</span>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="discount text-end" style="display:none;">
                        <span class="small text-danger"></span>
                    </div>
                </td>
                <td class="total_price">
                    <div class="text-center">${Number(data.price).toLocaleString('vi')}</div>
                </td>
                <td class="bg-danger text-white">
                    <a href="javascript:void(0);" class="action-icon delete_item" data-index=${index}> <i class="ri-close-line"></i></a>
                </td>
            </tr>
        `;
    }

    function formatRepoProduct(repo){
        if (repo.loading) {
            return repo.text;
        }
        let assets_storage = ASSETS.url_storage;
        let image = repo.image_url ? assets_storage.replace(':image_url', repo.image_url) : false;
        if(!image) {
            image = ASSETS.url_no_image;
        }

        let text_price_province = CARD_INFO_CUSTOMER.find('#province_code option:selected').text();
        text_price_province = text_price_province ? `Giá theo khu vực (${text_price_province}): ` : '';

        var $container = $(`
            <div class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <div class="d-flex">
                        <div style="width: 50px; height: 65px;">
                            <img src="${image}" class="w-100 h-100 object-fit-cover">
                        </div>
                        <div class="ms-2">
                            <div class="fw-bold">${repo.name}</div>
                            <div class="">${repo.sku ? repo.sku : ""}</div>
                        </div>
                    </div>
                </div>
                <div class="text-end">
                    <span class="fw-bold">${text_price_province} ${Number(repo.price).toLocaleString("vi")}</span>
                </div>
            </div>
        `);

        return $container;
        
    }

    function formatRepoSelection(repo){
        return repo.text;
    }

    function calculateTotalProduct(){
        CARD_TOTAL.label_total_quantity_order.text(data_prod.length);
        CARD_TOTAL.input_total_balance.val(data_prod.reduce((tich_tru, item) => tich_tru + item.total, 0 ).toLocaleString('vi'));
        CARD_TOTAL.coupon.val("").trigger("change");
        $("#total_after_coupon").val(data_prod.reduce((tich_tru, item) => tich_tru + item.total, 0 ).toLocaleString('vi'));
        CARD_TOTAL.discount_total.val(0).trigger('keyup')
    }

    function suggestCubeDimensions(weightGram) {
        const side = Math.ceil(Math.cbrt(weightGram));

        return {
            length: 10,
            width: 10,
            height: 10,
        };
    }

    function handleDateLeadTime(value) {
        let date = value
        date = new Date(date);

        // Lấy ngày/tháng/năm
        let day = String(date.getUTCDate()).padStart(2, '0');
        let month = String(date.getUTCMonth() + 1).padStart(2, '0'); // Tháng bắt đầu từ 0
        let year = date.getUTCFullYear();

        date = `${day}/${month}/${year}`;

        return date
    }

    function apiGetFee(url, params, method = 'POST') {
        return $.ajax({
            url: url,
            type: method,
            data: {
                _token: $("[name='csrf-token']").attr('content'),
                data: params,
                pick_address_id: $("#pick_address_id").val(),
                customers: JSON.stringify({
                    id: ELEMENTS_INFO_CUSTOMER.select_find_customer.val(),
                    full_name: CARD_INFO_CUSTOMER.find('#customer_full_name').val(),
                    phone: CARD_INFO_CUSTOMER.find('#customer_phone').val(),
                    province: CARD_INFO_CUSTOMER.find('#province_code option:selected').text(),
                    district: CARD_INFO_CUSTOMER.find('#district_code option:selected').text(),
                    ward: CARD_INFO_CUSTOMER.find('#ward_code option:selected').text(),
                    address: CARD_INFO_CUSTOMER.find('#address').val(),
                }),
                length: $("#length").val().replaceAll('.', ''),
                height: $("#height").val().replaceAll('.', ''),
                width: $("#width").val().replaceAll('.', ''),
                weight: $("#gam").val().replaceAll('.', ''),
                address: $("#customer_address").val(),
            },
        });
    }
</script>
<script>
    // JS xử lý địa chỉ khách hàng khi show popup thêm mới

    $("#modal_add_new_customer").find("#province_code").change(function(e, value_trigger = null){
        let $this = $(this);
        let code = $this.val();
        if(code) {
            _loadProvinceNew('wards', code, null, null, $("#modal_add_new_customer").find("#ward_code"), null);
        } else {
            $("#modal_add_new_customer").find("#ward_code").find('option[value!=""]').remove();
        }
    })

    // JS xử lý khi modal ẩn đi thì sẽ clear hết value xã/phường
    $("#modal_add_new_customer").on('hidden.bs.modal', function(){
        let $this = $(this);
        $this.find("#province_code option[value!='']").remove();
        let default_province = {
            id: '',
            text: 'Chọn tỉnh thành',
        };
        $this.find("#province_code").select2({
            data: [default_province,...@json($get_provinces)]
        });
        $this.find("#ward_code option[value!='']").remove();
        $this.find("#address").val("");

        $this.find('#form_action')[0].reset();

       $this.find('#form_action').find('.form-control').removeClass('is-invalid');
       $this.find('#form_action').find('.invalid-feedback').empty();
    })

    // JS xử lý khi thêm mới khách hàng
    $("#modal_add_new_customer").find('#btn_add_new_customer').click(function(e){
        e.preventDefault();
        let $this = $(this);
        let form = $("#modal_add_new_customer").find('#form_action');
        $.ajax({
            url: @json(route('admin.customer.store')),            
            type: "POST",
            data: form.serialize(),
            beforeSend: function(){
                form.find('.form-control').removeClass('is-invalid');
                form.find('.invalid-feedback').empty();

                $this.prop("disabled", true);
                $this.find('#loading').show();
                $this.find('.add-new').hide();
            },
            success: function(res){
                if(res.success) {
                    $("#modal_add_new_customer").modal('hide');
                    createToast('success', res.message);

                    let new_item = {
                        id: res.data.id,     
                        text: res.data.full_name,
                        full_name: res.data.full_name,
                        phone: res.data.phone,
                        province_code: res.data.province_code,
                        ward_code: res.data.ward_code,
                        address: res.data.address,

                    };

                    let new_option = new Option(new_item.text, new_item.id, true, true);
                    ELEMENTS_INFO_CUSTOMER.select_find_customer.append(new_option);

                    ELEMENTS_INFO_CUSTOMER.select_find_customer.trigger({
                        type: 'select2:select',
                        params: {
                            data: new_item
                        }
                    })
                }
            },
            error: function(err){
                let response_err = err.responseJSON;
                if(response_err) {
                    $.each(response_err.errors, function(key, item){
                        if(key === 'gender') {
                            form.find('#male').parent().parent().addClass('is-invalid')
                            form.find('#male').parent().parent().next().text(item[0]);
                        } else if(key === 'province_code' || key === 'district_code' || key === 'ward_code'){
                            form.find("#"+key).addClass('is-invalid');
                            form.find("#"+key).next().next().text(item[0]);
                        } else {
                            form.find("#"+key).addClass('is-invalid');
                            form.find("#"+key).next().text(item[0]);
                        }
                    })
                }
            },
            complete: function(){
                $this.prop("disabled", false);
                $this.find('#loading').hide();
                $this.find('.add-new').show();
            }
        });
    })

    // Javascript xử lý thông tin khách hàng
    const CARD_INFO_CUSTOMER = $("#card_customer");
    const ELEMENTS_INFO_CUSTOMER = {
        select_find_customer: CARD_INFO_CUSTOMER.find("#customer"),
        empty_customer: CARD_INFO_CUSTOMER.find("#empty_customer"),
    };
    ELEMENTS_INFO_CUSTOMER.select_find_customer.select2({
        language: "vi",
        ajax: {
            url: CARD_INFO_CUSTOMER.attr('data-route'),
            delay: 250,
            type: 'GET',
            data: function(params){
                var query = {
                    search: params.term,
                    page: params.page || 1
                }

                return query;
            },
            processResults: function(response, params){
                params.page = params.page || 1;
                var results = response.data.data || [];
                
                results.unshift({
                    id: -1,
                    full_name: 'Tạo mới khách hàng',
                    phone: "",
                });
            
                return {
                    results: results,
                    pagination: {
                        more: response.data.current_page < response.data.last_page
                    }
                };
            },
        },
        templateResult: formatCustomer,
        templateSelection: formatCustomerSelection
    }).on('select2:select', function(e){
        let $this = $(this);
        let data = e.params.data;

        if(Number(data.id) === -1) {
            $("#modal_add_new_customer").modal('show');
            $this.val(null).trigger('change');
            return;
        }

        template = renderCustomer(data);
        ELEMENTS_INFO_CUSTOMER.empty_customer.addClass('d-none');
        ELEMENTS_INFO_CUSTOMER.select_find_customer.next(".select2-container").hide();
        ELEMENTS_INFO_CUSTOMER.empty_customer.after(template);
        CARD_INFO_CUSTOMER.find('.data-customer .select2').select2();
        _loadProvinceNew('all', data.province_code, data.ward_code, CARD_INFO_CUSTOMER.find('#province_code'), CARD_INFO_CUSTOMER.find('#ward_code'), null);

        if(!$("#user_order").val()){
            $("#user_order").val(data.id).trigger('change.select2');
        }

        if(!$("#user_consignee").val()){
            $("#user_consignee").val(data.id).trigger('change.select2');
        }

        if(!$("#user_payer").val()){
            $("#user_payer").val(data.id).trigger('change.select2');
        }
    });

    CARD_INFO_CUSTOMER.on('click', '#clear_customer', function (e) {
        e.preventDefault();
        ELEMENTS_INFO_CUSTOMER.select_find_customer.val("").trigger("change");
        $(".result > .data-customer").remove()
        ELEMENTS_INFO_CUSTOMER.empty_customer.removeClass('d-none');
        ELEMENTS_INFO_CUSTOMER.select_find_customer.next(".select2-container").show();
        ELEMENTS_INFO_CUSTOMER.select_find_customer.select2('open');
    })

    $(document).on('change', '#card_customer #province_code', function(e){
        let $this = $(this);
        let code = $this.val();
        if(code) {
            _loadProvinceNew('wards', code, null, null, CARD_INFO_CUSTOMER.find("#ward_code"), null);
        } else {
            CARD_INFO_CUSTOMER.find("#ward_code").find('option[value!=""]').remove();
        }

        if(ELEMENTS_PRODUCT.table_product.find('tbody tr').length > 0) {
            ELEMENTS_PRODUCT.table_product.find('tbody tr').each(function(index, item){
                item.querySelector('.delete_item').click();
            })
        }

        if($("input[name='object_transport']:checked").val() === "2") {
            $("[name='object_transport'][value='2']").trigger('change');
        }

        CARD_INFO_CUSTOMER.find('#btn_char_fee_transport').prop('disabled', false);
    })

    // $(document).on('change', '#province_code', function(){
    //     let $this = $(this);
    //     let code = $this.val();
    //     if(code) {
    //         _loadProvince(`/dist/quan-huyen/${code}.json`, CARD_INFO_CUSTOMER.find('#district_code'), null);
    //         CARD_INFO_CUSTOMER.find('#ward_code').find('option[value!=""]').remove();
    //     } else {
    //         CARD_INFO_CUSTOMER.find('#district_code').find('option[value!=""]').remove();
    //         CARD_INFO_CUSTOMER.find('#ward_code').find('option[value!=""]').remove();
    //     }
    //     CARD_INFO_CUSTOMER.find('#btn_char_fee_transport').prop('disabled', false);
    // })
    // $(document).on('change', '#district_code', function(){
    //     let $this = $(this);
    //     let code = $this.val();
    //     if(code){
    //         _loadProvince(`/dist/xa-phuong/${code}.json`, CARD_INFO_CUSTOMER.find('#ward_code'), null);
    //     } else {
    //         CARD_INFO_CUSTOMER.find('#ward_code').find('option[value!=""]').remove();
    //     }
    //     CARD_INFO_CUSTOMER.find('#btn_char_fee_transport').prop('disabled', false)
    // })
    $(document).on('change', '#ward_code, #address', function(){
        CARD_INFO_CUSTOMER.find('#btn_char_fee_transport').prop('disabled', false)
    })
    $(document).on('click', '.btn_char_fee_transport', function(e){
        e.preventDefault();

        if(!CARD_INFO_CUSTOMER.find('#province_code').val() || !CARD_INFO_CUSTOMER.find('#ward_code').val() || !CARD_INFO_CUSTOMER.find('#address').val()){
            alert("Vui lòng chọn đầy đủ địa chỉ nhận hàng");
            return;
        }

        if($("[name='object_transport']:checked:not([value='3'])").length === 0) {
            alert("Chỉ hoạt động khi chọn Đẩy qua hãng hoặc vận chuyển ngoài");
            return;
        }

        if($("[name='object_transport']:checked").val() == 1) {
            let is_confirm = confirm('Ấn Ok để tính lại giá cước');
            if(!is_confirm) {
                return;
            }

            $("[name='object_transport'][value='1']").trigger('change');
        }
    })

    // Javascript xử lý Chọn sản phẩm
    const CHOOSE_DISCOUNT = {
        value : 1,
        percent: 2
    };
    const CARD_PRODUCT = $("#products");
    const ELEMENTS_PRODUCT = {
        select_find_prod: CARD_PRODUCT.find('#info_product'),
        table_product: CARD_PRODUCT.find('#table_product'),
        empty_prod: CARD_PRODUCT.find("#empty_prod"),
        btn_infor_prod: CARD_PRODUCT.find('#btn_infor_product'),
    };

    ELEMENTS_PRODUCT.select_find_prod.select2({
        closeOnSelect: false,
        minimumInputLength: 0,
        language: "vi",
        ajax: {
            url: CARD_PRODUCT.attr('data-route'),
            delay: 250,
            type: 'GET',
            data: function(params){
                var query = {
                    search: params.term || '',
                    page: params.page || 1,
                    store_id: $("#store_id").val(),
                    province_id: CARD_INFO_CUSTOMER.find('#province_code').val(),
                }

                return query;
            },
            processResults: function(response, params){
                params.page = params.page || 1;
            
                return {
                    results: response.data.data,
                    pagination: {
                        more: response.data.current_page < response.data.last_page
                    }
                };
            },
        },
        templateResult: formatRepoProduct,
        templateSelection: formatRepoSelection
    }).on("select2:select", function(e){
        let $this = $(this);
        let data = $this.select2('data')[0];
        ELEMENTS_PRODUCT.empty_prod.hide();
        ELEMENTS_PRODUCT.table_product.removeClass('d-none');
        $this.val("").trigger("change");
        let index = ELEMENTS_PRODUCT.table_product.find('tbody tr').length;
        ELEMENTS_PRODUCT.table_product.find('tbody').append(renderItem(data, index));
        $('[data-toggle="touchspin"], .touchspin').TouchSpin();

        data_prod.push({
            name: data.name,
            price: Number(data.price),
            quantity: 1,
            total: Number(data.price) * 1,
        });

        calculateTotalProduct();
    });

    CARD_PRODUCT.on('click', '.delete_item', function () {
        let $this = $(this);
        let index = $this.data('index');
        $this.parents('.product_item').remove();
        if(ELEMENTS_PRODUCT.table_product.find('tbody tr').length === 0) {
            ELEMENTS_PRODUCT.empty_prod.show();
            ELEMENTS_PRODUCT.table_product.addClass('d-none');
            ELEMENTS_PRODUCT.select_find_prod.select2('open');
            CARD_TOTAL.customer_paid_total.val(0);

            if ($("input[name='options']:checked").val() == 1) {
                $("#cod").val(0);
                $("#gam").val(0);
                $("#length").val(0);
                $("#width").val(0);
                $("#height").val(0);
                $("input[name='options']").prop("checked", false);
                $("#option-transport").empty();
                $("#left").hide();
            }
        }
        else {
            
            if ($("input[name='options']:checked").val() == 1) {
                $("#cod").val(0);
                $("#gam").val(0);
                $("#length").val(0);
                $("#width").val(0);
                $("#height").val(0);
                $("input[name='options'][value='1']").trigger('change');
            }
            changeHtml();
        }

        data_prod.splice(index, 1);
        calculateTotalProduct();
    })

    ELEMENTS_PRODUCT.btn_infor_prod.click(function(e){
        e.preventDefault();
        ELEMENTS_PRODUCT.select_find_prod.select2('open');
    })

    CARD_PRODUCT.on('keyup', '.input_discount', function(){
        let $this = $(this);
        let value = Number($this.val());
        let product_item = $this.parents('.product_item');
        let quantity = Number(product_item.find('.quantity').find('input').val());
        let parent_discount = $this.parents('.parent_discount');
        let index = parent_discount.data('index');
        
        let name_radio = parent_discount.data('name');
        let price = Number(parent_discount.data('price')) * quantity;
        let is_choose_discount = $(`[name='${name_radio}']:checked`).val();
        let total = 0;
        let unit = "";
        if(parseInt(is_choose_discount) === CHOOSE_DISCOUNT.percent) {
            unit = "%";
            if(value > 100) {
                $this.val(100);
                value = 100;
            }

            let total_discount = (price * value) / 100;
            total = price - total_discount;

            if(value > 0) {
                parent_discount.next().find('span').text((total_discount * -1).toLocaleString('vi'));
                parent_discount.next().show();
            } else {
                parent_discount.next().find('span').empty();
                parent_discount.next().hide();
            }

        } else {
            unit = "đ";
            parent_discount.next().find('span').empty();
            parent_discount.next().hide();

            if(value > price) {
                $this.val(price)
                value = price;
            }
            total = price - value;
        }

        $this.val(value.toLocaleString('vi'));
        product_item.find('.total_price').find('div').text(total.toLocaleString('vi'))
        product_item.find('span.unit').text(unit);

        data_prod[index].total = total;
        calculateTotalProduct();
    }).on('blur', '.input_discount', function () {
        let $this = $(this);
        if($this.val() === "" || $this.val() < 0) {
            $this.val(0);
        }
    })

    CARD_PRODUCT.on('change', '.btn_discount', function () {
        let $this = $(this);
        let product_item = $this.parents('.product_item');
        let parent_discount = $this.parents('.parent_discount');
        let quantity = Number(product_item.find('.quantity').find('input').val());
        let price = Number(parent_discount.data('price')) * quantity;
        let input_discount = parent_discount.find('.input_discount').val(0).trigger("change");
        product_item.find('.total_price').find('div').text(Number(price).toLocaleString('vi'));
        let unit = "";
        if(parseInt($this.val()) === CHOOSE_DISCOUNT.percent) {
            unit = "%";
        } else {
            unit = "đ";
            parent_discount.next().find('span').empty();
            parent_discount.next().hide();
        }
        product_item.find('span.unit').text(unit);
    })
    CARD_PRODUCT.on('change', '.product_quantity', function (e) {
        e.preventDefault();
        let $this = $(this);
        let index = $this.data('index');
        let quantity = Number($this.val());
        let product_item = $this.parents('.product_item');
        let parent_discount = product_item.find('.parent_discount')
        let price = Number(parent_discount.data('price'));
        parent_discount.find('.input_discount').val(0).trigger('keyup');
        product_item.find('.total_price > div').text(Number(price * quantity).toLocaleString('vi'));

        data_prod[index].quantity = quantity;
        data_prod[index].total = Number(price * quantity);
        calculateTotalProduct();

        if ($("input[name='options']:checked").val() == 1) {
            $("#cod").val(0);
            $("#gam").val(0);
            $("#length").val(0);
            $("#width").val(0);
            $("#height").val(0);
            $("input[name='options'][value='1']").trigger('change');
        } else if ($("input[name='options']:checked").val() == 2) {
            let items = ELEMENTS_PRODUCT.table_product.find('tbody tr.product_item');
            let weight = 0;
            $.each(items, function () { 
                let $this = $(this);
                let units = $this.attr('data-units');
                weight += Number(JSON.parse(units).weight) * $this.find('.product_quantity').val();
            });


            let unit_suggest = suggestCubeDimensions(weight);
            if (!$("#length").val() || $("#length").val() === '0') {
                $("#length").val(unit_suggest.length.toLocaleString('vi'));
            }
            if (!$("#height").val() || $("#height").val() === '0') {
                $("#height").val(unit_suggest.height.toLocaleString('vi'));
            }
            if (!$("#width").val() || $("#width").val() === '0') {
                $("#width").val(unit_suggest.width.toLocaleString('vi'));
            }
            if (!$("#gam").val() || $("#gam").val() === '0') {
                $("#gam").val(weight.toLocaleString('vi'));
            }
        }
    })

    // Javascript xử lý tổng đơn hàng
    const ELEMENTS_TOTAL = $("#result_info_product");
    const CARD_TOTAL = {
        label_total_quantity_order: ELEMENTS_TOTAL.find("#cnt_total_product"),
        input_total_balance: ELEMENTS_TOTAL.find("#total"),
        discount_total: ELEMENTS_TOTAL.find("#discount_total"),
        discount_total_money: ELEMENTS_TOTAL.find("#discount_total_money"),
        coupon: ELEMENTS_TOTAL.find("#coupon"),
        customer_paid_total: ELEMENTS_TOTAL.find("#customer_paid_total"),
        customer_has_paid_total: ELEMENTS_TOTAL.find("#customer_has_paid_total"),
        total_end: ELEMENTS_TOTAL.find("#total_end"),
    };

    CARD_TOTAL.coupon.on('change', function(){
        let $this = $(this);
        let data_item_coupon = $this.find('option:selected').attr('data-item');
        
        let total = 0;
        if(data_item_coupon) {
            data_item_coupon = JSON.parse(data_item_coupon);
            let total_order = Number(CARD_TOTAL.input_total_balance.val().replaceAll('.', ''));

            if(data_item_coupon.type === "PHAN_TRAM") {
                total = total_order - (total_order * parseFloat(data_item_coupon.fee)) / 100;
                
                if(total > total_order) {
                    total = total_order;
                }

            } else {
                total = total_order - parseFloat(data_item_coupon.fee);
            }
        } 

        total = Math.round(total);
        
        $("#total_after_coupon").val(total.toLocaleString('vi'));

        CARD_TOTAL.customer_paid_total.val(total.toLocaleString('vi'));
        CARD_TOTAL.discount_total.val(0).trigger('keyup');
        CARD_TOTAL.customer_has_paid_total.val(0).trigger('keyup');
        
    })

    CARD_TOTAL.discount_total.keyup(function(e){
        let $this = $(this);

        CARD_TOTAL.customer_has_paid_total.val(0).trigger('keyup');

        // let total_balance = Number(CARD_TOTAL.input_total_balance.val().replaceAll('.', ''));
        let total_balance = Number($("#total_after_coupon").val().replaceAll('.', ''));
        if(total_balance === 0) {
            let $this = $(this).val(0);
            return;
        }

        let value = Number($this.val().replaceAll('.', ''));

        if(value >= 100) {
            $this.val(100);
            value = 100;
        }

        if(value <= 0 || value === "") {
            $this.val(0);
            value = 0;
            CARD_TOTAL.discount_total_money.addClass('d-none');
            CARD_TOTAL.discount_total_money.find('span').text(0);
        }

        let discount_total_money = -(total_balance * value) / 100;
        
        if(discount_total_money !== -0){
            CARD_TOTAL.discount_total_money.removeClass('d-none');  
            discount_total_money = Math.round(discount_total_money);              
            CARD_TOTAL.discount_total_money.find('span').text(discount_total_money.toLocaleString('vi'))
        }

        let customer_paid_total = total_balance + discount_total_money;
        customer_paid_total = Math.round(customer_paid_total);
        CARD_TOTAL.customer_paid_total.val(customer_paid_total.toLocaleString('vi'))

    })

    CARD_TOTAL.customer_has_paid_total.keyup(function(e){
        let $this = $(this);

        let value = Number($this.val().replaceAll('.', ''));

        let customer_paid_total = Number(CARD_TOTAL.customer_paid_total.val().replaceAll('.', ''));
        // let total_balance = Number(CARD_TOTAL.input_total_balance.val().replaceAll('.', ''));
        let total_balance = Number($("#total_after_coupon").val().replaceAll('.', ''));
        if(total_balance === 0 && customer_paid_total === 0) {
            $this.val(0);
            value = 0;
            return;
        }

        if(customer_paid_total === 0) {
            $this.val(0);
            value = 0;
            CARD_TOTAL.total_end.val(0);
            return;
        }

        if(value === 0) {
            CARD_TOTAL.total_end.val(0);
            return;
        }

        let total_end = customer_paid_total - value;
        total_end = Math.round(total_end);
        
        CARD_TOTAL.total_end.val(total_end.toLocaleString('vi'));

        if(total_end >= 0) {
            $("#cod").val(total_end.toLocaleString('vi'));
        } else {
            $("#cod").val(0);
        }
    })

    // Javascript xử lý phần vận chuyển
    $("input[name='object_transport']").on('change', async function () {
        $("input[name='object_transport']").prop("disabled", true);
        let $this = $(this);
        let id = $this.attr('id');

        if ($this.val() == 1) {
            let weight = 0;
            try {
                let items = ELEMENTS_PRODUCT.table_product.find('tbody tr.product_item');
                let data = [];
                $.each(items, function () { 
                    let $this = $(this);
                    let units = $this.attr('data-units');
                    weight += Number(JSON.parse(units).weight) * $this.find('.product_quantity').val();

                    data.push({
                        product_id: $this.attr('data-product'),
                        quantity: $this.find('.product_quantity').val(),
                    });
                });
                data = JSON.stringify(data);
                data.weight = weight;
                let unit_suggest = suggestCubeDimensions(weight);
                if (!$("#length").val() || $("#length").val() === '0') {
                    $("#length").val(unit_suggest.length.toLocaleString('vi'));
                }
                if (!$("#height").val() || $("#height").val() === '0') {
                    $("#height").val(unit_suggest.height.toLocaleString('vi'));
                }
                if (!$("#width").val() || $("#width").val() === '0') {
                    $("#width").val(unit_suggest.width.toLocaleString('vi'));
                }
                if (!$("#gam").val() || $("#gam").val() === '0') {
                    $("#gam").val(weight.toLocaleString('vi'));
                }
                data_fee = await apiGetFee("/admin/order/apiGetFee", data);
                if (data_fee.success) {
                    html = "";
                    let data = data_fee.data;
                    data_response_transport = data;
                    $.each(data, function (key, item) {
                        image = "";
                        
                        if (key === "GHN") {
                            image = "/assets/images/transport/logo-ghn-new.png";
                            // let from_date = handleDateLeadTime(item.get_leadtime.leadtime_order.from_estimate_date);
                            // let to_date = handleDateLeadTime(item.get_leadtime.leadtime_order.to_estimate_date);
                            
                            html += `
                                <div class="form-check mb-2 item d-flex align-items-center gap-2">
                                    <input type="radio" id="${key}" name="hang_van_chuyen" value="${key}" class="form-check-input">
                                    <label class="form-check-label" for="${key}">
                                        <img height="35px" src="${image}" alt="image" class="">
                                        <span><b>Giao hàng nhanh</b></span>
                                       
                                        <div class="fee">
                                            Cước phí: <b>${(item.total).toLocaleString("vi")}</b>
                                        </div>
                                    </label>
                                </div>
                            `;
                        } else if (key === "GHTK") {
                            image = "/assets/images/transport/logo-ghtk.png";

                            let disabled = "disabled"
                            let text = "GTHK chưa hỗ trợ giao đến khu vực này";
                            if (item.fee.delivery) {
                                disabled = "";
                                text = "";
                            }

                            html += `
                                <div class="form-check mb-2 item d-flex align-items-center gap-2">
                                    <input type="radio" id="${key}" name="hang_van_chuyen" value="${key}" class="form-check-input" ${disabled}>
                                    <label class="form-check-label" for="${key}">
                                        <img height="35px" src="${image}" alt="image" class="">
                                        <span><b>Giao hàng tiết kiệm</b></span>
                                        <div class="fee">
                                            Cước phí: <b>${(item.fee.fee.toLocaleString('vi'))}</b>
                                        </div>
                                        ${text}
                                    </label>
                                </div>
                            `;
                        }
                        
                    })
                    $("#option-transport").html(html);
                    swal({
                            icon: "success",
                            title: "Thành công",
                            button: "Đóng",
                            text: "Đã tính phí vận chuyển",
                    })
                }
                
            } catch (error) {
                console.log(error);
                $("body").find("#popup_show_err").remove();
                $("body").append(`
                    <div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="popup_show_err">
                        <div class="modal-dialog modal-sm">
                            <div class="modal-content modal-filled bg-danger">
                                <div class="modal-body p-4">
                                    <div class="text-center">
                                        <i class="ri-close-circle-line h1"></i>
                                        <h4 class="mt-2">Có lỗi!</h4>
                                        <p class="mt-3">${error.responseJSON.message}</p>
                                        <button type="button" class="btn btn-light my-2" data-bs-dismiss="modal">Đóng</button>
                                    </div>
                                </div>
                            </div><!-- /.modal-content -->
                        </div><!-- /.modal-dialog -->
                    </div><!-- /.modal --> 
                    `);
                $("body").find("#popup_show_err").modal('show');
                $("input[name='object_transport']").prop('checked', false);
                $("input[name='object_transport']").prop("disabled", false);
                return;
            }
        } else if ($this.val() == 2) {
            let items = ELEMENTS_PRODUCT.table_product.find('tbody tr.product_item');
            let weight = 0;
            $.each(items, function () { 
                let $this = $(this);
                let units = $this.attr('data-units');
                weight += Number(JSON.parse(units).weight) * $this.find('.product_quantity').val();
            });
            let unit_suggest = suggestCubeDimensions(weight);
            if (!$("#length").val() || $("#length").val() === '0') {
                $("#length").val(unit_suggest.length.toLocaleString('vi'));
            }
            if (!$("#height").val() || $("#height").val() === '0') {
                $("#height").val(unit_suggest.height.toLocaleString('vi'));
            }
            if (!$("#width").val() || $("#width").val() === '0') {
                $("#width").val(unit_suggest.width.toLocaleString('vi'));
            }
            if (!$("#gam").val() || $("#gam").val() === '0') {
                $("#gam").val(weight.toLocaleString('vi'));
            }

           let province_id = CARD_INFO_CUSTOMER.find('#province_code').val();
           let find_fee_province = SHIPPING_FEES.find(function(item){
                return Number(item.province_id) === Number(province_id);
            });

            if(!find_fee_province) {
                find_fee_province = SHIPPING_FEES.find(function(item){
                    return Number(item.province_id) === -1;
                });
            }

            $("#shipping_fee").val(parseFloat(find_fee_province.fee).toLocaleString('vi')).trigger('keyup');
        }

        if (id === 'option4') {
            $("#left").addClass('d-none');
            $("#right").removeClass('d-none');
        } else if (id === 'option3') {
            $("#left").addClass('d-none');
            $("#right").addClass('d-none');
        } else {
            $("#left").removeClass('d-none');
            $("#right").removeClass('d-none');
        }

        $(".tab-content").find('.tab-pane.active').removeClass('active');

        $(".tab-content").find(`#tab-${id}`).addClass('active');

        $("input[name='object_transport']").prop("disabled", false);
    });

    $(document).on('change', "[name='hang_van_chuyen']", function(){
        $("#payment_type_id").val($("#payment_type_id").val()).trigger('change');
    })

    $("#payment_type_id").change(function () {
        if ($("[name='object_transport']:checked").val() == 1 || $("[name='object_transport']:checked").val() == 2) {
            let total_end = CARD_TOTAL.total_end.val().replaceAll(".", "") * 1;
            let fee = 0;

            if($("[name='object_transport']:checked").val() == 2) {
                fee = $("#shipping_fee").val().replaceAll(".", "") * 1;
            } else {
                let hang_van_chuyen = $("[name='hang_van_chuyen']:checked").val();
                if(hang_van_chuyen === "GHTK") {
                    fee = data_response_transport['GHTK'].fee.fee;
                } else if(hang_van_chuyen === "GHN"){
                    fee = data_response_transport['GHN'].total;
                }
            }

            if ($(this).val() == 2) {
                $("#cod").val(Number(total_end + fee).toLocaleString('vi'))
            } else {
                $("#cod").val(Number(total_end).toLocaleString('vi'))
            }
        }
        
    })

    $("#shipping_fee").on('keyup', function (e) {

        if ($("[name='object_transport']:checked").val() != 2) {
            return;
        }

        let $this = $(this);
        let fee = $this.val().replaceAll(".", "") * 1;
        let total_end = CARD_TOTAL.total_end.val().replaceAll(".", "") * 1;

        if ($("#payment_type_id").val() == 1) {
            $("#cod").val(Number(total_end).toLocaleString('vi'))
        } else {
            $("#cod").val(Number(total_end + fee).toLocaleString('vi'))
        }

    })

    $("input[type='radio'][name='loai_giao_hang']").change(function (e) {
        let $this = $(this);
        let value = $this.val();
        
        let data_transport = get_transport.filter(function (item) {
            return item.role === value;
        });
    
        $("select#shipping_partner_id").val("");
        $("select#shipping_partner_id option[value!='']").remove();
        $("select#shipping_partner_id").select2({
            data: data_transport
        })
    });

    $("#btn_order").click(function(){
        let $this = $(this);

        const customer = JSON.stringify({
            id: ELEMENTS_INFO_CUSTOMER.select_find_customer.val(),
            full_name: CARD_INFO_CUSTOMER.find('#customer_full_name').val(),
            phone: CARD_INFO_CUSTOMER.find('#customer_phone').val(),
            province: CARD_INFO_CUSTOMER.find('#province_code').val(),
            district: CARD_INFO_CUSTOMER.find('#district_code').val(),
            ward: CARD_INFO_CUSTOMER.find('#ward_code').val(),
            province_text: CARD_INFO_CUSTOMER.find('#province_code option:selected').text(),
            district_text: CARD_INFO_CUSTOMER.find('#district_code option:selected').text(),
            ward_text: CARD_INFO_CUSTOMER.find('#ward_code option:selected').text(),
            address: CARD_INFO_CUSTOMER.find('#address').val(),
        });

        if(!ELEMENTS_INFO_CUSTOMER.select_find_customer.val()) {
            alert("Vui lòng chọn khách mua hàng");
            return;
        }

        const pick_address_id = $("#pick_address_id").val();

        if(!pick_address_id) {
            alert("Vui lòng chọn cửa hàng");
            return;
        }

        const source = $("#source").val();

        if(!source) {
            alert("Vui lòng chọn nguồn bán");
            return;
        }

        // Sản phẩm
        let form_table_product = $("#form_table_product");
        let product_items = form_table_product.find('tr.product_item')
        let products = [];
        $.each(product_items, function (index, item) {
            let $this = $(this);
            products.push({
                product_id: $this.attr('data-product'),
                quantity: $this.find('input.product_quantity').val().replaceAll(".", ""),
                is_option: $this.find(`[name="options_${index}"]:checked`).val(),
                discount: $this.find('input.input_discount').val().replaceAll(".", ""),
            });
        })

        if(products.length === 0) {
            alert("Vui lòng chọn sản phẩm");
            return;
        }

        // Tổng tiền (Lấy tiền khách trả, giảm giá bao nhiêu % và ghi chú đơn hàng)
        let result_info_product = $("#result_info_product");
        const note = result_info_product.find("#note_total").val();
        const discount_total = result_info_product.find("#discount_total").val().replaceAll(".", "");
        const customer_has_paid_total = result_info_product.find("#customer_has_paid_total").val().replaceAll(".", "");

        // Vận chuyển
        let element_package_and_delivery = $("#package_and_delivery");
        let client_request_transport = {
            type: element_package_and_delivery.find("input[type='radio'][name='object_transport']:checked").val(),
            cod: element_package_and_delivery.find("#cod").val().replaceAll('.', ''),
            gam: element_package_and_delivery.find("#gam").val().replaceAll('.', ''),
            length: element_package_and_delivery.find("#length").val().replaceAll('.', ''),
            width: element_package_and_delivery.find("#width").val().replaceAll('.', ''),
            height: element_package_and_delivery.find("#height").val().replaceAll('.', ''),
            require_transport_option: element_package_and_delivery.find("#require_transport_option").val(),
            shipping_fee_payer: element_package_and_delivery.find("#payment_type_id").val() == 1 ? 'shop' : 'customer',
            note_transport: element_package_and_delivery.find("#note_transport").val(),
        }

        // đối tác vận chuyển
        if(client_request_transport.type == 1) {
            let shipping_fee = false;
            let hvc = element_package_and_delivery.find("input[type='radio'][name='hang_van_chuyen']:checked").val();
            client_request_transport.shipping_partner_id = hvc;

            if(hvc == "GHN") {
                shipping_fee = data_response_transport["GHN"].total;
            } else if(hvc === "GHTK"){
                shipping_fee = data_response_transport["GHTK"].fee.fee;
            }

            client_request_transport.shipping_fee = shipping_fee;
        } else if(client_request_transport.type == 2){
            client_request_transport.shipping_partner_id = element_package_and_delivery.find("#shipping_partner_id").val();
            client_request_transport.shipping_fee = element_package_and_delivery.find("#shipping_fee").val();
        } else if(client_request_transport.type == 3){
            client_request_transport.shipping_partner_id = null;
            client_request_transport.shipping_fee = 0
        }

        $.ajax({
        url: "/admin/order/createOrder",
        type: "POST",
        data: {
            _token: $("[name='csrf-token']").attr('content'),
            customer: customer,
            pick_address_id: pick_address_id,
            source: source,
            products: JSON.stringify(products),
            note: note,
            discount_total: discount_total,
            customer_has_paid_total: customer_has_paid_total,
            client_request_transport: JSON.stringify(client_request_transport),
            coupon: $("#coupon").val() ? $("#coupon").val() : '', 
            user_order: $("#user_order").val() ? $("#user_order").val() : '',
            user_consignee: $("#user_consignee").val() ? $("#user_consignee").val() : '',
            user_payer: $("#user_payer").val() ? $("#user_payer").val() : '',
        },
        beforeSend: function () {
            $this.find('span:first-child').hide();
            $this.find('span:last-child').show();
            $this.prop('disabled', true);
        },
        success: function (res) {
            if (res.success) {
                createToast('success', res.message);
                setTimeout(() => {
                    window.location.href = res.data.link_redirect;
                }, 2000);
           }
            
        },
        error: function (err) {
            if (err.responseJSON.errors) {
                let html_error = '<ul class="list-group list-group-flush">';
                $.each(err.responseJSON.errors, function (key, message) {
                    html_error += `<li class="list-group-item"><span class='text-danger'>${message[0]}</span></li>`
                })
                html_error += `</ul>`;

                $("body").find("#order_modal_error #html_elements").html(html_error);
                $("body").find("#order_modal_error").modal('show');
            }
            
        },
        complete: function () {
            $this.find('span:first-child').show();
            $this.find('span:last-child').hide();
            $this.prop('disabled', false);
        }
    });

    $("body").find("#order_modal_error").on('hidden.bs.modal', function(){
        $(this).find("#html_elements").empty();
    })
})
</script>
@endpush