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
                        <div class="text-uppercase align-middle">Tên</div>
                        <div class="text-uppercase align-middle">/ Mã khách hàng</div>
                    </th>
                    <th>
                        <div class="text-uppercase align-middle">Số điện thoại</div>
                        <div class="text-uppercase align-middle">/ Email</div>
                        <div class="text-uppercase align-middle">/ Ngày sinh</div>
                    </th>
                    <th>
                        <div class="text-uppercase align-middle">Ngày tạo</div>
                    </th>
                    <th>
                        <div class="text-uppercase align-middle">Ngày cập nhật</div>
                    </th>
                    <th>
                        <div class="text-uppercase align-middle">Người thao tác</div>
                    </th>
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
                { data: 'full_name', width: '25%', class: 'align-middle'},
                { data: 'info', width: '30%', class: 'align-middle'},
                { data: 'created_at', class: 'align-middle'},
                { data: 'updated_at', class: 'align-middle'},
                { data: 'user.full_name', class: 'align-middle'},
                { data: 'action', name: "action", class: 'align-middle', width: '15%',},
            ]
        });
    }

    function _loadProvince(file_json, $select, value_trigger = null) {        
        $.getJSON(file_json, function(data) {
            $select.find('option[value!=""]').remove();

            $.each(data, function(index, item) {
                let selected = value_trigger && value_trigger == item.code ? 'selected' : '';
                $select.append('<option value="' + item.code + '" ' + selected + '>' + item.name + '</option>');
            });
        }).fail(function() {
            $select.empty().append('<option value="-100">Không tải được dữ liệu</option>');
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

        renderTable(window.location.search);

        _loadProvince('/dist/tinh_tp.json', ELEMENTS.select_province, null);

        ELEMENTS.btn_filter.click(function(e){
            e.preventDefault();

            let search = $.trim($("#searchInput").val());
            let phone = $.trim($("#phoneInput").val());
            

            let params = new URLSearchParams();

            if (search) params.set("search", search);
            if (phone) params.set("phone", phone);
            

            const queryString = params.toString();
            const fullUrl = window.location.pathname + '?' + queryString;

            // Reload với query string
            window.history.pushState({}, '', fullUrl);
            // mai xử lý search ajax
            elements.table_manage_customer.DataTable().clear().destroy();
            renderTable(queryString)
        });

        ELEMENTS.btn_clear_filter.click(function(e){
            const baseUrl = window.location.origin + window.location.pathname;
            window.history.pushState({}, "", baseUrl);
            window.location.href = baseUrl;
        })

        ELEMENTS.btn_show_modal_add.click(function(e){
            e.preventDefault();
            ELEMENTS.modal_form.modal('show');
            ELEMENTS.modal_form.find('#modal_title').text('Thêm mới khách hàng');
            ELEMENTS.modal_form.find('.btn_action').attr('id', 'btn_add');
            ELEMENTS.modal_form.find('.btn_action .add-new').text('Thêm mới');
        })

        ELEMENTS.select_province.change(function(e, value_trigger = null){
            let $this = $(this);
            let code = $this.val();
            if(code) {
                _loadProvince(`/dist/quan-huyen/${code}.json`, ELEMENTS.select_district, value_trigger);
            } else {
                ELEMENTS.select_district.find('option[value!=""]').remove();
                ELEMENTS.select_ward.find('option[value!=""]').remove();
            }
        })

        ELEMENTS.select_district.change(function(e, value_trigger = null){
            let $this = $(this);
            let code = $this.val();
            if(code){
                _loadProvince(`/dist/xa-phuong/${code}.json`, ELEMENTS.select_ward, value_trigger);
            } else {
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
            success: function(res){
                if(res.success) {
                    $.each(res.data, function(key, value){
                        if(key === "gender") {
                            ELEMENTS.modal_form.find('input:radio[name="gender"]').prop('checked', false).filter(`[value="${value}"]`).attr("checked", true).trigger("click");
                        } else if(key === 'province_code'){
                            _loadProvince('/dist/tinh_tp.json', ELEMENTS.select_province, value);
                        } else if(key === 'district_code'){
                            _loadProvince(`/dist/quan-huyen/${res.data.province_code}.json`, ELEMENTS.select_district, value);
                        } else if(key === 'ward_code'){
                            _loadProvince(`/dist/xa-phuong/${res.data.district_code}.json`, ELEMENTS.select_ward, value);
                        } else {
                            ELEMENTS.modal_form.find('#'+key).val(value);
                        }
                    })
                    
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