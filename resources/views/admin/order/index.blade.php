@extends('_blank')
@push('style')
<style>
    .table .table-user img{
        height: 20px !important;
        width: auto !important;
    }

    #table_manage tr.cancle {
        background: #ff898a;
    }
</style>
@endpush
@section('content')
@include('admin.order.modal-status')
<div class="row mt-2">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row g-2 align-items-center">
            
               <div class="col-md-10 row row-gap-2">
                    <!-- Thanh tìm kiếm chính -->
                    <div class='col-md-4'>
                        <div class="input-group">
                        <span class="input-group-text"><i class="mdi mdi-magnify"></i></span>
                        <input type="text" id="searchInput" class="form-control" placeholder="Tìm kiếm theo mã đơn hàng, Tên khách hàng, Tên sản phẩm">
                        </div>
                    </div>
                
                    <!-- Dropdown Ngày tạo -->
                    <div class='col-md-4'>
                        <select id="dateSelect" class="form-control select2" data-toggle="select2">
                        <option value="">Ngày tạo</option>
                        <option value="today">Hôm nay</option>
                        <option value="7days">7 ngày qua</option>
                        <option value="30days">30 ngày qua</option>
                        <option value="1year">1 năm</option>
                        <option value="2year">2 năm</option>
                        </select>
                    </div>
                
                    <!-- Dropdown Trạng thái -->
                    <div class='col-md-4'>
                        <select id="statusSelect" class="form-control select2" data-toggle="select2">
                            <option value="">Trạng thái</option>
                            <option value="pending">Chờ xử lý</option>
                            <option value="confirmed">Đã xác nhận</option>
                            <option value="completed">Hoàn tất</option>
                        </select>
                    </div>
                
                    <!-- Dropdown Nhân viên -->
                    <div class='col-md-4'>
                        <select id="staffSelect" class="form-control select2" data-toggle="select2">
                            <option value="">Nhân viên phụ trách</option>
                            @foreach($staffs as $staff)
                            <option value="{{$staff->id}}">{{$staff->full_name}}</option>
                            @endforeach
                        </select>
                    </div>
                
                    <!-- Dropdown Tình trạng công nợ -->
                    <div class='col-md-4'>
                        <select id="status_return" class="select2 form-control select2-multiple"  multiple="multiple" data-toggle="select2" data-placeholder="Tình trạng công nợ">
                        <option value="return_full">Trả hết</option>
                        <option value="return_one_part">Trả 1 phần</option>
                        <option value="no_return">Chưa trả</option>
                        </select>
                    </div>

                    {{-- Trạng thái đơn hàng --}}
                    <div class='col-md-4'>
                        <select id="status_order" class="form-control select2" data-toggle="select2">
                            <option value="">Trạng thái đơn hàng</option>
                            <option value="completed">Hoàn tất đơn</option>
                            <option value="returned">Bị trả hàng</option>
                            <option value="-1">Tất cả</option>
                        </select>
                    </div>
               </div>
            
                <!-- Nút lưu -->
                <div class="col-md-2">
                    <button class="btn btn-outline-danger" id="clear-filter">Xoá lọc</button>
                    <button class="btn btn-outline-primary" id="btn-filter">Lọc</button>
                </div>
            
                </div>
            </div>
        </div>
        <div class="text-end mb-3">
            <button type="button" class="btn btn-primary" id="btn_show_modal_change_status_order">
                Chuyển trạng thái đơn
            </button>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="row mb-2 d-none">
                    <div class="col-xl-8">
                        <form class="row gy-2 gx-2 align-items-center justify-content-xl-start justify-content-between">
                            <div class="col-auto">
                                <label for="inputPassword2" class="visually-hidden">Search</label>
                                <input type="search" class="form-control" id="inputPassword2" placeholder="Search...">
                            </div>
                            <div class="col-auto">
                                <div class="d-flex align-items-center">
                                    <label for="status-select" class="me-2">Status</label>
                                    <select class="form-select" id="status-select">
                                        <option selected="">Choose...</option>
                                        <option value="1">Paid</option>
                                        <option value="2">Awaiting Authorization</option>
                                        <option value="3">Payment failed</option>
                                        <option value="4">Cash On Delivery</option>
                                        <option value="5">Fulfilled</option>
                                        <option value="6">Unfulfilled</option>
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-xl-4 d-none">
                        <div class="text-xl-end mt-xl-0 mt-2">
                            <button type="button" class="btn btn-danger mb-2 me-2"><i class="mdi mdi-basket me-1"></i> Add New Order</button>
                            <button type="button" class="btn btn-light mb-2">Export</button>
                        </div>
                    </div><!-- end col-->
                </div>
                <table id="table_manage" data-action="{{route('admin.order.get_data')}}" class="table dt-responsive w-100 table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>
                                <div class="align-middle text-center colorHeader">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="check-input-all">
                                        <label class="form-check-label" for="check-input-all">&nbsp;</label>
                                    </div>
                                </div>
                            </th>
                            <th>
                                <div class="align-middle text-center colorHeader">Mã đơn hàng</div>
                            </th>
                            <th>
                                <div class="align-middle text-center colorHeader">Ngày tạo đơn</div>
                            </th>
                            <th>
                                <div class="align-middle text-center colorHeader">Tên khách hàng</div>
                            </th>
                            <th>
                                <div class="align-middle text-center colorHeader">Trạng thái đơn hàng</div>
                            </th>
                            <th>
                                <div class="align-middle text-center colorHeader">Đối tượng vận chuyển</div>
                            </th>
                            <th>
                                <div class="align-middle text-center colorHeader">Khách phải trả</div>
                            </th>
                            <th>
                                <div class="align-middle text-center colorHeader">Người chịu trách ĐH</div>
                            </th>
                            {{-- <th>
                                <div class="align-middle text-center colorHeader">Chức năng</div>
                            </th> --}}
                        </tr>
                    </thead>
        
        
                    <tbody></tbody>
                </table>
            </div> <!-- end card-body-->
        </div> <!-- end card-->
    </div> <!-- end col -->
</div>
@endsection
@push('js')
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

<script>
    const elements = {
        table_manage: $("#table_manage"),
        route_delete: @json(route('admin.order.delete', ['id' => ':id']))
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
                    search: search,
                },
            },
            searching: false,
            stateSave: true,
            processing: true,
            serverSide: true,
            ordering: false,
            columns: [
                { data: 'checkbox', class: 'align-middle'},
                { data: 'code', class: 'align-middle'},
                { data: 'created_at', class: 'align-middle'},
                { data: 'main_customer_full_name', class: 'align-middle'},
                { data: 'status', class: 'align-middle'},
                { data: 'object_partner', class: 'align-middle'},
                { data: 'total_amount', class: 'align-middle'},
                { data: 'user_order', class: 'align-middle'},
                // { data: 'function', class: 'align-middle'},
            ],
            createdRow: function (row, data, dataIndex){
                if (data.status_raw == 4) {
                    $(row).addClass('cancle');
                }
            }
        });
    }

    function cancelOrderPartner(store_id, order_id, key_partner){
        swal({
            title: "Bạn có chắc huỷ đơn?",
            text: "Ấn xác nhận để huy đơn hàng này",
            icon: "warning",
            buttons: ['Đóng', 'Xác nhận'],
            dangerMode: true,
        })
        .then((willDelete) => {
        if (willDelete) {
            $.ajax({
                url: @json(route('admin.order.cancel_order_partner')),
                type: "POST",
                data: {
                    _token: $("[name='_token']").val(),
                    store_id: store_id,
                    order_id: order_id,
                    key_partner: key_partner,
                },
                beforeSend: function(){},
                success: function(response){
                    if (response.success) {
                        createToast('success', response.message);
                        elements.table_manage.DataTable().destroy();
                        renderTable(window.location.search);
                    }
                    
                },
                error: function(errors){
                    console.log(errors);
                    
                    let err = errors.responseJSON.message;
                    if(err) {
                        swal({
                            icon: "warning",
                            title: "Thông báo",
                            button: "Đóng",
                            text: err,
                        })
                    } else {
                        swal({
                            icon: "error",
                            title: "Có lỗi",
                            text: "Liên hệ kỹ thuật",
                            button: "Đóng",
                        })
                    }
                },
                
            });
        } 
        });
    }

    $(document).ready(function(){
        let params = new URLSearchParams(window.location.search);
        if (params.get("search")) $.trim($("#searchInput").val(params.get("search")));
        if (params.get("date")) $("#dateSelect").val(params.get("date")).trigger("change");
        if (params.get("status")) $("#statusSelect").val(params.get("status")).trigger("change");
        if (params.get("staff")) $("#staffSelect").val(params.get("staff")).trigger("change");
        if (params.get("status_order")) $("#status_order").val(params.get("status_order")).trigger("change");
        if (params.get("status_return")) {
            $("#status_return").val(params.get("status_return").split(",")).trigger('change');
        }
        
        renderTable(window.location.search);

        $("#btn-filter").click(function(e){
            e.preventDefault();

            let search = $.trim($("#searchInput").val());
            let date = $("#dateSelect").val();
            let status = $("#statusSelect").val();
            let staff = $("#staffSelect").val();
            let status_return = $("#status_return").val();
            let status_order = $("#status_order").val();

            let params = new URLSearchParams();

            if (search) params.set("search", search);
            if (date) params.set("date", date);
            if (status) params.set("status", status);
            if (staff) params.set("staff", staff);
            if (status_order) params.set("status_order", status_order);
            if (status_return && status_return.length > 0) params.set("status_return", status_return.join(","));

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

        $("#btn_show_modal_change_status_order").click(function(e){
            e.preventDefault();
            let input_checked = $(".check-input-item:not(:disabled):checked")
            if(input_checked.length === 0) {
                alert("Vui lòng chọn đơn hàng cần chuyển");
                return;
            }

            $("#modal_change_status_order").modal('show');
        });

        $("#btn_change_status_order").click(function(e){
            e.preventDefault();
            let $this = $(this);
            let input_checked = $(".check-input-item:not(:disabled):checked")
            if(input_checked.length === 0) {
                alert("Vui lòng chọn đơn hàng cần chuyển");
                return;
            }

            let ids = [];

            $(".check-input-item:not(:disabled):checked").each(function(index, item){
                ids.push(item.value);
            });

            $.ajax({
                url: @json(route("admin.order.change_status")),
                type: "POST",
                data: {
                    _token: $("[name='_token']").val(),
                    ids: ids,
                    status_code: $("#status_code").val(),
                    note_logs: $("#note_logs").val(),
                },
                beforeSend: function(){
                    $('#form_action').find('.form-control').removeClass('is-invalid');
                    $('#form_action').find('.invalid-feedback').empty();

                    $this.prop("disabled", true);
                    $this.find('#loading').show();
                    $this.find('.add-new').hide();
                },
                success: function(response){
                    if(response.success) {
                        $("#modal_change_status_order").modal('hide')
                        createToast('success', response.message);
                        elements.table_manage.DataTable().clear().destroy();
                        renderTable(window.location.search);
                    }
                },
                error: function(errors){
                    let response_err = errors.responseJSON;
                    if(response_err) {
                        $.each(response_err.errors, function(key, item){
                             if(key === 'status_code'){
                                $('#form_action').find("#"+key).addClass('is-invalid');
                                $('#form_action').find("#"+key).next().next().text(item[0]);
                            } else {
                                $('#form_action').find("#"+key).addClass('is-invalid');
                                $('#form_action').find("#"+key).next().text(item[0]);
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
        })
    })

    $(document).on("click", ".remove-record", async function(){
        let result = confirm("Có chắc muốn xoá dữ liệu?");
        if(!result) {
            return;
        }

        let $this = $(this);
        let record = $this.data('record');
        let action = elements.route_delete;
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
                    $("#btn-filter").trigger('click');
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

    $("#modal_change_status_order").on('hidden.bs.modal', function (e) {
       $("#modal_change_status_order").find('.form-control').removeClass('is-invalid')
       $("#modal_change_status_order").find('.invalid-feedback').empty();
       $("#modal_change_status_order").find('#form_action')[0].reset();

       $("#modal_change_status_order").find('#status_code').val('').trigger('change');
    });

    $(document).on('click', '#check-input-all', function(e){
        let $this = $(this);
        let checked = $this.prop('checked');
        if(checked) {
            $(".check-input-item:not(:disabled):not(:checked)").prop('checked', true);
        } else {
            $(".check-input-item:not(:disabled)").prop('checked', false);
        }
    })
</script>
@endpush