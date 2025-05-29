@extends('_blank')
@section('content')
<div class="row">
    <div class="col-6">
        <div class="card mt-2">
            <div class="card-body pb-0">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h4 class="header-title">Token API Đơn vị vận chuyển</h4>
                </div>
        
                <div class="d-flex flex-column">
                    <div class="items d-flex flex-wrap align-items-center justify-content-between">
                        <div class="box-left col-4">
                            <img src="/assets/images/transport/logo-ghn-new.png" class="w-75">
                            <p class="mb-0">Giao hàng nhanh</p>
                        </div>
                        <div class="box-center col-5">
                            @if(isset($data['GHN']))
                            <input type="password" name="token_ghn" id="token_ghn" class="form-control form-control-sm" disabled value="*******************">
                            @else
                            <input type="text" name="token_ghn" id="token_ghn" class="form-control form-control-sm" disabled value="Chưa kết nối">
                            @endif
                        </div>
                        <div class="box-right col-3 text-end">
                            <button type="button" class="btn btn-primary api_connect" data-key="GHN">
                               Kết nối <i class="ri-login-circle-line fs-6"></i>
                            </button>
                            
                        </div>
                    </div>
                    <hr/>
                </div>
        
        
            </div> <!-- end card-body -->
        </div>
    </div>
</div>
@endsection
@push('js')
<script>
    $(document).ready(function(){
        $(".api_connect").on("click", function(e){
            e.preventDefault();
            let $this = $(this);
            let token_api = prompt("Vui lòng nhập Token API, Ví dụ: token_api");
            if(token_api != null) {
                send(token_api, $this.attr('data-key'));
            }
        });

        function send(token, is_transport){
            $.ajax({
                url: @json(route('admin.token_transport.store')),
                data: {
                    _token: $("[name='csrf-token']").attr('content'),
                    token_api: token,
                    is_transport: is_transport,
                },
                type: "POST",
                beforeSend: function(){},
                success: function(res){
                    if(res.success) {
                        window.location.reload()
                        createToast('success', res.message);
                    }
                },
                error: function(err){
                    
                },
                complete: function(){},
            });
        }
    })
</script>
@endpush