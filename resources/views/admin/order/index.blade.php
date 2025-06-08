@extends('_blank')
@section('content')
<div class="row mt-2">
    <div class="col-12">
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
                                <div class="align-middle text-center colorHeader">STT</div>
                            </th>
                            <th>
                                <div class="align-middle text-center colorHeader">Ngày hẹn giao</div>
                                <div class="align-middle text-center colorHeader">/ Ngày tạo đơn</div>
                                <div class="align-middle text-center colorHeader">/ Khách hàng</div>
                                <div class="align-middle text-center colorHeader">/ Tên nhận hàng</div>
                            </th>
                            <th>
                                <div class="align-middle text-center colorHeader">Mã đơn</div>
                                <div class="align-middle text-center colorHeader">/ Mã đơn VC</div>
                                <div class="align-middle text-center colorHeader">/ Loại giao hàng</div>
                                <div class="align-middle text-center colorHeader">/ Địa chỉ giao</div>
                            </th>
                            <th>
                                <div class="align-middle text-center colorHeader">Tổng sản phẩm</div>
                                <div class="align-middle text-center colorHeader">/ Tổng tiền</div>
                                <div class="align-middle text-center colorHeader">/ Tổng chiết khấu</div>
                            </th>
                            <th>
                                <div class="align-middle text-center colorHeader">Khách cần trả</div>
                                <div class="align-middle text-center colorHeader">/ Khác đã trả</div>
                                <div class="align-middle text-center colorHeader">/ Còn lại</div>
                            </th>
                            <th>
                                <div class="align-middle text-center colorHeader">Thuộc VC</div>
                                <div class="align-middle text-center colorHeader">/ Đối tượng VC</div>
                                <div class="align-middle text-center colorHeader">/ Phí giao hàng</div>
                                <div class="align-middle text-center colorHeader">/ Bên trả phí</div>
                                <div class="align-middle text-center colorHeader">/ Thu hộ COD</div>
                            </th>
                            <th>
                                <div class="align-middle text-center colorHeader">Trạng thái đơn hàng</div>
                                <div class="align-middle text-center colorHeader">/ Khối lượng đơn hàng</div>
                                <div class="align-middle text-center colorHeader">/ Ghi chú đơn hàng</div>
                                <div class="align-middle text-center colorHeader">/ Ghi chú vận chuyển</div>
                            </th>
                            <th>
                                <div class="align-middle text-center colorHeader">Ngày tạo</div>
                                <div class="align-middle text-center colorHeader">/ Ngày cập nhật</div>
                                <div class="align-middle text-center colorHeader">/ Người thao tác</div>
                            </th>
                            <th>
                                <div class="align-middle text-center colorHeader">Chức năng</div>
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
            ordering: false,
            columns: [
                { data: 'no', class: 'align-middle',  width: "2%"},
                { data: 'col_1', class: 'align-middle', width: "15%"},
                { data: 'col_2', class: 'align-middle', width: "15%"},
                { data: 'col_3', class: 'align-middle', width: "15%"},
                { data: 'col_4', class: 'align-middle', width: "15%"},
                { data: 'col_5', class: 'align-middle', width: "15%"},
                { data: 'col_6', class: 'align-middle', width: "15%"},
                { data: 'col_7', class: 'align-middle', width: "10%"},
                { data: 'col_8', class: 'align-middle', width: "5%"},
            ],
        });
    }

    $(document).ready(function(){
        renderTable();
    })
</script>
@endpush