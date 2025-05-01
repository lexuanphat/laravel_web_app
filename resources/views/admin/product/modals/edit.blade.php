<div id="modal_edit" class="modal fade" data-bs-focus="false" role="dialog" aria-labelledby="mcsLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modal-title">Chỉnh sửa sản phẩm</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="modal-body">
                @include('admin.product.modals.form', [
                    'action' => route('admin.product.update', ['id' => ':id']),
                    'id' => 'form_edit',
                ])
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary" id="btn_update">
                    @include('admin._partials.update')
                    @include('admin._partials.loading')
                </button>
            </div>
        </div>
    </div>
</div>