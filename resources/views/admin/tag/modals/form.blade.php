<form action="{{$action}}" method="POST" id="{{$id}}">
    @csrf
    <div class="mb-2">
        <label for="tag_name" class="required">Tên <span class="text-danger">(*)</span></label>
        <input type="text" id="tag_name" name="tag_name" placeholder="-- Nhập tên tag --" class="form-control" required>
        @include('admin._partials.div-error')
    </div>

    <div class="mb-2">
        <label for="type" class="required">Phân loại <span class="text-danger">(*)</span></label>
        <select class="form-control select2 category_id" data-toggle="select2" id="type" name="type">
            <option value="1">Sản phẩm</option>
            <option value="2">Khách hàng</option>
        </select>
        @include('admin._partials.div-error')
    </div>
    
</form>