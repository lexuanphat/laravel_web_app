<div id="modal_change_status_order" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="standard-modalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="standard-modalLabel">Chuyển trạng thái đơn hàng</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="modal-body">
                <form action="" method="POST" id="form_action">
                    @csrf
                    <div class="mb-2">
                        <label for="status_code" class="required">Trạng thái <span class='text-danger'>(*)</span></label>
                        <select class="form-control select2" data-toggle="select2" name="status_code" id="status_code">
                            <option value=''>Chọn trạng thái</option>
                            @foreach($status_order as $value => $status)
                            <option value="{{$value}}">{{$status}}</option>
                            @endforeach
                        </select>
                        @include('admin.shop.modals.div-error')
                    </div>
                    <div class="mb-2">
                        <label for="note_logs"  class="required">Lý do <span class='text-danger'>(*)</span></label>
                        <textarea class="form-control" placeholder="Vui lòng nhập lý do chuyển trạng thái đơn hàng" id="note_logs" style="height: 100px;"></textarea>
                        @include('admin.shop.modals.div-error')
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary" id="btn_change_status_order">
                    <span class="add-new">Chuyển trạng thái</span>
                    @include('admin._partials.loading')
                </button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->