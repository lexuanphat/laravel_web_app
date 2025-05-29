<div id="modal_create" class="modal fade" data-bs-focus="false" role="dialog" aria-labelledby="mcsLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modal_title"></h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="modal-body">
                @include('admin.product_stock.modals.form', [
                    'action' => route('admin.product_stock.store'),
                    'id' => 'form_create',
                ])
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