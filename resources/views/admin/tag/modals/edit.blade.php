<div id="modal_edit" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mcsLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modal-title">Chỉnh sửa danh mục</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="modal-body">
                @include('admin.tag.modals.form', [
                    'action' => route('admin.tag.update', ['id' => ':id']),
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