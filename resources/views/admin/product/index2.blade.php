@extends('_blank')
@push('style') 
<link href="{{asset('assets/vendor/quill/quill.core.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('assets/vendor/quill/quill.snow.css')}}" rel="stylesheet" type="text/css" />  
@endpush
@section('content')
    <div class="bg-white p-2 my-2">
        <div class="button-actions">
            <button type="button" class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#modal_create"><i class="mdi mdi-plus-circle"></i> Thêm mới sản phẩm</button>
        </div>
        <table id="table_manage" data-action="{{route('admin.product.get_data')}}" class="table dt-responsive w-100">
            <thead class="table-light">
                <tr>
                    <th>
                        <div class=align-middle">STT</div>
                    </th>
                    <th>
                        <div class=align-middle">Tên sản phẩm</div>
                        <div class=align-middle">/ Mã sản phẩm</div>
                        <div class=align-middle">/ Mã SKU</div>
                    </th>
                    <th>
                        <div class=align-middle">Danh mục</div>
                        <div class=align-middle">/ Số lượng tồn</div>
                        <div class=align-middle">/ Giá bán</div>
                    </th>
                    <th>
                        <div class=align-middle">Ngày tạo</div>
                        <div class=align-middle">/ Ngày cập nhật</div>
                        <div class=align-middle">/ Người thao tác</div>
                    </th>
                    <th>
                        <div class=align-middle">Chức năng</div>
                    </th>
                </tr>
            </thead>


            <tbody></tbody>
        </table>
        <div class="list-modal">
            @include('admin.product.modals.create')
            @include('admin.product.modals.edit')
            @include('admin._partials.modal-noti.not-found')
        </div>
    </div>
@endsection
@push('js')
    <script src="{{asset('assets/vendor/quill/quill.js')}}"></script>
    <script>
        const _token = $("#token").val();

        const modals = {
            create: $("#modal_create"),
            edit: $("#modal_edit"),
        };

        const form = {
            create: modals.find('#form_create'),
            edit: modals.find('#form_edit'),
        };

        const route = {
            update: @json(route('admin.category.update', ['id' => ':id'])),
            delete: @json(route('admin.category.delete', ['id' => ':id'])),
            get_data_category: @json(route('admin.product.get_data_category')),
        };

        const elements = {
            btn_create: form.create.find('#btn_create'),
            btn_update: form.edit.find('#btn_update'),

            fields_create: {
                name: form.create.find('input.name'),
                sku: form.create.find('input.sku'),
                category_id: form.create.find('select.category_id'),
                price: form.create.find('input.price'),
                image_url: form.create.find('input.image_url'),
                img_preview: form.create.find('img.img_preview'),
                label_image: form.create.find('label.label_image'),
                textarea: form.create.find('.textarea.desc'),
                quill: null,
                snow_editor: form.create.find('.snow_editor'),
            },

            fields_edit: {
                name: form.edit.find('input.name'),
                sku: form.edit.find('input.sku'),
                category_id: form.edit.find('select.category_id'),
                price: form.edit.find('input.price'),
                image_url: form.edit.find('input.image_url'),
                img_preview: form.edit.find('img.img_preview'),
                label_image: form.edit.find('label.label_image'),
                textarea: form.edit.find('.textarea.desc'),
                quill: null,
                snow_editor: form.edit.find('.snow_editor'),
            },
        }

        const global = {
            category_id: $(".category_id"),
            desc: $(".desc"),
        };

        function getDataRecord(action, id, $this) {
            $.ajax({
                url: action,
                type: "GET",
                beforeSend: function(){
                    $this.prop('disabled', true);
                    $this.find('#loading').show();
                    $this.find('i').hide();
                },
                success: function(res){
                    if(res.success) {
                        $.each(res.data, function(key, value){
                            if(key !== 'image_url') {
                                elements.modal_edit.find('#'+key).val(value);
                            }
                        })
                        let form_action = elements.form_edit.attr('action');
                        elements.form_edit.attr('action', elements.action_update.replace(':id', id));
                        elements.modal_edit.modal('show');
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
        }

        function deleteRecord(action) {
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
                        elements.table_manage.DataTable().destroy();
                        elements.table_manage.find('tbody').empty();
                        renderTableStore();
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
        }

        function createStore(url, data, form, button_click){
            $.ajax({
                url: url,
                method: "POST",
                data: data,
                processData: false,
                contentType: false,
                beforeSend: function(){
                    form.find('.form-control').removeClass('is-invalid');
                    form.find('.invalid-feedback').empty();

                    button_click.prop("disabled", true);
                    button_click.find('#loading').show();
                    button_click.find('.add-new').hide();
                },
                success: function(response){
                    if(response.success) {
                        elements.modal_create.modal('hide')
                        createToast('success', response.message);
                        elements.table_manage.DataTable().destroy();
                        elements.table_manage.find('tbody').empty();
                        renderTableStore();
                    }
                    
                },
                error: function(err){
                    let response_err = err.responseJSON;
                    if(response_err) {
                        $.each(response_err.errors, function(key, item){
                            elements.modal_create.find("#"+key).addClass('is-invalid');
                            if(elements.modal_create.find("#"+key).is('select')) {
                                elements.modal_create.find("#"+key).next().next().text(item[0]);
                            } else {
                                elements.modal_create.find("#"+key).next().text(item[0]);
                            }
                        })
                    }
                    
                },
                complete: function(){
                    button_click.prop("disabled", false);
                    button_click.find('#loading').hide();
                    button_click.find('.add-new').show();
                }
            })
        }

        function updateStore(url, form, button_click){
            $.ajax({
                url: url,
                method: "PUT",
                data: form.serialize()+"&method=PUT",
                beforeSend: function(){
                    form.find('.form-control').removeClass('is-invalid');
                    form.find('.invalid-feedback').empty();

                    button_click.prop("disabled", true);
                    button_click.find('#loading').show();
                    button_click.find('.update').hide();
                },
                success: function(response){
                    if(response.success) {
                        elements.modal_edit.modal('hide')
                        createToast('success', response.message);
                        elements.table_manage.DataTable().destroy();
                        elements.table_manage.find('tbody').empty();
                        renderTableStore();
                        
                    }
                    
                },
                error: function(err){
                    let response_err = err.responseJSON;
                    if(response_err) {
                        $.each(response_err.errors, function(key, item){
                            elements.modal_edit.find("#"+key).addClass('is-invalid');
                            elements.modal_edit.find("#"+key).next().text(item[0]);
                        })

                        if(response_err.data?.length === 0){
                            elements.modal_edit.modal('hide')
                            $("#not_fount_modal").find('#modal_title_not_found').text(response_err.message);
                            $("#not_fount_modal").modal('show');
                        }
                    }
                    
                },
                complete: function(){
                    button_click.prop("disabled", false);
                    button_click.find('#loading').hide();
                    button_click.find('.update').show();
                }
            })
        }

        function renderTableStore(){
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
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', class: 'align-middle', width: "5%"},
                    { data: 'name', name: "name", class: 'align-middle all'},
                    { data: 'data_col_3', width: '25%', class: 'align-middle'},
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

        function addCategoryItem(data){
            $.ajax({
                url: @json(route("admin.category.store")),
                type: "POST",
                data: data,
                beforeSend: function(){},
                success: function(response){
                    if(response.success) {
                        let data = response.data;
                        let newOption = new Option(data.name, data.id, true, true);
                        elements.category_id_create.append(newOption).trigger('change');
                    }
                },
                error: function(err){
                    alert("Có lỗi xảy ra, vui lòng thử lại");
                    elements.category_id_create.val("").trigger('change');
                },
            })
        }

        function loadFile(event) {
            let output = elements.img_preview[0];
            output.src = URL.createObjectURL(event.target.files[0]);
            output.onload = function() {
                URL.revokeObjectURL(output.src) // free memory
            }
        }

        $(document).ready(function() {

            renderTableStore();

            elements.btn_create.click(function(e){
                e.preventDefault();
                let $this = $(this);
                let form = form.create;
                elements.desc.val(elements.quill_create.getSemanticHTML());
                let form_data = new FormData(form[0]);
                createStore(form.attr('action'), form_data, form, $this);
            });

            elements.modal_create.on('hidden.bs.modal', function (e) {
                elements.form_create.find('.form-control').removeClass('is-invalid')
                elements.form_create.find('.invalid-feedback').empty();
                elements.form_create[0].reset();
            });

            elements.btn_update.click(function(e){
                e.preventDefault();
                let $this = $(this);
                let form = elements.form_edit;
                let action = form.attr('action');
                updateStore(action, form, $this);
            })

            global.category_id.select2({
                minimumInputLength: 3,
                placeholder: 'Nhập hơn 3 kí tự để tìm',
                ajax: {
                    url: route.get_data_category,
                    delay: 250,
                    type: 'GET',
                    data: function(params){
                        var query = {
                            search: params.term,
                        }

                        return query;
                    },
                    processResults: function(response, params){
                        let data = response.data;
                        let results = response.data.map(item => ({
                            id: item.id,
                            text: item.name,
                        }));

                        if(response.exists === false) {
                            results.push({
                                id: -1,
                                text: params.term,
                                isNew: true,
                            });
                        }

                        return { results };
                    },
                },
            }).on('select2:select', function(e){
                let data = e.params.data;
                if(data.isNew) {
                    let confirm_create = confirm(`Tạo mới danh mục ${data.text}`);
                    if(confirm_create) {
                        data_send = {
                            name: data.text,
                            _token: $('meta[name="csrf-token"]').attr('content'),
                        };
                        addCategoryItem(data_send);
                    } else {
                        global.category_id.val("").trigger('change');
                    }
                }
                
            });

            elements.fields_create.quill = new Quill(elements.fields_create.snow_editor[0], {
                theme: 'snow'
            });

            elements.fields_edit.quill = new Quill(elements.fields_edit.snow_editor[0], {
                theme: 'snow'
            });

            // elements.label_image.click(function(e){
            //     e.preventDefault();
            //     elements.image_url.click();
            // });

            elements.delete_image.click(function(e){
                e.preventDefault();
                if(!elements.image_url.val()) {
                    return;
                }

                elements.image_url.val("");
                elements.img_preview.attr("src", elements.url_default_img);
            })

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
        });

        $(document).on("click", ".edit-record", async function(){
            let $this = $(this);
            let record = $this.data('record');
            let action = $this.data('action');
            getDataRecord(action, record, $this)
        })

        $(document).on("click", ".remove-record", async function(){
            let result = confirm("Có chắc muốn xoá dữ liệu?");
            if(!result) {
                return;
            }

            let $this = $(this);
            let record = $this.data('record');
            let action = elements.action_delete;
            action = action.replace(':id', record);
            deleteRecord(action);
        })
    </script>
@endpush
