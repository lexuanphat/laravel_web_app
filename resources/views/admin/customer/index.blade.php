@php
    $model_create_id = "modal_create_customer";
    $form_create_id = "form_create_customer";
    $btn_submit_create_id = "btn_create_customer";
    $action_radio_create = "create";

    $model_edit_id = "modal_edit_customer";
    $form_edit_id = "form_edit_customer";
    $btn_submit_edit_id = "btn_update_customer";
    $action_radio_update = "update";
@endphp
@extends('_blank')
@section('content')
<div class="bg-white p-2 my-2">
    <div class="button-actions">
        <button type="button" class="btn btn-primary mb-2" data-toggle="modal" data-target="#{{$model_create_id}}"><i class="mdi mdi-plus-circle"></i> Thêm mới khách hàng</button>
    </div>
    <table id="table_manage_customer" data-action="{{route('admin.customer.get_data')}}" class="table dt-responsive w-100">
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
                    <div class="text-uppercase align-middle">/ Sinh nhật</div>
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
        @include('admin.customer.modals.create', [
            'model_id' => $model_create_id,
            'form_id' => $form_create_id,
            'btn_submit_id' => $btn_submit_create_id,
            'action_radio' => $action_radio_create,
        ])
        @include('admin.customer.modals.edit', [
            'model_id' => $model_edit_id,
            'form_id' => $form_edit_id,
            'btn_submit_id' => $btn_submit_edit_id,
            'action_radio' => $action_radio_update,
        ])
        @include('admin._partials.modal-noti.not-found')
    </div>
</div>
@endsection
@push('js')
<script>
    const elements = {
        btn_create_customer: $("#btn_create_customer"),
        btn_update_customer: $("#btn_update_customer"),
        form_create_customer: $("#form_create_customer"),
        form_edit_customer: $("#form_edit_customer"),
        modal_create_customer: $("#modal_create_customer"),
        modal_edit_customer: $("#modal_edit_customer"),
        table_manage_customer: $("#table_manage_customer"),
        action_update: @json(route('admin.customer.update', ['id' => ':id'])),
        action_delete: @json(route("admin.customer.delete", ['id' => ':id'])),
    }

    function renderTableCustomer(){
        elements.table_manage_customer.DataTable({
            language: {
                // paginate: {
                //     previous: "<i class='mdi mdi-chevron-left'>",
                //     next: "<i class='mdi mdi-chevron-right'>"
                // },
                processing: '<div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div>',
            },
            ajax: {
                url: elements.table_manage_customer.data('action'),
                type: "GET",
            },
            searching: false,
            stateSave: true,
            processing: true,
            serverSide: true,
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', class: 'align-middle'},
                { data: 'full_name', width: '25%', class: 'align-middle'},
                { data: 'info', width: '30%', class: 'align-middle'},
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

    function createCustomer(action, form, button_click){
        $.ajax({
            url: action,
            type: "POST",
            data: form.serialize(),
            beforeSend: function(){
                form.find('.form-control').removeClass('is-invalid');
                form.find('.invalid-feedback').empty();

                button_click.prop("disabled", true);
                button_click.find('#loading').show();
                button_click.find('.add-new').hide();
            },
            success: function(res){
               if(res.success) {
                    elements.modal_create_customer.modal('hide')
                    createToast('success', res.message);
                    elements.table_manage_customer.DataTable().destroy();
                    elements.table_manage_customer.find('tbody').empty();
                    renderTableCustomer();
               }
                
            },
            error: function(err){
                let response_err = err.responseJSON;
                if(response_err) {
                    $.each(response_err.errors, function(key, item){
                        elements.modal_create_customer.find("#"+key).addClass('is-invalid');
                        elements.modal_create_customer.find("#"+key).next().text(item[0]);

                        if(key === 'gender') {
                            elements.modal_create_customer.find('#male').parent().parent().addClass('is-invalid')
                            elements.modal_create_customer.find('#male').parent().parent().next().text(item[0]);
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
                        elements.modal_edit_customer.find('#'+key).val(value);
                        if(key === "gender") {
                            elements.modal_edit_customer.find('input:radio[name="gender"]').prop('checked', false).filter(`[value="${value}"]`).attr("checked", true).trigger("click");
                        }
                    })
                    let form_action = elements.form_edit_customer.attr('action');
                    elements.form_edit_customer.attr('action', elements.action_update.replace(':id', id));
                    elements.modal_edit_customer.modal('show');
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
                    elements.table_manage_customer.DataTable().destroy();
                    elements.table_manage_customer.find('tbody').empty();
                    renderTableCustomer();
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

    function updateCustomer(url, form, button_click){
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
                    elements.modal_edit_customer.modal('hide')
                    createToast('success', response.message);
                    elements.table_manage_customer.DataTable().destroy();
                    elements.table_manage_customer.find('tbody').empty();
                    renderTableCustomer();
                }
                
            },
            error: function(err){
                let response_err = err.responseJSON;

                if(response_err) {
                    $.each(response_err.errors, function(key, item){
                        elements.modal_edit_customer.find("#"+key).addClass('is-invalid');
                        elements.modal_edit_customer.find("#"+key).next().text(item[0]);
                    })

                    if(response_err.data?.length === 0){
                        elements.modal_edit_customer.modal('hide')
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

    $(document).ready(function() {

        renderTableCustomer();

        elements.btn_create_customer.click(function(e){
            e.preventDefault();
            let $this = $(this);
            let form = elements.form_create_customer;
            createCustomer(form.attr('action'), form, $this);
        });

        elements.modal_create_customer.on('hidden.bs.modal', function (e) {
            elements.form_create_customer.find('.form-control').removeClass('is-invalid')
            elements.form_create_customer.find('.invalid-feedback').empty();
            elements.form_create_customer[0].reset();
        });

        elements.btn_update_customer.click(function(e){
            e.preventDefault();
            let $this = $(this);
            let form = elements.form_edit_customer;
            let action = form.attr('action');
            updateCustomer(action, form, $this);
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