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