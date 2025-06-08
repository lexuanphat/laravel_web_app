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
                            <button type="button" class="btn btn-primary api_connect" data-key="GHN" data-bs-target="#transport_token">
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
<!-- Standard modal -->
<div id="transport_token" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="standard-modalLabel">Cấu hình token với Đơn vị vận chuyển</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="modal-body">
                <form action="{{route('admin.token_transport.store')}}" method="POST" id="form_token_transport">
                    <input type="hidden" name="is_transport" value="" id="is_transport">
                    @csrf
                    <div class="mb-3">
                        <label for="simpleinput" class="form-label">Token<span class="text-danger">(*)</span></label>
                        <input type="text" name="token_transport" required id="_token" placeholder="-- Nhập Token --" class="form-control">
                    </div><div class="mb-3">
                        <label for="simpleinput" class="form-label">API<span class="text-danger">(*)</span></label>
                        <input type="text" name="api_transport" id="api" required placeholder="-- Nhập API --" class="form-control">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Đóng</button>
                <button type="submit" class="btn btn-primary" id="btn_save">Thêm mới</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@endsection
@push('js')
<script>
    $(document).ready(function(){
        let is_transport = false;
        $(".api_connect").on("click", function(e){
            e.preventDefault();
            $("#form_token_transport").find('#is_transport').val($(this).attr('data-key'));
            $("#transport_token").modal('show');
        });

        $("#btn_save").click(function(e){
            e.preventDefault();
            let $this = $(this);
            $this.prop('disabled', true);
            let data = $("#form_token_transport").serialize();
            send(data, $this);
        })

        function send(data, button){
            $.ajax({
                url: @json(route('admin.token_transport.store')),
                data: data,
                type: "POST",
                beforeSend: function(){},
                success: function(res){
                    if(res.success) {
                        createToast('success', res.message);
                        $("#transport_token").modal('hide');
                        window.location.reload()
                    }
                },
                error: function(err){
                    
                },
                complete: function(){
                    button.prop('disabled', false);
                },
            });
        }
    })
</script>
@endpush