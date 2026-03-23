<div id="modal_rut" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="standard-modalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title d-flex flex-wrap align-items-center gap-1" id="modal_label"></div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Số lượng rút</label>
                    <input type="text" class="form-control number" id="qty" name="qty">
                    @include('admin._partials.div-error')
                </div>
                <div class="mb-3">
                    <label class="form-label">Chọn loại</label>
                    <select class="form-control form-select" class="target_type" id="target_type">
                        <option value="">-- Chọn loại --</option>
                        <option value="BON">Bồn</option>
                        <option value="THUNG">Thùng</option>
                    </select>
                    @include('admin._partials.div-error')
                </div>
                <div class="mb-3">
                    <label class="form-label">Chọn đối tượng</label>
                    <select class="form-control form-select" class="target_id" id="target_id" disabled>
                       <option value="">-- Vui lòng chọn loại trước --</option>
                    </select>
                    @include('admin._partials.div-error')
                </div>
            </div>
            <div class="modal-footer">
                <input type="hidden" id="code_target_type" value="">
                <input type="hidden" id="code_target_id" value="">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary" id="btnHandleRut">Tiến hành rút</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->