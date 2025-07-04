@extends('_blank')
@push('style') 
<link href="{{asset('assets/vendor/quill/quill.core.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('assets/vendor/quill/quill.snow.css')}}" rel="stylesheet" type="text/css" />  
@endpush
@section('content')
<div class="card mt-2">
    <div class="card-body">
        <div class="row g-2 align-items-center">
    
            <!-- Thanh tìm kiếm chính -->
            <div class="col">
                <div class="input-group">
                <span class="input-group-text"><i class="mdi mdi-magnify"></i></span>
                <input type="text" id="searchInput" class="form-control" placeholder="Tìm kiếm tên sản phẩm">
                </div>
            </div>
    
            <div class="col">
                <select id="categorySelect" class="form-control select2" data-toggle="select2">
                    <option value="">Danh mục</option>
                    @foreach($categories as $cate)
                    <option value="{{$cate['id']}}">{{$cate['name']}}</option>
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
            <button type="button" id="btn_show_modal_create" class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#modal_product"><i class="mdi mdi-plus-circle"></i> Thêm mới sản phẩm</button>
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
                    </th>
                    <th>
                        <div class=align-middle">Ngày cập nhật</div>
                    </th>
                    <th>
                        <div class=align-middle">Người thao tác</div>
                    </th>
                    <th>
                        <div class=align-middle">Chức năng</div>
                    </th>
                </tr>
            </thead>


            <tbody></tbody>
        </table>
    </div>
</div>
<div class="list-modal">
    @include('admin.product.modals.create')
    @include('admin._partials.modal-noti.not-found')
</div>
@endsection
@push('js')
    <script src="{{asset('assets/vendor/quill/quill.js')}}"></script>
    <script>
        const title = {
            create: "Thêm mới sản phẩm",
            edit: "Chỉnh sửa sản phẩm",
            btn_create: "Thêm mới",
            btn_edit: "Cập nhật",
        };

        const route = {
            create: @json(route('admin.product.store')),
            update: @json(route('admin.product.update', ['id' => ':id'])),
            delete: @json(route('admin.product.delete', ['id' => ':id'])),
            get_data_category: @json(route('admin.product.get_data_category')),
            create_new_category: @json(route("admin.category.store")),
        };

        const elements = {
            modal: $("#modal_product"),
            table_manage: $("#table_manage"),
            btn_show_modal_create: $("#btn_show_modal_create"),
            _token: $("meta[name='csrf-token']").attr('content'),
            mask_money: $(".mask_money")
        };
        const elements_modal = {
            title: elements.modal.find('#modal_title'),
            form: elements.modal.find('#form'),
            name: elements.modal.find('#name'),
            sku: elements.modal.find('#sku'),
            category: elements.modal.find('#category_id'),
            price: elements.modal.find('#price'),
            desc: elements.modal.find('#desc'),
            snow_editor: elements.modal.find('#snow_editor'),
            quill_editor: null,
            file_image: {
                image_url: elements.modal.find('#image_url'),
                label_upload: elements.modal.find('#label_upload'),
                btn_remove_uploaded: elements.modal.find('#remove_uploaded'),
                image_preview: elements.modal.find('#img_preview'),
                url_default: @json(asset('assets/images/no-image.jpg')),
            },
            text_action: elements.modal.find('#text_action'),
            btn_submit: elements.modal.find('#btn_submit'),
            loading: elements.modal.find('#loading'),
        };

        function renderTable(search){
            elements.table_manage.DataTable({
                language: {
                    url: @json(asset('/assets/js/vi.json')),
                },
                ajax: {
                    url: elements.table_manage.data('action'),
                    type: "GET",
                    data: {
                        search: search
                    },

                },
                searching: false,
                stateSave: true,
                processing: true,
                serverSide: true,
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', class: 'align-middle', width: "5%"},
                    { data: 'name', name: "name", class: 'align-middle all'},
                    { data: 'data_col_3', width: '25%', class: 'align-middle'},
                    { data: 'created_at', class: 'align-middle'},
                    { data: 'updated_at', class: 'align-middle'},
                    { data: 'user.full_name', class: 'align-middle'},
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
                url: route.create_new_category,
                type: "POST",
                data: data,
                beforeSend: function(){},
                success: function(response){
                    if(response.success) {
                        let data = response.data;
                        let new_option = new Option(data.name, data.id, true, true);
                        elements_modal.category.append(new_option).trigger('change');
                    }
                },
                error: function(err){
                    alert("Có lỗi xảy ra, vui lòng thử lại");
                    elements_modal.category.val("").trigger('change');
                },
            })
        }

        function loadFile(event) {
            let output = elements_modal.file_image.image_preview[0];
            output.src = URL.createObjectURL(event.target.files[0]);
            output.onload = function() {
                URL.revokeObjectURL(output.src) // free memory
            }
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

        function updateItem(url, data){
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

                }
            });
        }

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
                        elements_modal.title.text(title.edit);
                        elements_modal.text_action.text(title.btn_edit);
                        $.each(res.data, function(key, value){
                            if(key === 'desc') {
                                elements_modal.quill_editor.root.innerHTML = value;
                            } else if(key === 'category'){
                                let option = new Option(value.name, value.id, true, true);
                                elements_modal.category.append(option).trigger('change');
                            } else if(key === 'image_url'){
                                elements_modal.file_image.image_preview.attr('src', @json(asset("storage/:url")).replace(':url', value))
                                elements.modal.find('#current_image').val(value);
                            } else {
                                elements.modal.find('#'+key).val(value);
                            }
                        })
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
        }

        $(document).ready(function(){
            let params = new URLSearchParams(window.location.search);
            if (params.get("search")) $.trim($("#searchInput").val(params.get("search")));
            if (params.get("category")) $("#categorySelect").val(params.get("category")).trigger("change");
            
            renderTable(window.location.search);

            elements.btn_show_modal_create.click(function(e){
                e.preventDefault();
                elements_modal.title.text(title.create);
                elements_modal.text_action.text(title.btn_create);
                elements.modal.modal('show');
            })

            elements_modal.category.select2({
                minimumInputLength: 3,
                language: "vi",
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
                        elements_modal.category.val("").trigger('change');
                    }
                }
                
            });

            elements_modal.quill_editor = new Quill(elements_modal.snow_editor[0], {
                theme: 'snow'
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

            elements_modal.file_image.btn_remove_uploaded.click(function(e){
                e.preventDefault();
                if(!elements_modal.file_image.image_url.val() && !elements.modal.find('#current_image').val()) {
                    return;
                }

                elements_modal.file_image.image_url.val("");
                elements.modal.find('#current_image').val("");
                elements_modal.file_image.image_preview.attr("src", elements_modal.file_image.url_default);
            })

            elements_modal.btn_submit.click(function(e){
                e.preventDefault();
                elements_modal.desc.val(elements_modal.quill_editor.getSemanticHTML());
                let form_data = new FormData(elements_modal.form[0]);
                form_data.append('weight', $("#weight").val().replaceAll(".", ""))

                if(elements_modal.text_action.text() === title.btn_create) {
                    createItem(route.create, form_data);
                } else {
                    form_data.append('_method', 'PUT');
                    updateItem(route.update.replace(":id", elements.modal.find('#id').val()), form_data);
                }
            })

            elements.modal.on('hidden.bs.modal', function (e) {
                elements_modal.form.find('.form-control').removeClass('is-invalid')
                elements_modal.form.find('.invalid-feedback').empty();
                elements_modal.category.val("").trigger("change");
                elements_modal.quill_editor.setContents([]);
                elements_modal.form[0].reset();
            });

            $("#btn-filter").click(function(e){
                e.preventDefault();

                let search = $.trim($("#searchInput").val());
                let categorySelect = $("#categorySelect").val();

                let params = new URLSearchParams();

                if (search) params.set("search", search);
                if (categorySelect) params.set("category", categorySelect);

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
        })

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

        $(document).on("click", ".edit-record", async function(){
            let $this = $(this);
            let record = $this.data('record');
            let action = $this.data('action');
            getDataRecord(action, record, $this)
        })

        
    </script>
    {{-- <script>
        const elements = {
            btn_create: $("#btn_create"),
            btn_update: $("#btn_update"),
            form_create: $("#form_create"),
            form_edit: $("#form_edit"),
            modal_create: $("#modal_create"),
            modal_edit: $("#modal_edit"),
            table_manage: $("#table_manage"),
            action_update: @json(route('admin.category.update', ['id' => ':id'])),
            action_delete: @json(route('admin.category.delete', ['id' => ':id'])),
            action_get_category: @json(route('admin.product.get_data_category')),
            category_id_create: $("#modal_create #category_id"),
            category_id_edit: $("#modal_edit #category_id"),
            desc: $("#desc"),
            snow_editor_create: $("#modal_create #snow_editor"),
            snow_editor_edit: $("#modal_edit #snow_editor"),
            quill_create: null,
            quill_edit: null,
            image_url: $("#image_url"),
            label_image: $("#label_image"),
            img_preview: $("#img_preview"),
            url_default_img: @json(asset('assets/images/no-image.jpg')),
            delete_image: $("#delete_image"),
            mask_money: $(".mask_money")
        }

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
                        renderTable(window.location.search);
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
                        renderTable(window.location.search);
                        
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

        function renderTable(window.location.search){
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

            renderTable(window.location.search);

            elements.btn_create.click(function(e){
                e.preventDefault();
                let $this = $(this);
                let form = elements.form_create;
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

            elements.category_id_create.select2({
                minimumInputLength: 3,
                placeholder: 'Nhập hơn 3 kí tự để tìm',
                ajax: {
                    url: elements.action_get_category,
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
                        elements.category_id_create.val("").trigger('change');
                    }
                }
                
            });

            elements.category_id_edit.select2({
                minimumInputLength: 3,
                placeholder: 'Nhập hơn 3 kí tự để tìm',
                ajax: {
                    url: elements.action_get_category,
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
                        elements.category_id_create.val("").trigger('change');
                    }
                }
                
            });

            elements.quill_create = new Quill(elements.snow_editor_create[0], {
                theme: 'snow'
            });

            elements.quill_edit = new Quill(elements.snow_editor_edit[0], {
                theme: 'snow'
            });

            elements.label_image.click(function(e){
                e.preventDefault();
                elements.image_url.click();
            });

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
    </script> --}}
@endpush
