<form action="{{$action}}" method="POST" id="{{$id}}">
    @csrf
    <div class="mb-2">
        <label for="full_name" class="required">Tên vận chuyển <span class="text-danger">(*)</span></label>
        <input type="text" id="full_name" name="full_name" placeholder="-- Nhập tên vận chuyển --" class="form-control" required>
        @include('admin._partials.div-error')
    </div>
    <div class="mb-2">
        <label for="phone" class="required">Số điện thoại <span class="text-danger">(*)</span></label>
        <input type="text" id="phone" name="phone" placeholder="-- Nhập số điện thoại --" class="form-control" required>
        @include('admin._partials.div-error')
    </div>
    <div class="mb-2">
        <div class="form-check form-check-inline">
            <input type="radio" id="{{$id}}_chanh_xe" checked name="role" value="CHANH_XE" class="form-check-input">
            <label class="form-check-label" for="{{$id}}_chanh_xe">Chành xe</label>
        </div>
        <div class="form-check form-check-inline">
            <input type="radio" id="{{$id}}_shipper" name="role" value="SHIPPER" class="form-check-input">
            <label class="form-check-label" for="{{$id}}_shipper">Shipper</label>
        </div>
    </div>
</form>