@extends('_blank')
@section('content')
@include('admin.customer.filter')
<div class="card">
    <div class="card-body">
        <div class="button-actions">
            <button type="button" id="btn_show_modal_add" class="btn btn-primary mb-2"><i class="mdi mdi-plus-circle"></i> Thêm mới khách hàng</button>
        </div>
        <table id="table_manage" data-action="{{route('admin.customer.get_data')}}" class="table dt-responsive w-100">
            <thead>
                <tr>
                    <th>
                        <div class="text-uppercase align-middle">STT</div>
                    </th>
                    <th>
                        <div class="text-uppercase align-middle">Mã khách hàng</div>
                    </th>
                    <th>
                        <div class="text-uppercase align-middle">Tên</div>
                    </th>
                    <th>
                        <div class="text-uppercase align-middle">SL đơn hàng</div>
                    </th>
                    <th>
                        <div class="text-uppercase align-middle">Nợ phải thu</div>
                    </th>
                    <th>
                        <div class="text-uppercase align-middle">Tổng chi tiêu</div>
                    </th>
                    <th>
                        <div class="text-uppercase align-middle">Số điện thoại</div>
                    </th>
                    <!-- <th>
                        <div class="text-uppercase align-middle">Ngày thao tác</div>
                    </th>
                    <th>
                        <div class="text-uppercase align-middle">Người thao tác</div>
                    </th> -->
                    <th>
                        <div class="text-uppercase align-middle">Chức năng</div>
                    </th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
@include('admin.customer.modals.form')
@endsection
@push('js')
<script>
    const ELEMENTS = {
        btn_show_modal_add: $("#btn_show_modal_add"),
        modal_form: $("#modal_form"),
        table_manage: $("#table_manage"),
        btn_filter: $("#btn_filter"),
        btn_clear_filter: $("#btn_clear_filter"),
        btn_add: $("#btn_add"),
        route_add: @json(route('admin.customer.store')),
        route_update: @json(route('admin.customer.update', ['id' => ':id'])),
        route_delete: @json(route("admin.customer.delete", ['id' => ':id'])),
        route_province: @json(route('admin.province.get_province')),

        select_province: $("#province_code"),
        select_district: $("#district_code"),
        select_ward: $("#ward_code"),
    };

    function renderTable(search){
        ELEMENTS.table_manage.DataTable({
            language: {
                    url: @json(asset('/assets/js/vi.json')),
                },
            ajax: {
                url: ELEMENTS.table_manage.data('action'),
                type: "GET",
                data: {
                    search: search
                },
            },
            searching: false,
            stateSave: true,
            processing: true,
            serverSide: true,
            ordering: false,
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', class: 'align-middle'},
                { data: 'code', class: 'align-middle'},
                { data: 'full_name', class: 'align-middle'},
                { data: 'total_order', class: 'align-middle'},
                { data: 'no_phai_thu', class: 'align-middle'},
                { data: 'tong_chi_tieu', class: 'align-middle'},
                { data: 'phone', class: 'align-middle'},
                // { data: 'date_action', class: 'align-middle'},
                // { data: 'user.full_name', class: 'align-middle'},
                { data: 'action', name: "action", class: 'align-middle',},
            ]
        });
    }

    function _loadProvince(type = 'provinces', province_id = null, ward_id = null, $select, value_trigger = null) {        
        $.ajax({
            url: ELEMENTS.route_province,
            method: "GET",
            data: {
                province_id: province_id,
                ward_id: ward_id,
                type: type,
            },
            beforeSend: function(){
                if($select){
                    $select.find("option[value!='']").remove();
                }
            },
            success: function(res){
                if(res.success) {
                    if(type === 'all') {

                        let options = "<option></option>";
                        $.each(res.data.provinces, function(index, item){
                            options += `<option ${province_id && province_id == item.id ? "selected" : ''} value="${item.id}">${item.text}</option>`;
                        });
                        ELEMENTS.select_province.html(options);

                        options = "<option></option>";
                        $.each(res.data.wards, function(index, item){
                            options += `<option ${ward_id && ward_id == item.id ? "selected" : ''} value="${item.id}">${item.text}</option>`;
                        });
                        ELEMENTS.select_ward.html(options);


                    } else {
                        let results = res.data[type];

                        let text = "Chọn tỉnh thành"
                        if(type === 'wards') {
                            text = "Chọn phường xã";
                        }

                        results.unshift({
                            id: "",
                            text: text,
                        });
                        $select.select2({
                            data: res.data[type],
                        });
                    }
                }
            }
        });
    }

    ELEMENTS.modal_form.on('hidden.bs.modal', function (e) {
       ELEMENTS.modal_form.find('.form-control').removeClass('is-invalid')
       ELEMENTS.modal_form.find('.invalid-feedback').empty();
       ELEMENTS.modal_form.find('#form_action')[0].reset();
       ELEMENTS.modal_form.find('#form_action').attr('action', '');

       ELEMENTS.modal_form.find('#province_code').val('').trigger('change');
       ELEMENTS.modal_form.find('#district_code').val('').trigger('change');
       ELEMENTS.modal_form.find('#ward_code').val('').trigger('change');

       ELEMENTS.modal_form.find('.btn_action').attr('id', '');
    });

    $(document).ready(function(){
        let params = new URLSearchParams(window.location.search);
        if (params.get("search")) $.trim($("#searchInput").val(params.get("search")));
        if (params.get("phone")) $.trim($("#phoneInput").val(params.get("phone")));
        if (params.get("province_id")) $.trim($("#provinceInput").val(params.get("province_id")));

        renderTable(window.location.search);

        ELEMENTS.btn_filter.click(function(e){
            e.preventDefault();

            let search = $.trim($("#searchInput").val());
            let phone = $.trim($("#phoneInput").val());
            let province_id = $.trim($("#provinceInput").val());
            

            let params = new URLSearchParams();

            if (search) params.set("search", search);
            if (phone) params.set("phone", phone);
            if (province_id) params.set("province_id", province_id);
            

            const queryString = params.toString();
            const fullUrl = window.location.pathname + '?' + queryString;

            // Reload với query string
            window.history.pushState({}, '', fullUrl);
            // mai xử lý search ajax
            ELEMENTS.table_manage.DataTable().clear().destroy();
            renderTable(queryString)
        });

        ELEMENTS.btn_clear_filter.click(function(e){
            const baseUrl = window.location.origin + window.location.pathname;
            window.history.pushState({}, "", baseUrl);
            window.location.href = baseUrl;
        })

        ELEMENTS.btn_show_modal_add.click(function(e){
            e.preventDefault();
            _loadProvince('provinces', null, null, ELEMENTS.select_province, null);
            ELEMENTS.modal_form.modal('show');
            ELEMENTS.modal_form.find('#modal_title').text('Thêm mới khách hàng');
            ELEMENTS.modal_form.find('.btn_action').attr('id', 'btn_add');
            ELEMENTS.modal_form.find('.btn_action .add-new').text('Thêm mới');
        })

        ELEMENTS.select_province.change(function(e, value_trigger = null){
            let $this = $(this);
            let code = $this.val();
            if(code) {
                _loadProvince('wards', code, null, ELEMENTS.select_ward, null);
            } else {
                ELEMENTS.select_district.find('option[value!=""]').remove();
                ELEMENTS.select_ward.find('option[value!=""]').remove();
            }
        })
    })

    $(document).on('click', '#btn_add', function(e){
        e.preventDefault();
        let $this = $(this);
        $.ajax({
            url: ELEMENTS.route_add,            
            type: "POST",
            data: ELEMENTS.modal_form.find('#form_action').serialize(),
            beforeSend: function(){
                ELEMENTS.modal_form.find('#form_action').find('.form-control').removeClass('is-invalid');
                ELEMENTS.modal_form.find('#form_action').find('.invalid-feedback').empty();

                $this.prop("disabled", true);
                $this.find('#loading').show();
                $this.find('.add-new').hide();
            },
            success: function(res){
                if(res.success) {
                    ELEMENTS.modal_form.modal('hide')
                    createToast('success', res.message);
                    ELEMENTS.table_manage.DataTable().destroy();
                    ELEMENTS.table_manage.find('tbody').empty();
                    renderTable(window.location.search);
                }
            },
            error: function(err){
                let response_err = err.responseJSON;
                if(response_err) {
                    $.each(response_err.errors, function(key, item){
                        if(key === 'gender') {
                            ELEMENTS.modal_form.find('#form_action').find('#male').parent().parent().addClass('is-invalid')
                            ELEMENTS.modal_form.find('#form_action').find('#male').parent().parent().next().text(item[0]);
                        } else if(key === 'province_code' || key === 'district_code' || key === 'ward_code'){
                            ELEMENTS.modal_form.find('#form_action').find("#"+key).addClass('is-invalid');
                            ELEMENTS.modal_form.find('#form_action').find("#"+key).next().next().text(item[0]);
                        } else {
                            ELEMENTS.modal_form.find('#form_action').find("#"+key).addClass('is-invalid');
                            ELEMENTS.modal_form.find('#form_action').find("#"+key).next().text(item[0]);
                        }
                    })
                }
            },
            complete: function(){
                $this.prop("disabled", false);
                $this.find('#loading').hide();
                $this.find('.add-new').show();
            }
        });
    });

    $(document).on('click', '#btn_edit', function(e){
        e.preventDefault();
        let $this = $(this);
        let form = ELEMENTS.modal_form.find('#form_action');
        let action = form.attr('action');
        $.ajax({
            url: action,
            method: "PUT",
            data: form.serialize()+"&method=PUT",
            beforeSend: function(){
                form.find('.form-control').removeClass('is-invalid');
                form.find('.invalid-feedback').empty();

                $this.prop("disabled", true);
                $this.find('#loading').show();
                $this.find('.update').hide();
            },
            success: function(response){
                if(response.success) {
                    ELEMENTS.modal_form.modal('hide')
                    createToast('success', response.message);
                    ELEMENTS.table_manage.DataTable().destroy();
                    ELEMENTS.table_manage.find('tbody').empty();
                    renderTable(window.location.search);
                }
                
            },
            error: function(err){
                let response_err = err.responseJSON;

                if(response_err) {
                    $.each(response_err.errors, function(key, item){
                        if(key === 'gender') {
                            ELEMENTS.modal_form.find('#form_action').find('#male').parent().parent().addClass('is-invalid')
                            ELEMENTS.modal_form.find('#form_action').find('#male').parent().parent().next().text(item[0]);
                        } else if(key === 'province_code' || key === 'district_code' || key === 'ward_code'){
                            ELEMENTS.modal_form.find('#form_action').find("#"+key).addClass('is-invalid');
                            ELEMENTS.modal_form.find('#form_action').find("#"+key).next().next().text(item[0]);
                        } else {
                            ELEMENTS.modal_form.find('#form_action').find("#"+key).addClass('is-invalid');
                            ELEMENTS.modal_form.find('#form_action').find("#"+key).next().text(item[0]);
                        }
                    })

                    if(response_err.data?.length === 0){
                        ELEMENTS.modal_form.modal('hide');
                        $("#not_fount_modal").find('#modal_title_not_found').text(response_err.message);
                        $("#not_fount_modal").modal('show');
                    }
                }
                
            },
            complete: function(){
                $this.prop("disabled", false);
                $this.find('#loading').hide();
                $this.find('.update').show();
            }
        })
    })

    $(document).on('click', '.edit-record', function(e){
        e.preventDefault();
        let $this = $(this);
        ELEMENTS.modal_form.modal('show');
        ELEMENTS.modal_form.find('#modal_title').text('Chỉnh sửa khách hàng');
        ELEMENTS.modal_form.find('.btn_action').attr('id', 'btn_edit');
        ELEMENTS.modal_form.find('.btn_action .add-new').text('Cập nhật');

        $.ajax({
            url: $this.attr('data-action'),
            type: "GET",
            beforeSend: function(){
                $this.prop('disabled', true);
                $this.find('#loading').show();
                $this.find('i').hide();
            },
            success: async function(res){
                if(res.success) {
                    // code này dành cho khi edit load tỉnh thành
                    _loadProvince('all', res.data.province_code, res.data.ward_code, null, null);
                    
                    $.each(res.data, function(key, value){
                        if(key === "gender") {
                            ELEMENTS.modal_form.find('input:radio[name="gender"]').prop('checked', false).filter(`[value="${value}"]`).attr("checked", true).trigger("click");
                        } else if(key !== 'province_code' && key !== 'ward_code') {
                            ELEMENTS.modal_form.find('#'+key).val(value);
                        }
                    });
                    
                    ELEMENTS.modal_form.find('#form_action').attr('action', ELEMENTS.route_update.replace(':id', $this.attr('data-record')));
                    ELEMENTS.modal_form.modal('show');
                }
            },
            error: function(err){
                let data_error = err.responseJSON;
                if(data_error.success === false) {
                    $("#not_fount_modal").find('#modal_title_not_found').text(data_error.message);
                    $("#not_fount_modal").modal('show');
                }
            },
            complete: function(){
                $this.prop('disabled', false);
                $this.find('#loading').hide();
                $this.find('i').show();
            }
        });
    })

    $(document).on("click", ".remove-record", async function(){
        let result = confirm("Có chắc muốn xoá dữ liệu?");
        if(!result) {
            return;
        }

        let $this = $(this);
        let record = $this.data('record');
        let action = ELEMENTS.route_delete;
        action = action.replace(':id', record);

        $.ajax({
            url: action,
            type: "DELETE",
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
            },
            beforeSend: function(){
                // $this.prop('disabled', true);
                // $this.find('#loading').show();
                // $this.find('i').hide();
            },
            success: function(res){
                if(res.success) {
                    createToast('success', res.message);
                    ELEMENTS.table_manage.DataTable().destroy();
                    ELEMENTS.table_manage.find('tbody').empty();
                    renderTable(window.location.search);
                }
            },
            error: function(err){
                let data_error = err.responseJSON;
                if(data_error.success === false) {
                    $("#not_fount_modal").find('#modal_title_not_found').text(data_error.message);
                    $("#not_fount_modal").modal('show');
                }
            },
            complete: function(){
            //     $this.prop('disabled', false);
            //     $this.find('#loading').hide();
            //     $this.find('i').show();
            // }
        }});
    })


</script>
@endpush