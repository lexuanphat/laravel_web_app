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
        <input type="text" class="form-control" name="protein_level" id="protein_level" value="">
        @include('admin._partials.div-error')
    </div>
    <div class="mb-2">
        <label for="salt_level" class="required">Nồng độ muối <span class="text-danger">(*)</span></label>
        <input type="text" class="form-control" name="salt_level" id="salt_level" value="">
        @include('admin._partials.div-error')
    </div>
    <div class="mb-2">
        <label for="histamine_level" class="required">Histamin <span class="text-danger">(*)</span></label>
        <input type="text" class="form-control" name="histamine_level" id="histamine_level" value="">
        @include('admin._partials.div-error')
    </div>
    <div class="mb-2">
        <label for="acid_level" class="required">Admin <span class="text-danger">(*)</span></label>
        <input type="text" class="form-control" name="acid_level" id="acid_level" value="">
        @include('admin._partials.div-error')
    </div>
    <div class="mb-2">
        <label for="amon_level" class="required">Amon <span class="text-danger">(*)</span></label>
        <input type="text" class="form-control" name="amon_level" id="amon_level" value="">
        @include('admin._partials.div-error')
    </div>
    <div class="mb-2">
        <label for="color" class="required">Màu sắc <span class="text-danger">(*)</span></label>
        <input type="text" class="form-control" name="color" id="color" value="">
        @include('admin._partials.div-error')
    </div>
</form>