<form action="{{$action}}" method="POST" id="{{$id}}">
    @csrf
     <div class="mb-2">
        <label for="name" class="required">Tên chỉ tiêu <span class="text-danger">(*)</span></label>
        <input type="text" id="name" name="name" placeholder="-- Nhập tên chỉ tiêu --" class="form-control" required>
        @include('admin._partials.div-error')
    </div>

    <div class="mb-2">
        <label for="type_report" class="required">Loại chỉ tiêu đánh giá <span class="text-danger">(*)</span></label>
        <select class="form-control" id="type_report" name="type_report">
            <option value="protein_level">Độ đạm</option>
            <option value="salt_level">Nồng độ muối</option>
            <option value="histamine_level">Histamin</option>
            <option value="acid_level">Admin</option>
            <option value="amon_level">Amon</option>
            <option value="color">Màu sắc</option>
        </select>
        @include('admin._partials.div-error')
    </div>
</form>