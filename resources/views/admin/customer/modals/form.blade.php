<form action="{{$action}}" method="POST" id="{{$id}}">
    @csrf
    <div class="mb-2">
        <label for="full_name" class="required">Tên<span class="text-danger">(*)</span></label>
        <input type="text" id="full_name" name="full_name" placeholder="-- Nhập tên khách hàng --" class="form-control" required>
        @include('admin.shop.modals.div-error')
    </div>
    <div class="mb-2">
        <label for="phone" class="required">Số điện thoại <span class="text-danger">(*)</span></label>
        <input type="text" oninput="this.value = this.value.replace(/[^0-9]/g, '')" id="phone" name="phone" placeholder="-- Nhập số điện thoại khách hàng --" class="form-control" required>
        @include('admin.shop.modals.div-error')
    </div>
    <div class="mb-2">
        <label for="email"  class="required">Email</label>
        <input type="email" id="email" name="email" placeholder="-- Nhập địa chỉ email khách hàng --" class="form-control">
        @include('admin.shop.modals.div-error')
    </div>
    <div class="mb-2">
        <label for="date_of_birth"  class="required">Ngày tháng năm sinh</label>
        <input type="date" id="date_of_birth" name="date_of_birth" class="form-control">
        @include('admin.shop.modals.div-error')
    </div>
    <div class="mb-2">
        <label for=""  class="required">Địa chỉ nhận hàng <span class="text-danger">(*)</span></label>
        <div class="row g-2">
            <div class="col-6">
                <input type="text" id="address" name="address" class="form-control" placeholder="-- Nhập địa chỉ --">
                @include('admin.shop.modals.div-error')
            </div>
            <div class="col-6">
                <input type="text" id="ward_text" name="ward_text" class="form-control" placeholder="-- Nhập phường/xã --">
                @include('admin.shop.modals.div-error')
            </div>
            <div class="col-6">
                <input type="text" id="district_text" name="district_text" class="form-control" placeholder="-- Nhập quận/huyện --">
                @include('admin.shop.modals.div-error')
            </div>
            <div class="col-6">
                <input type="text" id="province_text" name="province_text" class="form-control" placeholder="-- Nhập tỉnh/thành phố --">
                @include('admin.shop.modals.div-error')
            </div>
        </div>
    </div>
    <div class="mb-2">
        <div>
            <div class="custom-control custom-radio custom-control-inline">
                <input type="radio" id="male_{{$action_radio}}" value="0" class="custom-control-input" name="gender" checked>
                <label class="custom-control-label" for="male_{{$action_radio}}">Nam</label>
            </div>
            <div class="custom-control custom-radio custom-control-inline">
                <input type="radio" id="female_{{$action_radio}}" value="1" class="custom-control-input" name="gender">
                <label class="custom-control-label" for="female_{{$action_radio}}">Nữ</label>
            </div>
        </div>
        @include('admin.shop.modals.div-error')
    </div>
</form>