@extends('_blank')
@section('content')
<div class="container-fluid mt-3">
    <div class="row g-3">
        <!-- LEFT -->
        <div class="col-lg-8">
            <!-- Customer Info Card -->
            <div class="card">
                <div class="card-body">
                    <!-- Header -->
                    <div class="d-flex align-items-center mb-4 gap-2">
                        <div class="">
                            <img class="avatar-lg rounded-circle" src="{{asset('storage/' . auth()->user()->profile_default)}}" alt="">
                        </div>
                        <div>
                            <h4 class="mb-0">{{$find_data->full_name}}</h4>
                            <small class="text-muted">Thông tin khách hàng</small>
                        </div>
                    </div>

                    <!-- Stats -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100">
                                <div class="text-muted mb-1">
                                    <i class="uil uil-heart me-1"></i> Đã gắn bó với cửa hàng
                                </div>
                                <h4 class="mb-1">
                                    {{$get_customer_join}}
                                </h4>

                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100">
                                <div class="text-muted mb-1">
                                    <i class="uil uil-shopping-bag me-1"></i> Tổng số đơn đã mua
                                </div>
                                <h4 class="mb-1">{{$find_data->list_order ? $find_data->list_order->count() : 0}} đơn</h4>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100">
                                <div class="text-muted mb-1">
                                    <i class="uil uil-moneybag me-1"></i> Nợ phải thu
                                </div>
                                <h4 class="mb-1">
                                    @php
                                        $total_amount =  $find_data->list_order ?$find_data->list_order->sum('total_amount') : 0;
                                        $total_paid_amount =  $find_data->list_order ?$find_data->list_order->sum('paid_amount') : 0;

                                        $calculate = $total_amount - $total_paid_amount;
                                        if( $calculate > 0) {
                                            $money = number_format($calculate);
                                        } else {
                                            $money = 0;
                                        }
                                    @endphp
                                   {{$money}} ₫
                                </h4>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100">
                                <div class="text-muted mb-1">
                                    <i class="uil uil-moneybag me-1"></i> Tổng chi tiêu
                                </div>
                                <h4 class="mb-1">{{ $find_data->list_order ? number_format($find_data->list_order->sum('paid_amount')) : 0 }} ₫</h4>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Order List -->
            <div class="card mt-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">Danh sách đơn hàng</h5>
                        <a href="{{route('admin.order')}}?customer_id={{$find_data->id}}" class="text-primary">Xem tất cả</a>
                    </div>

                    <div class="list-group list-group-flush">
                        <!-- Order item -->
                        @foreach($find_data->list_order as $item_od)
                        <div class="list-group-item px-0">
                            <div class="row align-items-center">
                                <!-- Left column -->
                                <div class="col">
                                    <h6 class="mb-1">#{{$item_od->code}}</h6>
                                    <p class="mb-0 text-muted">{{number_format($item_od->total_amount)}} ₫</p>
                                </div>

                                <!-- Right column -->
                                <div class="col-auto text-end">
                                    <p class="mb-1 text-muted">{{date("d/m/Y H:i", strtotime($item_od->created_at))}}</p>
                                    <a href="{{route('admin.order.detail', ['id' => $item_od->id])}}" class="text-primary">
                                        Xem đơn hàng <i class="uil uil-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach 
                    </div>
                </div>
            </div>

        </div>

        <!-- RIGHT -->
        <div class="col-lg-4">
            <!-- Address -->
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">Thông tin liên hệ</h5>
                    <p class="mb-2"><a href="tel:{{$find_data->phone}}">{{$find_data->phone}}</a></p>
                    <p class="mb-2"><a href="mailto:{{$find_data->email}}">{{$find_data->email}}</a></p>
                </div>
            </div>
            <!-- Address -->
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title mb-2">Nhóm khách hàng</h5>
                    <!-- Multiple Select -->
                    <select class="select2 form-control select2-multiple" data-toggle="select2" multiple="multiple" data-placeholder="Chọn nhóm khách hàng" name="tag_customer_ids">
                        @foreach($get_customer_tags as $tag)
                        <option {{in_array($tag->id, $find_data->tags_customer) ? 'selected' : ''}} value="{{$tag->id}}">{{$tag->tag_name}}</option>
                        @endforeach
                    </select>
                    <button class="btn btn-primary mt-1 w-100" id="save_tag">Lưu</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('js')
<script>
    const URL_UPDATE_TAG = @json(route('admin.customer.update', ['id' => $find_data->id]));
    const DATA =  @json($find_data);

    $(document).ready(function(){
        $("#save_tag").click(function(e){
            e.preventDefault();
            let $this = $(this);
            let tag_customer_ids = $("select[name='tag_customer_ids']").val();

            DATA.tags = tag_customer_ids;
            DATA._token = @json(csrf_token());
            DATA.method = "PUT";

            $.ajax({
                url: URL_UPDATE_TAG,
                method: "PUT",
                data: DATA,
                beforeSend: function(){
                    $this.prop('disabled', true);
                },
                success: function(res){
                    if(res.success) {
                        createToast('success', res.message, 5000);
                    }
                },
                error: function(err){
                    console.log(err);
                    
                },
                complete: function(){
                    $this.prop('disabled', false);
                }
            });
        })
    })
</script>
@endpush