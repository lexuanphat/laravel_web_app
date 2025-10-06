<div id="modal_create" class="modal fade" data-bs-focus="false" role="dialog" aria-labelledby="mcsLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modal_title"></h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="modal-body">
                <form action="{{$action}}" method="POST" id="{{$id}}">
                    @csrf
                    <div class="mb-2">
                        <label for="product_id" class="required">Sản phẩm <span class="text-danger">(*)</span></label>
                        <select class="form-control select2 product_id" data-toggle="select2" id="product_id" name="product_id">
                            <option value="">Chọn sản phẩm</option>
                            @foreach($products as $item)
                            <option value="{{$item->id}}">{{$item->text}}</option>
                            @endforeach
                        </select>
                        @include('admin._partials.div-error')
                    </div>
                    <div class="mb-2">
                        <label for="province_id" class="required">Khu vực <span class="text-danger">(*)</span></label>
                        <select class="form-control select2 province_id" data-toggle="select2" id="province_id" name="province_id">
                            <option value="">Chọn khu vực</option>
                            @foreach($provinces as $item)
                            <option value="{{$item->id}}">{{$item->text}}</option>
                            @endforeach
                        </select>
                        @include('admin._partials.div-error')
                    </div>
                    <div class="mb-2">
                        <div class="mb-2 col-md-4">
                            <label for="fee" class="required">Giá khu vực <span class="text-danger">(*)</span></label>
                            <input type="text" oninput="this.value = this.value.replace(/[^0-9]/g, '')" id="fee" value="0" name="fee" placeholder="-- Nhập giá khu vực --" class="form-control mask_money" required>
                            @include('admin.shop.modals.div-error')
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