@extends('_blank')
@section('content')
<div class="row">
    <div class="col-left col">
        <a href="{{route('admin.order')}}" class="btn btn-primary">Quay lại</a>
    </div>
    <div class="col-right col">
        <div class="d-flex justify-content-end gap-2">
            <button class='btn btn-danger remove-record'>Xoá đơn hàng</button>

            @php
            if(in_array($data->shipping_partner_id, [-1, -2])) {
                $is_disabled = "";
                $text = "Huỷ đơn hàng";
                $key_partner = 'GHN';
                if($data->shipping_partner_id == -2){
                    $key_partner = 'GHTK';
                }
                if($data->status === 4) {
                    $is_disabled = "disabled";
                }
                if($data->status === 9) {
                    $is_disabled = "disabled";
                    $text = "Đã gửi yêu cầu";
                }
                echo '<button class="btn btn-primary" '.$is_disabled.' onclick="cancelOrderPartner('.$data->store_id.', '.$data->id.', `'.$key_partner.'`)">'.$text.'</button>';
            }
            @endphp
        </div>
    </div>
</div>
<div class="row justify-content-center">
    <div class="col-lg-7 col-md-10 col-sm-11">

        <div class="horizontal-steps mt-4 mb-4 pb-5">
            <div class="horizontal-steps-content">
                {{-- <div class="step-item">
                    <span data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-original-title="20/08/2018 07:24 PM">Order Placed</span>
                </div>
                <div class="step-item current">
                    <span data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-original-title="21/08/2018 11:32 AM">Packed</span>
                </div> --}}
                @foreach($data->logs as $logs)
                <div class="step-item">
                    <span data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-original-title="{{date("d/m/Y H:i:s", strtotime($logs->status_time))}}">{{$logs->status_text}}</span>
                </div>
                @endforeach
            </div>

            <div class="process-line" style="width: {{ round(100/count($data->logs)) * count($data->logs) }}%;"></div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title mb-3">Các sản phẩm trong đơn <span class="text-decoration-underline">{{$data->code}}</span></h4>

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
                            @foreach($data->items as $detail)
                            <tr>
                                <td>
                                    <div class="text-center">{{$detail->product_name}}</div>
                                </td>
                                <td>
                                    <div class="text-end">{{number_format($detail->product_price, 0, ",", ".")}}</div>
                                </td>
                                <td>
                                    <div class="text-end">{{number_format($detail->product_quantity, 0, ",", ".")}}</div>
                                </td>
                                <td>
                                    @if($detail->is_discount == 1)
                                    Giá trị: {{number_format($detail->product_discount, 0, ",", ".")}}
                                    @else 
                                    Phần trăm: {{$detail->product_discount}}%
                                    @endif
                                </td>
                                <td>
                                    <div class="text-end">{{number_format($detail->product_total_discount, 0, ",", ".")}}</div>
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
                                <td>Áp dụng phiếu giảm:</td>
                                <td class="text-end">{{number_format($data->total_apply_coupon, 0, ",", ".")}}</td>
                            </tr>
                            <tr>
                                <td>Chiết khấu:</td>
                                <td class="text-end">{{(int)$data->total_discount}}%</td>
                            </tr>
                            <tr>
                                <th>Khách phải trả : </th>
                                <th class="text-end">{{number_format($data->total_amount, 0, ",", ".")}}</th>
                            </tr>
                            <tr>
                                <th>Khách đã trả :</th>
                                <th class="text-end">{{number_format($data->paid_amount, 0, ",", ".")}}</th>
                            </tr>
                            <tr>
                                <td>Còn lại:</td>
                                <td class="text-end">{{number_format((int)$data->total_amount - (int)$data->paid_amount, 0 , ",", ".")}}</td>
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
                <h5>{{$data->customer_full_name}}</h5>
                <address class="mb-0 font-14 address-lg">
                    <abbr title="Địa chỉ khách hàng">ĐC:</abbr> {{$data->customer_address}}, {{$data->customer_ward_name}}, {{$data->customer_province_name}}<br>
                    <abbr title="Số điện thoại khách hàng">SDT:</abbr> <a href="tel:{{$data->customer_phone}}" class="link">{{$data->customer_phone}}</a> <br>
                </address>
                <hr>
                <ul class="list-unstyled mb-0">
                    <li>
                        <span class="fw-bold me-2">Người đặt hàng:</span> {{isset($customers[$data->user_order]) ? $customers[$data->user_order]->full_name : ""}}
                    </li>
                    <li>
                        <span class="fw-bold me-2">Người nhận hàng:</span> {{isset($customers[$data->user_consignee]) ? $customers[$data->user_consignee]->full_name : ""}}
                    </li>
                    <li>
                        <span class="fw-bold me-2">Người trả tiến:</span> {{isset($customers[$data->user_payer]) ? $customers[$data->user_payer]->full_name : ""}}
                    </li>
                </ul>
            </div>
        </div>
    </div> <!-- end col -->

    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title mb-3">Thông tin chi tiết đơn hàng</h4>

                <ul class="list-unstyled mb-0">
                    <li>
                        <p class="mb-2"><span class="fw-bold me-2">Người lên đơn:</span> {{$data->user_full_name}}</p>
                        <p class="mb-2"><span class="fw-bold me-2">Cửa hàng:</span> {{$data->store_id}}</p>
                        <p class="mb-2"><span class="fw-bold me-2">Nguồn:</span> {{$data->source}}</p>
                        <p class="mb-2"><span class="fw-bold me-2">Hẹn giao:</span> {{date("d/m/Y", strtotime($data->created_at))}}</p>
                        <p class="mb-2"><span class="fw-bold me-2">Ghi chú:</span> {{$data->note ? $data->note : "X"}}</p>
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
                    <p class="mb-1"><b>Đối tượng VC : {{$data->transport_partner}}</p>
                    <p class="mb-1"><b>Mã VC :</b> {{$data->tracking_number ? $data->tracking_number : "X"}}</p>
                    <p class="mb-1"><b>Thu hộ COD :</b> {{number_format($data->cod, 0, ",", ".")}}</p>
                    <p class="mb-1"><b>Ghi chú VC :</b> {{$data->note_transport}}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('js')
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script>
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

    $(document).on("click", ".remove-record", async function(){
        let result = confirm("Có chắc muốn xoá dữ liệu?");
        if(!result) {
            return;
        }

        let $this = $(this);
        let record = $this.data('record');
        let action = @json(route('admin.order.delete', ['id' => ':id']));
        action = action.replace(':id', @json($data->id));

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
                    window.location = @json(route('admin.order'));
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
</script>
@endpush