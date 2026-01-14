@extends('_blank')
@section('content')
@push('style')
<style>
    .dp-months {
        flex-wrap: nowrap !important;
        max-width: none !important;
    }
    .dp-months .dp-month{
        width: 290px !important;
        flex: 0 0 290px !important;
    }
    .dp-months .dp-month .dp-start, .dp-months .dp-month .dp-end{
        background-color: #ff898a !important;
        color: #c01415 !important;
    }
    @media (max-width: 767.98px) {
        .dp-months {
            flex-wrap: wrap !important;
        }

        .dp-month {
            width: 100% !important;
            flex: 0 0 100% !important;
        }
    }
    .css-icon-bsdatepicker{
        font-size: 1.5rem;
    }
    .swal-spinner {
        width: 36px;
        height: 36px;
        border: 4px solid #e5e7eb;
        border-top-color: #0d6efd;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
        margin: 0 auto;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }
</style>
@endpush
<!-- Start Content-->
<div class="container-fluid">

    <div class="row mt-3">
        <div class="col-xl-12 col-lg-12 order-lg-2 order-xl-1"">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div id="rangeDropdown" class="col-md-4">
                            <input type="hidden" name="start_date">
                            <input type="hidden" name="end_date">
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-outline-danger" id="clear-filter">Xoá lọc</button>
                            <button class="btn btn-outline-primary" id="btn-filter">Lọc</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card widget-inline">
                <div class="card-body p-0">
                    <div class="row g-0">
                        <div class="col-sm-6 col-lg-3">
                            <div class="card rounded-0 shadow-none m-0">
                                <div class="card-body text-center">
                                    <i class="ri-group-line text-muted font-24"></i>
                                    <h3><span id="total_customer">{{number_format($total_customer)}}</span></h3>
                                    <p class="text-muted font-15 mb-0" id="text_total_customer">Khách hàng mới trong tháng</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6 col-lg-3">
                            <div class="card rounded-0 shadow-none m-0 border-start border-light">
                                <div class="card-body text-center">
                                    <i class="ri-list-check-2 text-muted font-24"></i>
                                    <h3><span id="cnt_order">{{number_format($total_order->cnt_order)}}</span></h3>
                                    <p class="text-muted font-15 mb-0" id="text_cnt_order">Tổng đơn trong tháng</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6 col-lg-3">
                            <div class="card rounded-0 shadow-none m-0 border-start border-light">
                                <div class="card-body text-center">
                                    <i class="ri-group-line text-muted font-24"></i>
                                    <h3><span id="total_amount">{{number_format($total_order->total_amount)}}</span></h3>
                                    <p class="text-muted font-15 mb-0" id="text_total_amount">Tổng thu trong tháng</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6 col-lg-3">
                            <div class="card rounded-0 shadow-none m-0 border-start border-light">
                                <div class="card-body text-center">
                                    <i class="ri-line-chart-line text-muted font-24"></i>
                                    <h3>
                                        <span id="paid_amount">{{number_format($total_order->paid_amount)}}</span> 
                                    </h3>
                                    <p class="text-muted font-15 mb-0" id="text_paid_amount">Tổng nhận trong tháng</p>
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
                            <tbody id="tbody_order_latest">
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
                            <tbody id="tbody_customer_top_order">
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
@push('js')
<script src="{{asset('assets')}}/js/bs-date-picker-range-custom/dist/bs-datepicker.min.js"></script>
<script type="text/template" id="datepicker-presets">
    <div class="dp-presets d-flex flex-column gap-1">
        <button class="btn btn-outline-primary text-start" data-type="today">Hôm nay</button>
        <button class="btn btn-outline-primary text-start" data-type="yesterday">Hôm qua</button>
        <button class="btn btn-outline-primary text-start" data-type="7days">7 ngày trước</button>
        <button class="btn btn-outline-primary text-start" data-type="1month">1 tháng trước</button>
        <button class="btn btn-outline-primary text-start" data-type="2month">2 tháng trước</button>
    </div>
</script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script>
    const url = @json(route('admin.dashboard.get_data'));

    function parseYmd(dateStr) {
        const [y, m, d] = dateStr.split('-').map(Number);
        return new Date(y, m - 1, d);
    }

    function formatYmdToDmy(dateStr) {
        const [y, m, d] = dateStr.split('-');
        return `${d}/${m}/${y}`;
    }

    function render(search){
        $.ajax({
            url: url,
            data: {
                search: search,
            },
            type: "GET",
            beforeSend: function(){
                swal({
                    title: "Đang xử lý",
                    text: "Vui lòng chờ...",
                    buttons: false,
                    closeOnClickOutside: false,
                    closeOnEsc: false,
                    content: {
                        element: "div",
                        attributes: {
                            innerHTML: `
                                <div class="swal-spinner"></div>
                                <div style="margin-top:10px">Đang tải dữ liệu...</div>
                            `
                        }
                    }
                });
            },
            success: function(res){
                let params = new URLSearchParams(window.location.search);
                let dates = params.get("date").split(',');
                if(res.success){
                    let data = res.data;
                    $("#total_customer").text(data.total_customer ? Number(data.total_customer).toLocaleString('vi').replaceAll('.', ',') : 0);
                    $("#text_total_customer").text(`Khách hàng mới từ ${formatYmdToDmy(dates[0])} đến ${formatYmdToDmy(dates[1])}`);

                    $("#cnt_order").text(data.total_order.cnt_order ? Number(data.total_order.cnt_order).toLocaleString('vi').replaceAll('.', ',') : 0);
                    $("#text_cnt_order").text(`Tổng đơn từ ${formatYmdToDmy(dates[0])} đến ${formatYmdToDmy(dates[1])}`);

                    $("#total_amount").text(data.total_order.total_amount ? Number(data.total_order.total_amount).toLocaleString('vi').replaceAll('.', ',') : 0);
                    $("#text_total_amount").text(`Tổng thu từ ${formatYmdToDmy(dates[0])} đến ${formatYmdToDmy(dates[1])}`);

                    $("#paid_amount").text(data.total_order.paid_amount ? Number(data.total_order.paid_amount).toLocaleString('vi').replaceAll('.', ',') : 0);
                    $("#text_paid_amount").text(`Tổng nhận từ ${formatYmdToDmy(dates[0])} đến ${formatYmdToDmy(dates[1])}`);

                    let html_tbody_order_latest = "";
                    data.order_latest.map(function(item, index){
                        return html_tbody_order_latest += `
                            <tr>
                                <td>
                                    <h5 class="font-14 my-1 fw-normal">${item.code}</h5>
                                    <span class="text-muted font-13">${formatYmdToDmy(item.created_at)}</span>
                                </td>
                                <td>
                                    <h5 class="font-14 my-1 fw-normal">${Number(item.total_amount).toLocaleString('vi').replaceAll('.', ',')}</h5>
                                    <span class="text-muted font-13">Tổng tiền đơn</span>
                                </td>
                                <td>
                                    <h5 class="font-14 my-1 fw-normal">${Number(item.paid_amount).toLocaleString('vi').replaceAll('.', ',')}</h5>
                                    <span class="text-muted font-13">Tổng tiền nhận</span>
                                </td>
                                <td>
                                    <h5 class="font-14 my-1 fw-normal">${item.customer_full_name}</h5>
                                    <span class="text-muted font-13">Khách hàng</span>
                                </td>
                            </tr>`;
                    })

                    if(!html_tbody_order_latest) {
                        html_tbody_order_latest = `
                            <tr>
                                <td colspan="5" class="text-center">Không có đơn</td>
                            </tr>
                        `;
                    }

                    $("#tbody_order_latest").html(html_tbody_order_latest);
                }
                swal.close();
                
            },
            error: function(err){
                console.log(err);
                
            },
            complete: function(){
                
            }
        });
    }

    $(document).ready(function(){
        const $input_date_range = $('#rangeDropdown');

        $input_date_range.bsDatepicker({
            autoClose: true,
            format: 'dd/mm/yyyy',
            inline: false,
            locale: 'vi-VN',
            months: 2,
            placeholder: 'Ngày tạo',
            separator:' - ',
            icons: {
                prevYear:'ri-arrow-left-circle-fill icon-preYear css-icon-bsdatepicker',
                prev:'ri-arrow-left-s-line icon-prev css-icon-bsdatepicker',
                today:'ri-radio-button-line icon-today css-icon-bsdatepicker',
                next:'ri-arrow-right-s-line icon-next css-icon-bsdatepicker',
                nextYear:'ri-arrow-right-circle-fill icon-nextYear css-icon-bsdatepicker',
            }
        });

        $input_date_range.on('render.bs.datepicker',function(e) {
            const $picker = $('.bs-datepicker');
            
            if ($picker.find('.dp-presets').length) return;

            const html = $('#datepicker-presets').html();
            if($picker.find('.dp-months #datepicker-presets').length == 0) {
                $picker.find('.dp-months').prepend(html);
            }

        });


        $input_date_range.on('show.bs.datepicker', function () {
            const $picker = $('.bs-datepicker');
            
            if ($picker.find('.dp-presets').length) return;

            const html = $('#datepicker-presets').html();
            
            $picker.find('.dp-months').prepend(html);
        });

        $(document).on('click', '.dp-presets button', function () {
            const type = $(this).data('type');
            const today = new Date();

            let start, end;
            end = new Date(today);

            switch (type) {
                case 'today':
                    start = new Date(today);
                    break;

                case 'yesterday':
                    start = new Date(today);
                    start.setDate(start.getDate() - 1);
                    end = new Date(start);
                    break;

                case '7days':
                    start = new Date(today);
                    start.setDate(start.getDate() - 6);
                    break;

                case '1month':
                    start = new Date(today);
                    start.setMonth(start.getMonth() - 1);
                    break;
                case '2month':
                    start = new Date(today);
                    start.setMonth(start.getMonth() - 2);
                    break;
            }

            $input_date_range.bsDatepicker('setDate', [start, end]);
        });

        
        $("#btn-filter").click(function(e){
            e.preventDefault();
            let date = $input_date_range.bsDatepicker('val');
        
            let params = new URLSearchParams();

            if (date) params.set("date", date);

            const queryString = params.toString();
            const fullUrl = window.location.pathname + '?' + queryString;

            // Reload với query string
            window.history.pushState({}, '', fullUrl);
            render(queryString)

        });

        $("#clear-filter").click(function(e){
            const baseUrl = window.location.origin + window.location.pathname;
            window.history.pushState({}, "", baseUrl);
            window.location.href = baseUrl;
        })

        let params = new URLSearchParams(window.location.search);
        if (params.get("date")) {
            let dates = params.get("date").split(',');
            let start = parseYmd(dates[0]);
            let end   = parseYmd(dates[1]);
            $input_date_range.bsDatepicker('setDate', [start, end]);

            const queryString = params.toString();
            const fullUrl = window.location.pathname + '?' + queryString;

            // Reload với query string
            window.history.pushState({}, '', fullUrl);
            render(queryString)
        }
    })
</script>
@endpush