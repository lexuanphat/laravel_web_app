<div id="modal_form" class="modal fade" data-bs-focus="false" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modal_title">Thêm mới khách hàng</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="modal-body">
                <form action="" method="POST" id="form_action">
                    @csrf
                    <div class="mb-2">
                        <label for="full_name" class="required">Tên<span class="text-danger">(*)</span></label>
                        <input type="text" id="full_name" name="full_name" placeholder="-- Nhập tên khách hàng --" class="form-control" required>
                        @include('admin.shop.modals.div-error')
                    </div>
                    <div class="mb-2">
                        <label for="phone" class="required">Số điện thoại <span class="text-danger">(*)</span></label>
                        <input type="text" oninput="this.value = this.value.replace(/[^0-9]/g, '')" id="phone" name="phone" placeholder="-- Nhập số điện thoại khách hàng --" class="form-control" required>
                        @include('admin.shop.modals.div-error')
                    </div>
                    <div class="mb-2">
                        <label for="email"  class="required">Email</label>
                        <input type="email" id="email" name="email" placeholder="-- Nhập địa chỉ email khách hàng --" class="form-control">
                        @include('admin.shop.modals.div-error')
                    </div>
                    <div class="mb-2">
                        <label for="date_of_birth"  class="required">Ngày tháng năm sinh</label>
                        <input type="date" id="date_of_birth" name="date_of_birth" class="form-control">
                        @include('admin.shop.modals.div-error')
                    </div>
                    <div class="mb-2">
                        <label for=""  class="required">Địa chỉ nhận hàng <span class="text-danger">(*)</span></label>
                        <div class="row g-2">
                            <div class="col-6">
                                <select class="form-control select2" data-toggle="select2" name="province_code" id="province_code">
                                    <option value=''>Chọn tỉnh thành</option>
                                </select>
                                @include('admin.shop.modals.div-error')
                            </div>
                            <div class="col-6">
                                <select class="form-control select2" data-toggle="select2" name="ward_code" id="ward_code">
                                    <option value=''>Chọn phường xã</option>
                                </select>
                                @include('admin.shop.modals.div-error')
                            </div>
                            <div class="col-12">
                                <input type="text" id="address" name="address" class="form-control" placeholder="-- Nhập địa chỉ --">
                                @include('admin.shop.modals.div-error')
                            </div>
                        </div>
                    </div>
                    <div class="mb-2">
                        <div>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="male" value="0" class="custom-control-input" name="gender" checked>
                                <label class="custom-control-label" for="male">Nam</label>
                            </div>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="female" value="1" class="custom-control-input" name="gender">
                                <label class="custom-control-label" for="female">Nữ</label>
                            </div>
                        </div>
                        @include('admin.shop.modals.div-error')
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary btn_action" id="">
                    @include('admin._partials.add-new')
                    @include('admin._partials.loading')
                </button>
            </div>
        </div>
    </div>
</div>