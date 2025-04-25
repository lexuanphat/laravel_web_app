<div id="modal_create" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal_createLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modal-title">Thêm mới cửa hàng</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="modal-body">
                @include('admin.shop.modals.form', [
                    'action' => route('admin.shop.store'),
                    'id' => 'form_create',
                ])
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary" id="btn_create">
                    @include('admin._partials.add-new')
                    @include('admin._partials.loading')
                </button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->