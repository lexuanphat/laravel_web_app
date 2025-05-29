@extends('_blank')
@section('content')
<div class="bg-white p-2 my-2">
    <div class="button-actions">
        <button type="button" class="btn btn-primary mb-2" id="btn_show_modal_create" data-bs-toggle="modal" data-bs-target="#modal_create"><i class="mdi mdi-plus-circle"></i> Nhập kho</button>
    </div>
    <table id="table_manage" data-action="{{route('admin.product_stock.get_data')}}" class="table dt-responsive w-100">
        <thead>
            <tr>
                <th>
                    <div class="text-uppercase align-middle">STT</div>
                </th>
                <th>
                    <div class="text-uppercase align-middle">Tên sản phẩm</div>
                    <div class="text-uppercase align-middle">/ Mã sản phẩm</div>
                    <div class="text-uppercase align-middle">/ Mã SKU</div>
                </th>
                <th>
                    <div class="text-uppercase align-middle">Số lượng tồn</div>
                    <div class="text-uppercase align-middle">/ Số lượng bán</div>
                    <div class="text-uppercase align-middle">/ Giá sản phẩm</div>
                </th>
                <th>
                    <div class="text-uppercase align-middle">Cửa hàng</div>
                </th>
                <th>
                    <div class="text-uppercase align-middle">Ngày tạo</div>
                    <div class="text-uppercase align-middle">/ Ngày cập nhật</div>
                    <div class="text-uppercase align-middle">/ Người thao tác</div>
                </th>
                <th>
                    <div class="text-uppercase align-middle">Chức năng</div>
                </th>
            </tr>
        </thead>


        <tbody></tbody>
    </table>
    <div class="list-modal">
        @include('admin.product_stock.modals.create')
        @include('admin._partials.modal-noti.not-found')
    </div>
</div>
@endsection
@push('js')
<script>
    const assets_storage = @json(asset("storage/:image_url"));
    const title = {
        create: "Nhập kho sản phẩm",
        btn_create: "Tiến hành nhập kho",
    };

    const route = {
        create: @json(route('admin.product_stock.store')),
        delete: @json(route('admin.product_stock.delete', ['id' => ':id'])),
        get_data_product: @json(route('admin.product_stock.get_data_product')),
    };

    const elements = {
        modal: $("#modal_create"),
        table_manage: $("#table_manage"),
        btn_show_modal_create: $("#btn_show_modal_create"),
        _token: $("meta[name='csrf-token']").attr('content'),
        mask_money: $(".mask_money")
    };

    const elements_modal = {
        title: elements.modal.find('#modal_title'),
        form: elements.modal.find('#form_create'),
        product_id: elements.modal.find('#product_id'),
        store_id: elements.modal.find('#store_id'),
        text_action: elements.modal.find('#text_action'),
        btn_submit: elements.modal.find('#btn_submit'),
        loading: elements.modal.find('#loading'),
    };

    function renderTable(){
        elements.table_manage.DataTable({
                language: {
                    // paginate: {
                    //     previous: "<i class='mdi mdi-chevron-left'>",
                    //     next: "<i class='mdi mdi-chevron-right'>"
                    // },
                    processing: '<div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div>',
                },
                ajax: {
                    url: elements.table_manage.data('action'),
                    type: "GET",
                },
                searching: false,
                stateSave: true,
                processing: true,
                serverSide: true,
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', class: 'align-middle', width: "3%"},
                    { data: 'data_col_2', class: 'align-middle all', width: "30%"},
                    { data: 'data_col_3', class: 'align-middle'},
                    { data: 'store.name', name: "action", class: 'align-middle'},
                    { data: 'date_action', class: 'align-middle'},
                    { data: 'action', name: "action", class: 'align-middle', width: '10%',},
                ],
                columnDefs: [
                    { "orderable": false, "targets": [0,2,3,4] },
                    { "orderable": true, "targets": [1] }
                ],
                order: [[1, 'asc']]
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
                <div class='select2-result-repository__sku text-danger'>${repo.sku}</div>
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

                elements_modal.btn_submit.prop("disabled", true);
                elements_modal.loading.show();
                elements_modal.text_action.hide();
            },
            success: function(response){
                if(response.success) {
                    elements.modal.modal('hide')
                    createToast('success', response.message);
                    elements.table_manage.DataTable().destroy();
                    elements.table_manage.find('tbody').empty();
                    renderTable();
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
                    renderTable();
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
        renderTable();

        elements.btn_show_modal_create.click(function(e){
            e.preventDefault();
            elements_modal.title.text(title.create);
            elements_modal.text_action.text(title.btn_create);
            elements.modal.modal('show');
        });

        elements_modal.product_id.select2({
            ajax: {
                delay: 250,
                url: route.get_data_product,
                data: function (params) {
                var query = {
                    search: params.term,
                    page: params.page || 1
                }

                return query;
                },
                processResults: function(res, params){
                    params.page = params.page || 1;
                    
                    return {
                        results: res.data.data,
                        pagination: {
                            more: res.data.current_page < res.data.last_page
                        }
                    };
                },
            },
            placeholder: 'Chọn sản phẩm',
            minimumInputLength: 2,
            templateResult: formatRepo,
            templateSelection: formatRepoSelection
        });

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

        elements_modal.btn_submit.click(function(e){
            e.preventDefault();
            let form_data = new FormData(elements_modal.form[0]);
            createItem(route.create, form_data);
        })

        elements.modal.on('hidden.bs.modal', function(e){
            elements_modal.form.find('select.select2').trigger("").change();
            elements_modal.form[0].reset();
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

</script>
@endpush