<form action="{{$action}}" method="POST" id="{{$id}}">
    @csrf
    <div class="form-group">
        <label for="name" class="required">Tên <span class="text-danger">(*)</span></label>
        <input type="text" id="name" name="name" placeholder="-- Nhập tên danh mục --" class="form-control" required>
        @include('admin._partials.div-error')
    </div>
</form>