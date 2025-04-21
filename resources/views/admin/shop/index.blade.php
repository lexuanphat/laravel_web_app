@extends('_blank')
@section('content')
    <div class="bg-white p-2 my-2">
        <div class="button-actions">
            <button type="button" class="btn btn-primary mb-2" data-toggle="modal" data-target="#modal-create-store"><i class="mdi mdi-plus-circle"></i> Thêm mới cửa hàng</button>
        </div>
        <table id="table_manage_store" data-action="{{route('admin.shop.get_data')}}" class="table dt-responsive w-100">
            <thead>
                <tr>
                    <th>
                        <div class="text-uppercase align-middle">STT</div>
                    </th>
                    <th>
                        <div class="text-uppercase align-middle">Tên</div>
                        <div class="text-uppercase align-middle">/ Số điện thoại</div>
                    </th>
                    <th>
                        <div class="text-uppercase align-middle">Địa chỉ</div>
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
            @include('admin.shop.modals.create')
            @include('admin.shop.modals.edit')
            @include('admin._partials.modal-noti.not-found')
        </div>
    </div>
@endsection
@push('js')
    <script>
        const elements = {
            btn_create_store: $("#btn_create_store"),
            btn_update_store: $("#btn_update_store"),
            form_create_store: $("#form_create_store"),
            form_edit_store: $("#form_edit_store"),
            modal_create_store: $("#modal-create-store"),
            modal_edit_store: $("#modal-edit-store"),
            table_manage_store: $("#table_manage_store"),
            action_update: @json(route('admin.shop.update', ['id' => ':id'])),
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
                            elements.modal_edit_store.find('#'+key).val(value);
                        })
                        let form_action = elements.form_edit_store.attr('action');
                        elements.form_edit_store.attr('action', elements.action_update.replace(':id', id));
                        elements.modal_edit_store.modal('show');
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
                        elements.table_manage_store.DataTable().destroy();
                        elements.table_manage_store.find('tbody').empty();
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
                        elements.modal_create_store.modal('hide')
                        createToast('success', response.message);
                        elements.table_manage_store.DataTable().destroy();
                        elements.table_manage_store.find('tbody').empty();
                        renderTableStore();
                    }
                    
                },
                error: function(err){
                    let response_err = err.responseJSON;
                    if(response_err) {
                        $.each(response_err.errors, function(key, item){
                            elements.modal_create_store.find("#"+key).addClass('is-invalid');
                            elements.modal_create_store.find("#"+key).next().text(item[0]);
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
                        elements.modal_edit_store.modal('hide')
                        createToast('success', response.message);
                        elements.table_manage_store.DataTable().destroy();
                        elements.table_manage_store.find('tbody').empty();
                        renderTableStore();
                        
                    }
                    
                },
                error: function(err){
                    let response_err = err.responseJSON;
                    if(response_err) {
                        $.each(response_err.errors, function(key, item){
                            elements.modal_edit_store.find("#"+key).addClass('is-invalid');
                            elements.modal_edit_store.find("#"+key).next().text(item[0]);
                        })

                        if(response_err.data?.length === 0){
                            elements.modal_edit_store.modal('hide')
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
            elements.table_manage_store.DataTable({
                language: {
                    // paginate: {
                    //     previous: "<i class='mdi mdi-chevron-left'>",
                    //     next: "<i class='mdi mdi-chevron-right'>"
                    // },
                    processing: '<div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div>',
                },
                ajax: {
                    url: elements.table_manage_store.data('action'),
                    type: "GET",
                },
                searching: false,
                stateSave: true,
                processing: true,
                serverSide: true,
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', class: 'align-middle'},
                    { data: 'name', width: '25%', name: "abc", class: 'align-middle'},
                    { data: 'address', width: '30%', class: 'align-middle'},
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

            elements.btn_create_store.click(function(e){
                e.preventDefault();
                let $this = $(this);
                let form = elements.form_create_store;
                createStore(form.attr('action'), form, $this);
            });

            elements.modal_create_store.on('hidden.bs.modal', function (e) {
                elements.form_create_store.find('.form-control').removeClass('is-invalid')
                elements.form_create_store.find('.invalid-feedback').empty();
                elements.form_create_store[0].reset();
            });

            elements.btn_update_store.click(function(e){
                e.preventDefault();
                let $this = $(this);
                let form = elements.form_edit_store;
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
            let action = @json(route("admin.shop.delete", ['id' => ':id']));
            action = action.replace(':id', record);
            deleteRecord(action);
        })
    </script>
@endpush
