@extends('_blank')
@section('content')
<!-- Start Content-->
<div class="container-fluid">

    <div class="row mt-3">
        <div class="col-12">
            <div class="card widget-inline">
                <div class="card-body p-0">
                    <div class="row g-0">
                        <div class="col-sm-6 col-lg-3">
                            <div class="card rounded-0 shadow-none m-0">
                                <div class="card-body text-center">
                                    <i class="ri-group-line text-muted font-24"></i>
                                    <h3><span>{{number_format($total_customer)}}</span></h3>
                                    <p class="text-muted font-15 mb-0">Khách hàng mới trong tháng</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6 col-lg-3">
                            <div class="card rounded-0 shadow-none m-0 border-start border-light">
                                <div class="card-body text-center">
                                    <i class="ri-list-check-2 text-muted font-24"></i>
                                    <h3><span>{{number_format($total_order->cnt_order)}}</span></h3>
                                    <p class="text-muted font-15 mb-0">Tổng đơn trong tháng</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6 col-lg-3">
                            <div class="card rounded-0 shadow-none m-0 border-start border-light">
                                <div class="card-body text-center">
                                    <i class="ri-group-line text-muted font-24"></i>
                                    <h3><span>{{number_format($total_order->total_amount)}}</span></h3>
                                    <p class="text-muted font-15 mb-0">Tổng thu trong tháng</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6 col-lg-3">
                            <div class="card rounded-0 shadow-none m-0 border-start border-light">
                                <div class="card-body text-center">
                                    <i class="ri-line-chart-line text-muted font-24"></i>
                                    <h3>
                                        <span>{{number_format($total_order->paid_amount)}}</span> 
                                    </h3>
                                    <p class="text-muted font-15 mb-0">Tổng nhận trong tháng</p>
                                </div>
                            </div>
                        </div>

                    </div> <!-- end row -->
                </div>
            </div> <!-- end card-box-->
        </div> <!-- end col-->
    </div>

    <div class="row">
        <div class="col-xl-12 col-lg-12 order-lg-2 order-xl-1">
            <div class="card">
                <div class="d-flex card-header justify-content-between align-items-center">
                    <h4 class="header-title">Đơn hàng mới nhất</h4>
                </div>

                <div class="card-body pt-0">
                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap table-hover mb-0">
                            <tbody>
                                @forelse($order_latest as $item)
                                <tr>
                                    <td>
                                        <h5 class="font-14 my-1 fw-normal">{{$item->code}}</h5>
                                        <span class="text-muted font-13">{{date("d/m/Y H:i", strtotime($item->created_at))}}</span>
                                    </td>
                                    <td>
                                        <h5 class="font-14 my-1 fw-normal">{{number_format($item->total_amount)}}</h5>
                                        <span class="text-muted font-13">Tổng tiền đơn</span>
                                    </td>
                                    <td>
                                        <h5 class="font-14 my-1 fw-normal">{{number_format($item->paid_amount)}}</h5>
                                        <span class="text-muted font-13">Tổng tiền nhận</span>
                                    </td>
                                    <td>
                                        <h5 class="font-14 my-1 fw-normal">{{$item->customer_full_name}}</h5>
                                        <span class="text-muted font-13">Khách hàng</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">Chưa có đơn nào trong hôm nay</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div> <!-- end table-responsive-->
                </div> <!-- end card-body-->
            </div> <!-- end card-->
        </div> <!-- end col-->
    </div>

    <div class="row">
        <div class="col-xl-12 col-lg-12 order-lg-2 order-xl-1">
            <div class="card">
                <div class="d-flex card-header justify-content-between align-items-center">
                    <h4 class="header-title">Khách hàng VIP</h4>
                </div>

                <div class="card-body pt-0">
                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap table-hover mb-0">
                            <tbody>
                                @php $stt = 1; @endphp
                                @forelse($customer_top_order as $item)
                                <tr>
                                    <td>
                                        <h5 class="font-14 my-1 fw-normal">
                                            <span class="badge badge-outline-warning">TOP {{$stt}}</span>
                                        </h5>
                                    </td>
                                    <td>
                                        <h5 class="font-14 my-1 fw-normal">{{$item->customer_full_name}}</h5>
                                    </td>
                                    <td class="">
                                        <h5 class="font-14 my-1 fw-normal">{{number_format($item->sum_paid_amount)}}</h5>
                                        <span class="text-muted font-13">Tổng tiền khách hàng đã chi</span>
                                    </td>
                                    <td>
                                        <h5 class="font-14 my-1 fw-normal">{{number_format($item->cnt_order)}}</h5>
                                        <span class="text-muted font-13">Tổng đơn khách đã đặt</span>
                                    </td>
                                </tr>
                                @php $stt++; @endphp
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">Chưa có khách đặt đơn</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div> <!-- end table-responsive-->
                </div> <!-- end card-body-->
            </div> <!-- end card-->
        </div> <!-- end col-->
    </div>

</div>
<!-- container -->
@endsection