<form action="{{$action}}" method="POST" id="{{$id}}">
    @csrf
     <div class="mb-2">
        <label for="vat_id" class="required">Mã thùng <span class="text-danger">(*)</span></label>
        <select class="form-control select2" data-toggle="select2" id="vat_id" name="vat_id">
          @foreach($data_vats as $item)
            <option value="{{$item->id}}">{{$item->code}}</option>
          @endforeach
        </select>
        @include('admin._partials.div-error')
    </div>
    <div class="mb-2">
        <label for="protein_level" class="required">Độ đạm <span class="text-danger">(*)</span></label>
        <select class="form-control select2" data-toggle="select2" id="protein_level" name="protein_level">
            <option value="1">Ví dụ 1</option>
            <option value="2">Ví dụ 2</option>
            <option value="3">Ví dụ 3</option>
        </select>
        @include('admin._partials.div-error')
    </div>
    <div class="mb-2">
        <label for="salt_level" class="required">Nồng độ muối <span class="text-danger">(*)</span></label>
        <select class="form-control select2" data-toggle="select2" id="salt_level" name="salt_level">
            <option value="1">Ví dụ 1</option>
            <option value="2">Ví dụ 2</option>
            <option value="3">Ví dụ 3</option>
        </select>
        @include('admin._partials.div-error')
    </div>
    <div class="mb-2">
        <label for="histamine_level" class="required">Histamin <span class="text-danger">(*)</span></label>
        <select class="form-control select2" data-toggle="select2" id="histamine_level" name="histamine_level">
            <option value="1">Ví dụ 1</option>
            <option value="2">Ví dụ 2</option>
            <option value="3">Ví dụ 3</option>
        </select>
        @include('admin._partials.div-error')
    </div>
    <div class="mb-2">
        <label for="acid_level" class="required">Admin <span class="text-danger">(*)</span></label>
        <select class="form-control select2" data-toggle="select2" id="acid_level" name="acid_level">
            <option value="1">Ví dụ 1</option>
            <option value="2">Ví dụ 2</option>
            <option value="3">Ví dụ 3</option>
        </select>
        @include('admin._partials.div-error')
    </div>
    <div class="mb-2">
        <label for="amon_level" class="required">Amon <span class="text-danger">(*)</span></label>
        <select class="form-control select2" data-toggle="select2" id="amon_level" name="amon_level">
            <option value="1">Ví dụ 1</option>
            <option value="2">Ví dụ 2</option>
            <option value="3">Ví dụ 3</option>
        </select>
        @include('admin._partials.div-error')
    </div>
    <div class="mb-2">
        <label for="color" class="required">Màu sắc <span class="text-danger">(*)</span></label>
        <select class="form-control select2" data-toggle="select2" id="color" name="color">
            <option value="1">Ví dụ 1</option>
            <option value="2">Ví dụ 2</option>
            <option value="3">Ví dụ 3</option>
        </select>
        @include('admin._partials.div-error')
    </div>
</form>