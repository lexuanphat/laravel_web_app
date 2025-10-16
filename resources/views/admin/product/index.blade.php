@extends('_blank')
@push('style') 
<link href="{{asset('assets/vendor/quill/quill.core.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('assets/vendor/quill/quill.snow.css')}}" rel="stylesheet" type="text/css" />  
<style>
    #select2-category_id-results li:has(#div-create-new) {
        background-color: #C01415;
    }
</style>
@endpush
@section('content')
<div class="card mt-2">
    <div class="card-body">
        <div class="row g-2 align-items-center filter-row">
    
            <!-- Thanh tìm kiếm chính -->
            <div class="col-md-5">
                <div class="input-group">
                <span class="input-group-text"><i class="mdi mdi-magnify"></i></span>
                <input type="text" id="searchInput" class="form-control" placeholder="Tìm kiếm tên sản phẩm">
                </div>
            </div>
    
            <div class="col-md-5">
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
                        <div class=align-middle">Ảnh</div>
                    </th>
                    <th>
                        <div class=align-middle">Tên sản phẩm</div>
                    </th>
                    <th>
                        <div class=align-middle">Danh mục</div>
                    </th>
                    <th>
                        <div class=align-middle">Ngày thao tác</div>
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
            get_data_tag: @json(route('admin.product.get_data_tag')),
            create_new_tag: @json(route("admin.tag.store")),
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
            tag: elements.modal.find('#tag_id'),
            tag_option: elements.modal.find('#tag_id option'),
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
                    { data: 'image', name: "image", class: 'align-middle all'},
                    { data: 'name', width: '30%', class: 'align-middle'},
                    { data: 'category', class: 'align-middle'},
                    { data: 'date_action', class: 'align-middle'},
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

        function addTagItem(data){
            $.ajax({
                url: route.create_new_tag,
                type: "POST",
                data: data,
                beforeSend: function(){},
                success: function(response){
                    if(response.success) {
                        let data = response.data;
                        let new_option = new Option(data.tag_name, data.id, true, true);
                        elements_modal.tag.append(new_option).trigger('change');
                    }
                },
                error: function(err){
                    alert("Có lỗi xảy ra, vui lòng thử lại");
                    elements_modal.tag.val("").trigger('change');
                },
            })
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
                            }else if(key === 'tag_id'){
                                elements_modal.tag.empty();
                                if(value.length > 0) {
                                    let new_options = [];
                                    value.forEach(item => {
                                        let option = new Option(item.tag_name, item.id, true, true);
                                        new_options.push(option);
                                    });
                                    elements_modal.tag.append(new_options).trigger('change');
                                }
                            }else if(key === 'image_url'){
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

                        results.unshift({
                            id: -1,
                            text: 'Tạo mới danh mục',
                            isNew: true,
                        });

                        return { results };
                    },
                    
                },
                templateResult: function (data) {
                    if (data.isNew) {
                        return $(`<div class="row" id="div-create-new">
                            <div class="item-create">
                                <i class="ri-add-box-line text-white"></i>
                                <span class="text-white">${data.text}</span>    
                            </div>
                        </div>`);
                    }
                    return data.text;
                },
            }).on('select2:select', function(e){
                let data = e.params.data;
                if(data.isNew) {
                    // let regex = /"([^"]*)"/;
                    // let regex_get_text = data.text.match(regex);

                    // let text_confirm = "";

                    // if (regex_get_text && regex_get_text[1]) {
                    //     text_confirm = `Tạo mới danh mục "${regex_get_text[1]}"`;
                    // } else {
                    //     text_confirm = data.text;
                    // }
                    
                    let confirm_create = prompt("Tạo mới danh mục");
                    
                    if(confirm_create) {
                        data_send = {
                            name: confirm_create,
                            _token: $('meta[name="csrf-token"]').attr('content'),
                        };
                        addCategoryItem(data_send);
                    } else {
                        elements_modal.category.val("").trigger('change');
                    }
                }
                
            });

            elements_modal.tag.select2({
                language: "vi",
                ajax: {
                    url: route.get_data_tag,
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
                            text: item.tag_name,
                        }));

                        return { results };
                    },
                },
            }).on('select2:select', function(e){
                let data = e.params.data;
                if(data.isNew) {
                    let confirm_create = confirm(`Tạo mới tag ${data.text}`);
                    if(confirm_create) {
                        data_send = {
                            tag_name: data.text,
                            _token: $('meta[name="csrf-token"]').attr('content'),
                        };
                        addTagItem(data_send);
                    } else {
                        elements_modal.tag.val("").trigger('change');
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
                let array_tag = elements_modal.tag.val();
                form_data.append('tag_id', array_tag.toString());
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
                elements_modal.tag.val([]).trigger("change");
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
@endpush
