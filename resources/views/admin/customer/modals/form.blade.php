<form action="{{$action}}" method="POST" id="{{$id}}">
    @csrf
    <div class="form-group">
        <label for="full_name" class="required">Tên<span class="text-danger">(*)</span></label>
        <input type="text" id="full_name" name="full_name" placeholder="-- Nhập tên khách hàng --" class="form-control" required>
        @include('admin.shop.modals.div-error')
    </div>
    <div class="form-group">
        <label for="phone" class="required">Số điện thoại <span class="text-danger">(*)</span></label>
        <input type="text" oninput="this.value = this.value.replace(/[^0-9]/g, '')" id="phone" name="phone" placeholder="-- Nhập số điện thoại khách hàng --" class="form-control" required>
        @include('admin.shop.modals.div-error')
    </div>
    <div class="form-group">
        <label for="email"  class="required">Email</label>
        <input type="email" id="email" name="email" placeholder="-- Nhập địa chỉ email khách hàng --" class="form-control">
        @include('admin.shop.modals.div-error')
    </div>
    <div class="form-group">
        <label for="date_of_birth"  class="required">Ngày tháng năm sinh</label>
        <input type="date" id="date_of_birth" name="date_of_birth" class="form-control">
        @include('admin.shop.modals.div-error')
    </div>
    <div class="form-group">
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