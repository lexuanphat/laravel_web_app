<form action="{{$action}}" method="POST" id="{{$id}}">
    @csrf
    <div class="mb-2">
        <label for="product_id" class="required">Sản phẩm <span class="text-danger">(*)</span></label>
        <select class="form-control select2 product_id" data-toggle="select2" id="product_id" name="product_id">
            <option value="">Chọn sản phẩm</option>
        </select>
        @include('admin._partials.div-error')
    </div>
    <div class="mb-2">
        <label for="store_id" class="required">Cửa hàng <span class="text-danger">(*)</span></label>
        <select class="form-control select2 store_id" data-toggle="select2" id="store_id" name="store_id">
            <option value="">Chọn cửa hàng</option>
            @foreach($store as $item) 
            <option value="{{$item->id}}">{{$item->name}}</option>
            @endforeach
        </select>
        @include('admin._partials.div-error')
    </div>
    <div class="row g-3">
        <div class="mb-2 col-md-4">
            <label for="stock_quantity" class="required">Số lượng tồn <span class="text-danger">(*)</span></label>
            <input type="text" oninput="this.value = this.value.replace(/[^0-9]/g, '')" id="stock_quantity" value="1" name="stock_quantity" placeholder="-- Nhập số lượng tồn --" class="form-control mask_money" required>
            @include('admin.shop.modals.div-error')
        </div>
        <div class="mb-2 col-md-4">
            <label for="available_quantity" class="required">Số lượng bán <span class="text-danger">(*)</span></label>
            <input type="text" oninput="this.value = this.value.replace(/[^0-9]/g, '')" id="available_quantity" value="1" name="available_quantity" placeholder="-- Nhập số lượng bán --" class="form-control mask_money" required>
            @include('admin.shop.modals.div-error')
        </div>
        <div class="mb-2 col-md-4">
            <label for="stock_price" class="required">Giá bán <span class="text-danger">(*)</span></label>
            <input type="text" oninput="this.value = this.value.replace(/[^0-9]/g, '')" id="stock_price" value="0" name="stock_price" placeholder="-- Nhập giá bán --" class="form-control mask_money" required>
            @include('admin.shop.modals.div-error')
        </div>
    </div>
</form>