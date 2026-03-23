<form action="{{$action}}" method="POST" id="{{$id}}">
    @csrf
     <div class="mb-2">
        <label for="code" class="required">Mã số thùng <span class="text-danger">(*)</span></label>
        <input type="text" id="code" name="code" placeholder="-- Nhập mã số thùng --" class="form-control" required>
        @include('admin._partials.div-error')
    </div>
    <div class="mb-2">
        <label for="max_capacity" class="required">Dung tích tối đa <span class="text-danger">(*)</span></label>
        <input type="text" id="max_capacity" name="max_capacity" placeholder="-- Nhập dung tích tối đa --" class="form-control number" required>
        @include('admin._partials.div-error')
    </div>
    <div class="mb-2">
        <label for="current_capacity" class="required">Dung tích hiện tại <span class="text-danger">(*)</span></label>
        <input type="text" id="current_capacity" name="current_capacity" placeholder="-- Nhập dung tích hiện tại --" class="form-control number" required>
        @include('admin._partials.div-error')
    </div>
   
    <div class="mb-2">
        <label for="fish_status" class="required">Trạng thái cá tốt <span class="text-danger">(*)</span></label>
        <select class="form-control" id="fish_status" name="fish_status">
            <option value="ca_dep">Cá đẹp</option>
            <option value="ca_binh_thuong">Cá bình thường</option>
            <option value="ca_xau">Cá xấu</option>
            <option value="ca_cuc_xau">Cá cực xấu</option>
        </select>
        @include('admin._partials.div-error')
    </div>
     <div class="mb-2">
        <label for="status_vat" class="required">Tình trạng thùng <span class="text-danger">(*)</span></label>
        <select class="form-control" id="status_vat" name="status_vat">
            <option value="nguyen">Thùng nguyên</option>
            <option value="long_tron">Thùng đang long trộn</option>
            <option value="keo_rut">Thùng đang kéo rút</option>
            <option value="keo_ra_bon_chua">Thùng đang kéo ra bồn chứa (bán nước mắm thô)</option>
            <option value="keo_ra_bon_chai">Thùng đang kéo ra bồn ra chai (đóng chai thành phẩm)</option>
            <option value="ban_xac">Thùng chuẩn bị bán xác mắm</option>
            <option value="danh_nuoc_muoi">Thùng đang đánh nước muối</option>
        </select>
        @include('admin._partials.div-error')
    </div>
</form>