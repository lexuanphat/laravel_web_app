<div id="{{$model_id}}" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="{{$model_id}}Label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modal-title">Chỉnh sửa khách hàng</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="modal-body">
                @include('admin.customer.modals.form', [
                    'action' => route('admin.customer.update', ['id' => ':id']),
                    'id' => $form_id,
                ])
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary" id="{{$btn_submit_id}}">
                    @include('admin._partials.update')
                    @include('admin._partials.loading')
                </button>
            </div>
        </div>
    </div>
</div>