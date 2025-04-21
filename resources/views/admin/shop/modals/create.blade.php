<div id="modal-create-store" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mcsLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modal-title">Thêm mới cửa hàng</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                @include('admin.shop.modals.form', [
                    'action' => route('admin.shop.store'),
                    'id' => 'form_create_store',
                ])
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary" id="btn_create_store">
                    @include('admin._partials.add-new')
                    @include('admin._partials.loading')
                </button>
            </div>
        </div>
    </div>
</div>