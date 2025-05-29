<div class="col-md-4">
    <div class="card" id="card_info_addition">
        <div class="card-body p-2">
            <h5>Thông tin bổ sung</h5>
            <div class="info_addition">
                <div class="mb-2 row">
                    <label for="colFormLabelSm" class="col-sm-3 col-form-label col-form-label-sm">
                        Bán tại
                    </label>
                    <div class="col-sm-9">
                        {{-- <input type="text" class="form-control" value="{{auth()->user()->store->name}}" disabled> --}}
                        <select name="" id="" data-toggle="select2">
                            <option value="">-- Chọn cửa hàng --</option>
                        </select>
                    </div>
                </div>
                <div class="mb-2 row">
                    <label for="colFormLabelSm" class="col-sm-3 col-form-label col-form-label-sm">
                        Bán bởi
                    </label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" value="{{auth()->user()->full_name}}" disabled>
                    </div>
                </div>
                <div class="mb-2 row">
                    <label for="colFormLabelSm" class="col-sm-3 col-form-label col-form-label-sm">
                        Nguồn
                    </label>
                    <div class="col-sm-9">
                        <select name="" required id="" class="form-control select2" data-toggle="select2">
                            <option value="">Nhân viên chưa chọn...</option>
                            <option value="tiktok">Tiktok</option>
                            <option value="facebook">Facebook</option>
                            <option value="instagram">Instagram</option>
                            <option value="other">Khác</option>
                        </select>
                    </div>
                </div>
                <div class="mb-2 row">
                    <label for="colFormLabelSm" class="col-sm-3 col-form-label col-form-label-sm">
                        Hẹn giao
                    </label>
                    <div class="col-sm-9">
                        <input type="date" name="" id="" class="form-control">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>