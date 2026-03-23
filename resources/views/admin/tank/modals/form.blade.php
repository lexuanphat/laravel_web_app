<form action="{{$action}}" method="POST" id="{{$id}}">
    @csrf
     <div class="mb-2">
        <label for="type" class="required">Loại bồn <span class="text-danger">(*)</span></label>
        <select class="form-control select2" data-toggle="select2" id="type" name="type">
            <option value="thanh_pham">Bồn chứa thành phẩm: Dùng để chứa nước mắm thô</option>
            <option value="ra_chai">Bồn chứa ra chai: Dùng để chứa nước mắm trước khi đóng chai</option>
            <option value="nhua">Bồn nhựa: Tạm chứa nước mắm từ thùng này kéo ra để bơm lên thùng khác hoặc trộn nước muối</option>
        </select>
        @include('admin._partials.div-error')
    </div>
     <div class="mb-2">
        <label for="code" class="required">Mã số bồn <span class="text-danger">(*)</span></label>
        <input type="text" id="code" name="code" placeholder="-- Nhập mã số bồn --" class="form-control" required>
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
</form>