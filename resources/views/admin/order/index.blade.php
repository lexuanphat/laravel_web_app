@extends('_blank')
@section('content')
<div class="row mt-2">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row g-2 align-items-center">
            
                <!-- Thanh tìm kiếm chính -->
                <div class="col-md-3">
                    <div class="input-group">
                    <span class="input-group-text"><i class="mdi mdi-magnify"></i></span>
                    <input type="text" id="searchInput" class="form-control" placeholder="Tìm kiếm theo mã đơn hàng, Tên khách hàng, Tên sản phẩm">
                    </div>
                </div>
            
                <!-- Dropdown Ngày tạo -->
                <div class="col">
                    <select id="dateSelect" class="form-control select2" data-toggle="select2">
                    <option value="">Ngày tạo</option>
                    <option value="today">Hôm nay</option>
                    <option value="7days">7 ngày qua</option>
                    <option value="30days">30 ngày qua</option>
                    </select>
                </div>
            
                <!-- Dropdown Trạng thái -->
                <div class="col">
                    <select id="statusSelect" class="form-control select2" data-toggle="select2">
                        <option value="">Trạng thái</option>
                        <option value="pending">Chờ xử lý</option>
                        <option value="confirmed">Đã xác nhận</option>
                        <option value="completed">Hoàn tất</option>
                    </select>
                </div>
            
                <!-- Dropdown Nhân viên -->
                <div class="col-md-2">
                    <select id="staffSelect" class="form-control select2" data-toggle="select2">
                        <option value="">Nhân viên phụ trách</option>
                        @foreach($staffs as $staff)
                        <option value="{{$staff->id}}">{{$staff->full_name}}</option>
                        @endforeach
                    </select>
                </div>
            
                <!-- Dropdown Tình trạng công nợ -->
                <div class="col-md-2">
                    <select id="status_return" class="select2 form-control select2-multiple"  multiple="multiple" data-toggle="select2" data-placeholder="Tình trạng công nợ">
                    <option value="return_full">Trả hết</option>
                    <option value="return_one_part">Trả 1 phần</option>
                    <option value="no_return">Chưa trả</option>
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
                                <div class="align-middle text-center colorHeader">Thanh toán</div>
                            </th>
                            <th>
                                <div class="align-middle text-center colorHeader">Khách phải trả</div>
                            </th>
                            
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
<script>
    const elements = {
        table_manage: $("#table_manage")
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
                { data: 'code_order', class: 'align-middle'},
                { data: 'create_date', class: 'align-middle'},
                { data: 'full_name', class: 'align-middle'},
                { data: 'status_transport', class: 'align-middle'},
                { data: 'status_payment', class: 'align-middle'},
                { data: 'customer_paid_total', class: 'align-middle'},
            ],
        });
    }

    $(document).ready(function(){
        let params = new URLSearchParams(window.location.search);
        if (params.get("search")) $.trim($("#searchInput").val(params.get("search")));
        if (params.get("date")) $("#dateSelect").val(params.get("date")).trigger("change");
        if (params.get("status")) $("#statusSelect").val(params.get("status")).trigger("change");
        if (params.get("staff")) $("#staffSelect").val(params.get("staff")).trigger("change");
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

            let params = new URLSearchParams();

            if (search) params.set("search", search);
            if (date) params.set("date", date);
            if (status) params.set("status", status);
            if (staff) params.set("staff", staff);
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
    })
</script>
@endpush