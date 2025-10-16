<div class="card mt-2">
    <div class="card-body">
        <div class="row g-2 align-items-center">
    
        <!-- Thanh tìm kiếm chính -->
        <div class="col-md-5">
            <div class="input-group">
            <span class="input-group-text"><i class="mdi mdi-magnify"></i></span>
            <input type="text" id="searchInput" class="form-control" placeholder="Tìm kiếm tên khách hàng">
            </div>
        </div>

        <div class="col-md-5">
            <div class="input-group">
            <span class="input-group-text"><i class="mdi mdi-magnify"></i></span>
            <input type="text" id="phoneInput" class="form-control" placeholder="Tìm kiếm số điện thoại">
            </div>
        </div>

        <div class="col">
            <select id="provinceInput" class="form-control select2" data-toggle="select2">
                <option value="">Chọn tỉnh thành</option>
                @foreach($provinces as $province)
                <option value="{{$province->id}}">{{$province->name}}</option>
                @endforeach
            </select>
        </div>

    
        <!-- Nút lưu -->
        <div class="col-md-2">
            <button class="btn btn-outline-danger" id="btn_clear_filter">Xoá lọc</button>
            <button class="btn btn-outline-primary" id="btn_filter">Lọc</button>
        </div>
    
        </div>
    </div>
</div>