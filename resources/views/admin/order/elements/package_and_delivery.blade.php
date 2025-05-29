<form action="POST" id="form_transport">
    <div class="card-body">
        <h5>Đóng gói và giao hàng</h5>
        <div class="options d-flex flex-wrap gap-2">
            <div class="option">
                <input type="radio" class="btn-check" value="1" name="options" id="option1" autocomplete="off" checked>
                <label class="btn btn-outline-primary" for="option1"><i class="ri-truck-line"></i> Đẩy qua hãng vận chuyển</label>
            </div>
    
            <div class="option">
                <input type="radio" class="btn-check" value="2" name="options" id="option2" autocomplete="off">
                <label class="btn btn-outline-primary" for="option2"><i class="ri-takeaway-line"></i> Đẩy vận chuyển ngoài</label>
            </div>
    
            <div class="option">
                <input type="radio" class="btn-check" value="3" name="options" id="option3" autocomplete="off">
                <label class="btn btn-outline-primary" for="option3"><i class="ri-home-8-line"></i> Nhận tại cửa hàng</label>
            </div>
    
            <div class="option">
                <input type="radio" class="btn-check" value="4" name="options" id="option4" autocomplete="off">
                <label class="btn btn-outline-primary" for="option4"> <i class="uil-dropbox"></i>Giao hàng sau</label>
            </div>
        </div>
    </div>
    <hr class="my-0">
    <div class="card-body">
        <div class="row">
            <div class="left col-md-4" id="left">
                <div class="border border-primary rounded-3 p-2 bg-primary-subtle">
                    <h4>Địa chỉ giao hàng</h4>
                    <ul id="list_info_customer">
                        <li>
                            <a href="tel:0905631254">0905631254</a>
                        </li>
                        <li>
                            <span>Số 15, Phường Nhơn Phú, Thành phố Quy Nhơn, Tỉnh Bình Định, Việt Nam</span>
                        </li>
                    </ul>
                    <h4>Thông tin giao hàng</h4>
                    <div class="mb-3">
                        <label class="form-label">Thu tiền hộ (COD)</label>
                        <input type="text" id="cod" name="cod" class="form-control text-end" value="0" placeholder="Nhập tiền thu hộ">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Khối lượng (g)</label>
                        <input type="text" id="gam" name="gam" class="form-control text-end" value="0" placeholder="Nhập khối lượng">
                    </div>
                    <div class="mb-3">
                        <div class="row g-3">
                            <div class="col-4">
                                <label class="form-label">Dài (cm)</label>
                                <input type="text" id="length" name="length" class="form-control text-end" value="0" placeholder="Nhập chiều dài">
                            </div>
                            <div class="col-4">
                                <label class="form-label">Cao (cm)</label>
                                <input type="text" id="height" name="height" class="form-control text-end" value="0" placeholder="Nhập chiều cao">
                            </div>
                            <div class="col-4">
                                <label class="form-label">Rộng (cm)</label>
                                <input type="text" id="width" name="width" class="form-control text-end" value="0" placeholder="Nhập chiều rộng">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Yêu cầu giao hàng</label>
                        <select class="form-control select2" id="require_transport_option" name="require_transport_option" data-toggle="select2">
                            <option>-- Chọn --</option>
                            <option value="AK">Cho xem hàng, không cho thử</option>
                            <option value="AK1">Cho xem hàng, cho thử</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="example-textarea" class="form-label">Ghi chú</label>
                        <textarea class="form-control" name="note" id="note_transport" rows="5"></textarea>
                    </div>
                </div>
            </div>
            <div class="right col-md-8" id="right">
                <div class="tab-content">
                    <div class="tab-pane active" id="tab-option1">
                        @include('admin.order.modal-transport.hang-don-vi-van-chuyen')
                    </div>
                    <div class="tab-pane" id="tab-option2">
                        @include('admin.order.modal-transport.van-chuyen-ngoai')
                    </div>
                    <div class="tab-pane" id="tab-option3">Nội dung nhận tại cửa hàng</div>
                    <div class="tab-pane" id="tab-option4">
                        <div class="option-transport">
                            <label for="" class="form-label">Bạn có thể chọn trước đối tác giao hàng dự kiến để thuận tiện hơn cho việc xử lý đơn</label>
                            <select class="form-control select2" data-toggle="select2">
                                <option>Chọn đối tác</option>
                                
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@push('js_ready')
@php echo file_get_contents(asset('assets/js/order/package_and_delivery.js')) @endphp
@endpush