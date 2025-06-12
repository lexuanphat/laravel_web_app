@extends('_blank')
@section('content')
    <div class="bg-white p-2 my-2">
        <div class="button-actions">
            <button type="button" class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#modal_create"><i class="mdi mdi-plus-circle"></i> Thêm mới danh mục</button>
        </div>
        <table id="table_manage" data-action="{{route('admin.category.get_data')}}" class="table dt-responsive w-100">
            <thead>
                <tr>
                    <th>
                        <div class="text-uppercase align-middle">STT</div>
                    </th>
                    <th>
                        <div class="text-uppercase align-middle">Tên</div>
                        <div class="text-uppercase align-middle">/ Mã danh mục</div>
                    </th>
                    <th>
                        <div class="text-uppercase align-middle">Tổng sản phẩm</div>
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
            @include('admin.category.modals.create')
            @include('admin.category.modals.edit')
            @include('admin._partials.modal-noti.not-found')
        </div>
    </div>
@endsection
@push('js')
    <script>
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
                            console.log(key, value)
                            elements.modal_edit.find('#'+key).val(value);
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
                        $("#not_fount_modal").find('#modal_title_not_found').text(message_errors.title_not_exists);
                        $("#not_fount_modal").find('#modal_text_not_found').text(data_error.message);
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

        function createStore(url, form, button_click){
            $.ajax({
                url: url,
                method: "POST",
                data: form.serialize(),
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
                            elements.modal_create.find("#"+key).next().text(item[0]);
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
                    url: @json(asset('/assets/js/vi.json')),
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
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', class: 'align-middle'},
                    { data: 'name', width: '30%', name: "abc", class: 'align-middle'},
                    { data: 'products_count', width: '20%', class: 'align-middle text-center'},
                    { data: 'date_action', class: 'align-middle'},
                    { data: 'action', name: "action", class: 'align-middle', width: '15%',},
                ],
                columnDefs: [
                    { "orderable": false, "targets": [0,2,3,4] },
                    { "orderable": true, "targets": [1] }
                ],
                order: [[1, 'asc']]
            });
        }

        $(document).ready(function() {

            renderTableStore();

            elements.btn_create.click(function(e){
                e.preventDefault();
                let $this = $(this);
                let form = elements.form_create;
                createStore(form.attr('action'), form, $this);
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
