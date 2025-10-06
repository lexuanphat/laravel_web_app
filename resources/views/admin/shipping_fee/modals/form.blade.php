<div id="modal_form" class="modal fade" data-bs-focus="false" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modal_title">Thêm mới phí giao hàng</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="modal-body">
                <form action="" method="POST" id="form_action">
                    @csrf
                    <div class="mb-2">
                        <label for="province_id" class="required">Tỉnh thành<span class="text-danger">(*)</span></label>
                        <select class="form-control select2" data-toggle="select2" name="province_id" id="province_id">
                        </select>
                        @include('admin.shop.modals.div-error')
                    </div>
                    <div class="mb-2">
                        <label for="phone" class="required">Phí giao hàng <span class="text-danger">(*)</span></label>
                        <input type="text" oninput="this.value = Number(this.value.replace(/[^0-9]/g, '')).toLocaleString('vi')" id="fee" name="fee" placeholder="-- Nhập phí giao hàng --" class="form-control" required>
                        @include('admin.shop.modals.div-error')
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary btn_action" id="">
                    @include('admin._partials.add-new')
                    @include('admin._partials.loading')
                </button>
            </div>
        </div>
    </div>
</div>