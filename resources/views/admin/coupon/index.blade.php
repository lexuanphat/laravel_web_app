@extends('_blank')
@section('content')
<div class="card mt-2">
    <div class="card-body d-none">
        <div class="row g-2 align-items-center">
    
        <!-- Thanh tìm kiếm chính -->
        <div class="col">
            <div class="input-group">
            <span class="input-group-text"><i class="mdi mdi-magnify"></i></span>
            <input type="text" id="searchInput" class="form-control" placeholder="Tìm kiếm tên sản phẩm">
            </div>
        </div>
    
        <!-- Dropdown Trạng thái -->
        <div class="col">
            <select id="provinceSelect" class="form-control select2" data-toggle="select2">
                <option value="">Khu vực</option>
                @foreach($provinces as $item)
                <option value="{{$item->id}}">{{$item->text}}</option>
                @endforeach
            </select>
        </div>
    
        <!-- Nút lưu -->
        <div class="col-md-2">
            <button class="btn btn-outline-danger" id="clear-filter">Xoá lọc</button>
            <button class="btn btn-outline-primary" id="btn-filter">Lọc</button>
        </div>
    
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <div class="button-actions">
            <button type="button" class="btn btn-primary mb-2" id="btn_show_modal_create" data-bs-toggle="modal" data-bs-target="#modal_action"><i class="mdi mdi-plus-circle"></i> Thêm mới phiếu giảm giá</button>
        </div>
        <table id="table_manage" data-action="{{route('admin.coupon.get_data')}}" class="table dt-responsive w-100">
            <thead>
                <tr>
                    <th>
                        <div class="text-uppercase align-middle">STT</div>
                    </th>
                    <th>
                        <div class="text-uppercase align-middle">Tên phiếu</div>
                    </th>
                    <th>
                        <div class="text-uppercase align-middle">Giảm theo</div>
                    </th>
                    <th>
                        <div class="text-uppercase align-middle">Giá trị giảm</div>
                    </th>
                    <th>
                        <div class="text-uppercase align-middle">Ngày bắt đầu</div>
                    </th>
                    <th>
                        <div class="text-uppercase align-middle">Ngày kết thúc</div>
                    </th>
                    <th>
                        <div class="text-uppercase align-middle">Ngày thao tác</div>
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
<div class="list-modal">
    @include('admin.coupon.modals.form')
    @include('admin._partials.modal-noti.not-found')
</div>
@endsection
@push('js')
<script>
    const assets_storage = @json(asset("storage/:image_url"));
    const title = {
        create: "Thêm mới phiếu giảm giá",
        edit: "Chỉnh sửa phiếu giảm giá",
        btn_create: "Thêm mới",
        btn_edit: "Cập nhật",
    };

    const route = {
        create: @json(route('admin.coupon.store')),
        update: @json(route('admin.coupon.update', ['id' => ':id'])),
        delete: @json(route('admin.coupon.delete', ['id' => ':id'])),
        get_data_product: @json(route('admin.coupon.get_data_product')),
    };

    const elements = {
        modal: $("#modal_action"),
        table_manage: $("#table_manage"),
        btn_show_modal_create: $("#btn_show_modal_create"),
        _token: $("meta[name='csrf-token']").attr('content'),
        mask_money: $(".mask_money")
    };

    const elements_modal = {
        title: elements.modal.find('#modal_title'),
        form: elements.modal.find('#form_action'),
        date_start_apply: elements.modal.find('#date_start_apply'),
        date_end_apply: elements.modal.find('#date_end_apply'),
        name: elements.modal.find('#name'),
        type: elements.modal.find('#type'),
        fee: elements.modal.find('#fee'),
        text_action: elements.modal.find('#text_action'),
        btn_submit: elements.modal.find('#btn_submit'),
        loading: elements.modal.find('#loading'),
    };

    elements.modal.on('hidden.bs.modal', function(){
        elements_modal.form[0].reset();
        elements_modal.form.find('.form-control').removeClass('is-invalid');
        elements_modal.form.find('.invalid-feedback').empty();
        elements_modal.form.find('#fee').val(0);
    })

    elements_modal.type.change(function(){
        let $this = $(this);

        elements_modal.fee.val(0);

        if($this.val() === 'PHAN_TRAM'){
            $("#prefix_value").text("%");
        } else {
            $("#prefix_value").text("VNĐ");
        }
    });

    elements_modal.fee.keyup(function(){
        let $this = $(this);
        let value = Number($this.val());

        if(elements_modal.type.val() === 'PHAN_TRAM'){
            elements_modal.fee.val(value > 100 ? 100: value);
        }

    })

    function renderTable(search){
        elements.table_manage.DataTable({
            language: {
                    url: @json(asset('/assets/js/vi.json')),
                },
                ajax: {
                    url: elements.table_manage.data('action'),
                    type: "GET",
                    data: {
                        search: search,
                    }
                },
                searching: false,
                stateSave: true,
                processing: true,
                serverSide: true,
                ordering: false,
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', class: 'align-middle', width: "3%"},
                    { data: 'name', class: 'align-middle all',},
                    { data: 'type', class: 'align-middle'},
                    { data: 'fee', name: "action", class: 'align-middle'},
                    { data: 'date_start_apply', class: 'align-middle'},
                    { data: 'date_end_apply', class: 'align-middle'},
                    { data: 'date_action', class: 'align-middle'},
                    { data: 'user_action', class: 'align-middle'},
                    { data: 'action', name: "action", class: 'align-middle', width: '10%',},
                ],
            });
    }

    function formatRepo (repo) {
        
        if (repo.loading) {
            return repo.text;
        }

        var $container = $(`
            <div class='select2-result-repository clearfix d-flex flex-wrap gap-2'>
            <div class='select2-result-repository__avatar col-1'><img class='img-fluid' src='${assets_storage.replace(':image_url', repo.image_url)}' /></div>
            <div class='select2-result-repository__meta col'>
                <div class='select2-result-repository__title'>${repo.name} - ${Number(repo.price).toLocaleString("vi")}</div>
                <div class='select2-result-repository__code text-warning'>${repo.code}</div>
                <div class='select2-result-repository__sku text-danger'>${repo.sku ? repo.sku : ''}</div>
                </div>
            </div>
            </div>
        `);

        return $container;
    }

    function formatRepoSelection(repo){
        let name = repo.name && repo.price && repo.code ? `${repo.name} - ${Number(repo.price).toLocaleString("vi")} - ${repo.code}` : '';
        return name || repo.text;
    }

    function createItem(url, data){
        $.ajax({
            url: url,
            method: "POST",
            data: data,
            processData: false,
            contentType: false,
            beforeSend: function(){
                elements_modal.form.find('.form-control').removeClass('is-invalid');
                elements_modal.form.find('.invalid-feedback').empty();

                elements.modal.find("#btn_add").prop("disabled", true);
                elements_modal.loading.show();
                elements_modal.text_action.hide();
            },
            success: function(response){
                if(response.success) {
                    elements.modal.modal('hide')
                    createToast('success', response.message);
                    elements.table_manage.DataTable().destroy();
                    elements.table_manage.find('tbody').empty();
                    renderTable(window.location.search);
                }
                
            },
            error: function(err){
                let response_err = err.responseJSON;
                if(response_err) {
                    $.each(response_err.errors, function(key, item){
                        elements.modal.find("#"+key).addClass('is-invalid');
                        if(elements.modal.find("#"+key).is('select')) {
                            elements.modal.find("#"+key).next().next().text(item[0]);
                        } else {
                            elements.modal.find("#"+key).next().text(item[0]);
                        }
                    })
                }
                
            },
            complete: function(){
                elements_modal.btn_submit.prop("disabled", false);
                elements_modal.loading.hide();
                elements_modal.text_action.show();
            }
        })
    }

    function deleteRecord(action) {
        $.ajax({
            url: action,
            type: "DELETE",
            data: {
                _token: elements._token,
            },
            beforeSend: function(){
            },
            success: function(res){
                if(res.success) {
                    createToast('success', res.message);
                    elements.table_manage.DataTable().destroy();
                    elements.table_manage.find('tbody').empty();
                    renderTable(window.location.search);;
                }
            },
            error: function(err){
                let data_error = err.responseJSON;
                if(data_error.success === false) {
                    $("#not_fount_modal").find('#modal_title_not_found').text(message_errors.title_not_exists);
                    $("#not_fount_modal").find('#modal_text_not_found').text(data_error.message);
                    $("#not_fount_modal").modal('show');
                }
            },
            complete: function(){

            }
        });
    }

    $(document).ready(function(){
        let params = new URLSearchParams(window.location.search);
        if (params.get("search")) $.trim($("#searchInput").val(params.get("search")));
        if (params.get("province_id")) $("#provinceSelect").val(params.get("store")).trigger("change");
        
        renderTable(window.location.search);

        elements.btn_show_modal_create.click(function(e){
            e.preventDefault();
            elements_modal.form.attr('action', route.create)
            elements_modal.title.text(title.create);
            elements_modal.text_action.parent().attr('id', 'btn_add');
            elements_modal.text_action.text(title.btn_create);
            elements.modal.modal('show');
        });

        // elements_modal.product_id.select2({
        //     ajax: {
        //         delay: 250,
        //         url: route.get_data_product,
        //         data: function (params) {
        //         var query = {
        //             search: params.term,
        //             page: params.page || 1
        //         }

        //         return query;
        //         },
        //         processResults: function(res, params){
        //             params.page = params.page || 1;
                    
        //             return {
        //                 results: res.data.data,
        //                 pagination: {
        //                     more: res.data.current_page < res.data.last_page
        //                 }
        //             };
        //         },
        //     },
        //     placeholder: 'Chọn sản phẩm',
        //     minimumInputLength: 0,
        //     templateResult: formatRepo,
        //     templateSelection: formatRepoSelection
        // });

        elements.mask_money.on('keyup', function(e){
            let $this = $(this);
            let value = Number($this.val().replace(/\D/g,''));
            $this.val(value.toLocaleString('vi'))
        }).on('focus', function(e){
            if(Number($(this).val()) === 0) {
                $(this).val("")
            }
        }).on('blur', function(e){
            if($(this).val() === "") {
                $(this).val(0)
            }
        })

        elements.modal.on('hidden.bs.modal', function(e){
            elements_modal.form.find('select.select2').trigger("").change();
            elements_modal.form[0].reset();
        })

        $("#btn-filter").click(function(e){
            e.preventDefault();

            let search = $.trim($("#searchInput").val());
            let storeSelect = $("#storeSelect").val();

            let params = new URLSearchParams();

            if (search) params.set("search", search);
            if (storeSelect) params.set("store", storeSelect);

            const queryString = params.toString();
            const fullUrl = window.location.pathname + '?' + queryString;

            // Reload với query string
            window.history.pushState({}, '', fullUrl);
            // mai xử lý search ajax
            elements.table_manage.DataTable().clear().destroy();
            renderTable(queryString)
        });

        $("#clear-filter").click(function(e){
            const baseUrl = window.location.origin + window.location.pathname;
            window.history.pushState({}, "", baseUrl);
            window.location.href = baseUrl;
        })
    });

    $(document).on("click", ".remove-record", function(){
        let result = confirm("Có chắc muốn xoá dữ liệu?");
        if(!result) {
            return;
        }

        let $this = $(this);
        let record = $this.data('record');
        let action = route.delete.replace(':id', record);
        deleteRecord(action);
    })

    $(document).on('click', '.edit-record', function(e){
        e.preventDefault();
        let $this = $(this);
        let record = $this.attr('data-record');
        elements_modal.form.attr('action', route.update.replace(':id', record));
        elements_modal.title.text(title.edit);
        elements_modal.text_action.text(title.btn_edit);


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
        
                    $.each(res.data, function(key, value){
                       if(key === 'type') {
                        $("#"+key).val(value).trigger('change');
                       } else {
                        $("#"+key).val(value);
                       }
                    });
                    
                    elements_modal.text_action.parent().attr('id', 'btn_edit');
                    elements.modal.modal('show');
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

    $(document).on('click', '#btn_add', function(e){
        e.preventDefault();
        let form_data = new FormData(elements_modal.form[0]);
        createItem(route.create, form_data);
    })

    $(document).on('click', '#btn_edit', function(e){
        e.preventDefault();
        let $this = $(this);

        let form = elements_modal.form;
        let action = form.attr('action');
        $.ajax({
            url: action,
            method: "PUT",
            data: form.serialize()+"&method=PUT",
            beforeSend: function(){
                elements_modal.form.find('.form-control').removeClass('is-invalid');
                elements_modal.form.find('.invalid-feedback').empty();

                $this.prop("disabled", true);
                elements_modal.loading.show();
                elements_modal.text_action.hide();
            },
            success: function(response){
                if(response.success) {
                    elements.modal.modal('hide')
                    createToast('success', response.message);
                    elements.table_manage.DataTable().destroy();
                    elements.table_manage.find('tbody').empty();
                    renderTable(window.location.search);
                }
                
            },
            error: function(err){
                let response_err = err.responseJSON;

                if(response_err) {
                    $.each(response_err.errors, function(key, item){
                        elements.modal.find("#"+key).addClass('is-invalid');
                        if(elements.modal.find("#"+key).is('select')) {
                            elements.modal.find("#"+key).next().next().text(item[0]);
                        } else {
                            elements.modal.find("#"+key).next().text(item[0]);
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
                elements_modal.loading.hide();
                elements_modal.text_action.show();
            }
        })
    })

</script>
@endpush