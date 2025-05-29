<form action="{{$action}}" method="POST" id="{{$id}}">
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <strong>Địa chỉ</strong> yêu cầu nhập đúng định dạng để có thể đồng bộ với Đơn vị vận chuyển<br>
        Cú pháp: địa chỉ chi tiết, Phường - xã, Quận - huyện, Tỉnh - Thành phố <br>
        Ví dụ: Số 9, Phường Tây Sơn, Thành phố Pleiku, Tỉnh Gia Lai
    </div>
    @csrf
    <div class="mb-2">
        <label for="name" class="required">Tên <span class="text-danger">(*)</span></label>
        <input type="text" id="name" name="name" placeholder="-- Nhập tên cửa hàng --" class="form-control" required>
        @include('admin.shop.modals.div-error')
    </div>
    <div class="mb-2">
        <label for="location" class="required">Số điện thoại <span class="text-danger">(*)</span></label>
        <input type="text" oninput="this.value = this.value.replace(/[^0-9]/g, '')" id="contact_phone" name="contact_phone" placeholder="-- Nhập số điện thoại cửa hàng --" class="form-control" required>
        @include('admin.shop.modals.div-error')
    </div>
    <div class="mb-2">
        <label for="location"  class="required">Địa chỉ <span class="text-danger">(*)</span></label>
        <input type="text" id="address" name="address" placeholder="-- Nhập địa chỉ cửa hàng --" class="form-control" required>
        @include('admin.shop.modals.div-error')
    </div>
</form>