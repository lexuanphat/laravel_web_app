<div id="modal_action" class="modal fade" data-bs-focus="false" role="dialog" aria-labelledby="mcsLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modal_title"></h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="modal-body">
                <form action="" method="POST" id="form_action">
                    @csrf
                    <div class="row g-2">
                        <div class="mb-2 col-md-6">
                            <label for="date_start_apply" class="required">Ngày bắt đầu <span class="text-danger">(*)</span></label>
                            <input type="date" id="date_start_apply" value="0" name="date_start_apply" class="form-control mask_money" required>
                            @include('admin.shop.modals.div-error')
                        </div>
                        <div class="mb-2 col-md-6">
                            <label for="date_end_apply" class="required">Ngày kết thúc <span class="text-danger">(*)</span></label>
                            <input type="date" id="date_end_apply" value="0" name="date_end_apply" class="form-control mask_money" required>
                            @include('admin.shop.modals.div-error')
                        </div>
                    </div>
                    
                    <div class="mb-2 col-md-12">
                        <label for="name" class="required">Tên coupon <span class="text-danger">(*)</span></label>
                        <input type="text" id="name" value="" name="name" placeholder="-- Nhập tên coupon --" class="form-control" required>
                        @include('admin.shop.modals.div-error')
                    </div>
                    
                    <div class="row g-2">
                        <div class="mb-2 col-md-6">
                            <label for="type" class="required">Loại giảm <span class="text-danger">(*)</span></label>
                            <select class="form-control select2 type" data-toggle="select2" id="type" name="type">
                                <option value="TIEN">Tiền</option>
                                <option value="PHAN_TRAM">Phần trăm</option>
                                
                            </select>
                            @include('admin._partials.div-error')
                        </div>
                        <div class="mb-2 col-md-6">
                            <label for="fee" class="required">Giá trị giảm <span class="text-danger">(*)</span></label>
                            <div class="input-group mb-2">
                                <div class="input-group-text" id="prefix_value">VNĐ</div>
                                <input type="text" id="fee" value="0" name="fee" placeholder="-- Nhập giá trị giảm --" class="form-control mask_money" required>
                                @include('admin.shop.modals.div-error')
                            </div>
                           
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary" id="btn_submit">
                    <span id="text_action"></span>
                    @include('admin._partials.loading')
                </button>
            </div>
        </div>
    </div>
</div>