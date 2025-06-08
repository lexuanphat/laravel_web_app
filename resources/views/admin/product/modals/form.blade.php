<form action="{{$action}}" method="POST" id="form" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="id" value="0" id="id">
    <div class="row g-2">
        <div class="col-md-9">
            <div class="mb-2">
                <label for="name" class="required">Tên sản phẩm <span class="text-danger">(*)</span></label>
                <input type="text" id="name" name="name" placeholder="-- Nhập tên sản phẩm --" class="form-control name" required>
                @include('admin._partials.div-error')
            </div>
            <div class="mb-2">
                <label for="sku" class="required">Mã SKU</label>
                <input type="text" id="sku" name="sku" placeholder="-- Nhập mã SKU --" class="form-control sku">
                @include('admin._partials.div-error')
            </div>
            <div class="mb-2">
                <label for="category_id" class="required">Danh mục <span class="text-danger">(*)</span></label>
                <select class="form-control select2 category_id" data-toggle="select2" id="category_id" name="category_id">
                    <option value="">Chọn danh mục</option>
                </select>
                @include('admin._partials.div-error')
            </div>
            <div class="mb-2">
                <label for="price" class="required">Giá sản phẩm <span class="text-danger">(*)</span></label>
                <input type="text" id="price" name="price" placeholder="-- Nhập giá sản phẩm --" value="0" class="form-control mask_money price" required>
                @include('admin._partials.div-error')
            </div>
        </div>
        <div class="col-md-3">
            <div class="mb-2">
                <div class="col-md-12">
                    <img src="{{asset('assets/images/no-image.jpg')}}" alt="image" id="img_preview" class="img-fluid img_preview" height="50px"/>
                </div>
                <label for="image_url" id="label_upload" class="btn btn-primary col-12 label_image"><i class="ri-upload-2-line"></i> Tải hình ảnh</label>
                <div class="mt-1 col-12">
                    <button type="button" class="btn btn-danger col-12" id="remove_uploaded"><i class="ri-delete-bin-line"></i> Xoá hình đã tải lên</button>
                    <input type="hidden" name="current_image" id="current_image" value="0">
                </div>
                <input type="file" class="d-none image_url" accept="image/*" name="image_url" id="image_url" onchange="loadFile(event)">
                @include('admin._partials.div-error')
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3 mb-2">
            <label for="desc" class="required">Chiều dài (cm) <span class="text-danger">(*)</span></label>
            <input type="text" class="form-control input_money" id="length" name="length" value="0">
            @include('admin._partials.div-error')
        </div>
        <div class="col-md-3 mb-2">
            <label for="desc" class="required">Chiều rộng (cm) <span class="text-danger">(*)</span></label>
            <input type="text" class="form-control input_money" id="width" name="width" value="0">
            @include('admin._partials.div-error')
        </div>
        <div class="col-md-3 mb-2">
            <label for="desc" class="required">Chiều cao (cm) <span class="text-danger">(*)</span></label>
            <input type="text" class="form-control input_money" id="height" name="height" value="0">
            @include('admin._partials.div-error')
        </div>
        <div class="col-md-3 mb-2">
            <label for="desc" class="required">KL (gram) <span class="text-danger">(*)</span></label>
            <input type="text" class="form-control input_money" id="weight" name="weight" value="0">
            @include('admin._partials.div-error')
        </div>
    </div>
    <div class="mb-2">
        <label for="desc" class="required">Mô tả sản phẩm</label>
        <div class="snow_editor" id="snow_editor" style="height: 300px;">
        </div>
        <textarea name="desc" id="desc" class="d-none desc"></textarea>
    </div>
</form>