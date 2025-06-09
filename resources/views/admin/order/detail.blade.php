@extends('_blank')
@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7 col-md-10 col-sm-11">

        <div class="horizontal-steps mt-4 mb-4 pb-5">
            <div class="horizontal-steps-content">
                <div class="step-item">
                    <span data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-original-title="20/08/2018 07:24 PM">Order Placed</span>
                </div>
                <div class="step-item current">
                    <span data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-original-title="21/08/2018 11:32 AM">Packed</span>
                </div>
                <div class="step-item">
                    <span>Shipped</span>
                </div>
                <div class="step-item">
                    <span>Delivered</span>
                </div>
            </div>

            <div class="process-line" style="width: 33%;"></div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title mb-3">Các sản phẩm trong đơn <span class="text-decoration-underline">{{$data->code_order}}</span></h4>

                <div class="table-responsive">
                    <table class="table mb-0 table-bordered table-striped">
                        <thead class="table-light">
                            <tr>
                                <th class="align-middle text-center">Tên sản phẩm</th>
                                <th class="align-middle text-center">Giá tiền</th>
                                <th class="align-middle text-center">Số lượng</th>
                                <th class="align-middle text-center">Giảm giá</th>
                                <th class="align-middle text-center">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data->details as $detail)
                            <tr>
                                <td>
                                    <div class="text-center">{{$detail['product_name']}}</div>
                                </td>
                                <td>
                                    <div class="text-end">{{number_format($detail['price'], 0, ",", ".")}}</div>
                                </td>
                                <td>
                                    <div class="text-end">{{number_format($detail['quantity'], 0, ",", ".")}}</div>
                                </td>
                                <td>
                                    @if($detail['is_discount'] == 1)
                                    Giá trị: {{number_format($detail['discount'], 0, ",", ".")}}
                                    @else 
                                    Phần trăm: {{$detail['discount']}}%
                                    @endif
                                </td>
                                <td>
                                    <div class="text-end">{{number_format($detail['total_price'], 0, ",", ".")}}</div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- end table-responsive -->

            </div>
        </div>
    </div> <!-- end col -->

    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title mb-3">Tổng giá trị đơn hàng</h4>

                <div class="table-responsive">
                    <table class="table mb-0">
                        <tbody>
                            <tr>
                                <td>Tổng tiền:</td>
                                <td class="text-end">{{number_format($data->total_price, 0, ",", ".")}}</td>
                            </tr>
                            <tr>
                                <td>Chiết khấu:</td>
                                <td class="text-end">{{(int)$data->total_discount}}%</td>
                            </tr>
                            <tr>
                                <th>Khách phải trả : </th>
                                <th class="text-end">{{number_format($data->customer_paid_total, 0, ",", ".")}}</th>
                            </tr>
                            <tr>
                                <th>Khách đã trả :</th>
                                <th class="text-end">{{number_format($data->customer_has_paid_total, 0, ",", ".")}}</th>
                            </tr>
                            <tr>
                                <td>Còn lại:</td>
                                <td class="text-end">{{number_format((int)$data->customer_paid_total - (int)$data->customer_has_paid_total, 0 , ",", ".")}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- end table-responsive -->

            </div>
        </div>
    </div> <!-- end col -->
</div>
<div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title mb-3">Thông tin khách hàng</h4>

                <h5>{{$data->full_name}}</h5>

                <address class="mb-0 font-14 address-lg">
                    <abbr title="Địa chỉ khách hàng">ĐC:</abbr> {{$data->address}}<br>
                    <abbr title="Số điện thoại khách hàng">SDT:</abbr> <a href="tel:{{$data->phone}}" class="link">{{$data->phone}}</a> <br>
                </address>

            </div>
        </div>
    </div> <!-- end col -->

    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title mb-3">Thông tin chi tiết đơn hàng</h4>

                <ul class="list-unstyled mb-0">
                    <li>
                        <p class="mb-2"><span class="fw-bold me-2">Người bán:</span> {{$data->user_order_full_name}}</p>
                        <p class="mb-2"><span class="fw-bold me-2">Cửa hàng:</span> {{$data->store_name}}</p>
                        <p class="mb-2"><span class="fw-bold me-2">Nguồn:</span> {{$data->source}}</p>
                        <p class="mb-2"><span class="fw-bold me-2">Hẹn giao:</span> {{date("d/m/Y", strtotime($data->delivery_date))}}</p>
                        <p class="mb-2"><span class="fw-bold me-2">Ghi chú:</span> {{$data->note_order ? $data->note_order : "X"}}</p>
                    </li>
                </ul>

            </div>
        </div>
    </div> <!-- end col -->

    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title d-flex align-items-center gap-2"><i class="mdi mdi-truck-fast h2 text-muted"></i> Thông tin vận chuyển</h4>

                <div class="">
                    @if((int)$data->delivery_method === 3)
                    <h5><b>Nhận tại cửa hàng</b></h5>
                    @else 
                        
                        <p class="mb-1"><b>Loại VC :</b> {{$data->delivery_method_name}}</p>
                        <p class="mb-1"><b>Đối tượng VC :</b>
                            @if($data->delivery_method == 1)
                            {{$data->transport_full_name}}
                            @elseif($data->delivery_method == 2)
                            {{$data->partner_transport_type_name}} -  {{$data->transport_full_name}}
                            @endif
                        </p>
                        <p class="mb-1"><b>Mã VC :</b> {{$data->code_transport ? $data->code_transport : "X"}}</p>
                        <p class="mb-0"><b>Thu hộ COD :</b> {{number_format($data->cod, 0, ",", ".")}}</p>
                        <p class="mb-0"><b>Ghi chú VC :</b> {{$data->note_transport}}</p>
                    @endif
                </div>
            </div>
        </div>
    </div> <!-- end col -->
</div>
@endsection