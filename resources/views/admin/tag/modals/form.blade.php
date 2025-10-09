<form action="{{$action}}" method="POST" id="{{$id}}">
    @csrf
    <div class="mb-2">
        <label for="tag_name" class="required">Tên <span class="text-danger">(*)</span></label>
        <input type="text" id="tag_name" name="tag_name" placeholder="-- Nhập tên tag --" class="form-control" required>
        @include('admin._partials.div-error')
    </div>
</form>