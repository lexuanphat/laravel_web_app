<div>
    <h4>Đối tác giao hàng</h4>
    <hr>
    <div class="mb-3">
        <h5>Loại giao hàng</h5>
        <div class="row align-items-center g-2">
            <div class="col-md-3">
                <div class="form-check">
                    <input type="radio" id="shiper" value="SHIPPER" name="loai_giao_hang" class="form-check-input">
                    <label class="form-check-label" for="shiper">Shipper tự liên hệ</label>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-check">
                    <input type="radio" id="chanh_xe" value="CHANH_XE" name="loai_giao_hang" class="form-check-input">
                    <label class="form-check-label" for="chanh_xe">Hãng vận chuyển ngoài</label>
                </div>
            </div>
            <div class="col-md-6">
                <label for="">Chọn đối tác giao hàng</label>
                <select name="partner_transport" id="partner_transport" data-toggle="select2">
                    <option value="">-- Chọn đối tác giao hàng --</option>
                </select>
            </div>
        </div>
        <div class="row align-items-center g-2">
            <div class="col-md-6 mb-3">
                <label for="">Phí trả đối tác vận chuyển</label>
                <input type="text" name="delivery_method_fee"  id="delivery_method_fee" class="form-control text-end input_money" value="0">
            </div>
            <div class="col-md-3 mb-3">
                <div class="form-check">
                    <input type="radio" id="shop_fee" value="1" checked name="fee_transport" class="form-check-input">
                    <label class="form-check-label" for="shop_fee">Shop trả phí</label>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="form-check">
                    <input type="radio" id="receiver_fee" value="2" name="fee_transport" class="form-check-input">
                    <label class="form-check-label" for="receiver_fee">Người nhận trả phí</label>
                </div>
            </div>
        </div>
    </div>
</div>
@push('js')
<script>
    const get_transport = @json($get_transport);
</script>
@endpush