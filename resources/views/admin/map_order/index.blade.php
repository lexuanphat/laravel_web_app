@extends('_blank')
@section('content')
@push('style')
        <style>
    /* Biến tấu card để trông hiện đại hơn */
    .add-card {
        border: 2px dashed #dee2e6; /* Đường viền đứt đoạn tạo cảm giác "chờ thêm mới" */
        border-radius: 10px;
        transition: all 0.3s ease-in-out;
        cursor: pointer;
        background-color: #fafbfe;
        max-height: 150px;
        min-height: 150px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Hiệu ứng khi di chuột vào */
    .add-card:hover {
        background-color: #fff;
        border-color: #727cf5; /* Màu sắc đặc trưng của Hyper */
        box-shadow: 0 10px 20px rgba(0,0,0,0.05);
    }

    /* Biến tấu dấu cộng */
    .plus-icon {
        font-size: 2rem;
        color: #adb5bd;
        transition: all 0.3s ease;
    }

    .add-card:hover .plus-icon {
        color: #727cf5;
        transform: scale(1.2);
    }

    .card-title-sub {
        font-size: 0.9rem;
        color: #98a6ad;
        margin-top: 10px;
        font-weight: 500;
    }
    .element_root .active{
        background-color: #727cf5;
    }

    .element_root .element {
        height: 80px; /* Cố định chiều cao cho vùng chứa icon */
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 10px;
    }

    .element_root .element svg {
        max-width: 100%;
        max-height: 100%;
        display: block;
    }

</style>
@endpush
<div class="card d-none">
    <div class="card-body">
        <div class="row g-2 align-items-center">
    
        <!-- Thanh tìm kiếm chính -->
        <div class="col-md-6">
            <div class="input-group">
            <span class="input-group-text"><i class="mdi mdi-magnify"></i></span>
            <input type="text" id="searchInput" class="form-control" placeholder="Tìm kiếm tên danh mục">
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
<div class="card">
    <div class="card-body">
        <div class="row g-4">
            @for($i = 1; $i <= 500; $i++)
            @if($data->has($i))
            <div class="col-xl-3 col-lg-4 col-md-6 element_root" data-id="{{$data->get($i)->id}}" data-code="{{$data->get($i)->code}}">
                <div class="add-card text-center d-flex flex-column active">
                    <button class="btn-delete btn-danger btn" title="Xoá bồn này" onclick="confirmDelete({{$data->get($i)->id}}, {{$i}}, this)">
                        <i class="bi bi-x-lg">×</i>
                    </button>
                    <div class="element">
                        @if($data->get($i)->target_type == 1)
                        <svg width="65" height="65" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <ellipse cx="50" cy="25" rx="25" ry="10" fill="#0acf97" stroke="#08a075" stroke-width="2"/>
                            
                            <path d="M25 25V75C25 83.284 36.193 90 50 90C63.807 90 75 83.284 75 75V25 A25 10 0 0 1 25 25" fill="#0acf97" stroke="#08a075" stroke-width="2"/>
                            
                            <path d="M25 45H75" stroke="white" stroke-width="2" stroke-opacity="0.3"/>
                            <path d="M25 65H75" stroke="white" stroke-width="2" stroke-opacity="0.3"/>
                            
                            <circle cx="50" cy="80" r="4" fill="white" fill-opacity="0.5"/>
                        </svg>
                        @else
                        <svg width="65" height="65" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="20" y="80" width="60" height="8" rx="2" fill="#adb5bd"/>
                            
                            <rect x="22" y="15" width="56" height="65" rx="5" fill="#f8f9fa" stroke="#dee2e6" stroke-width="2"/>
                            
                            <path d="M22 35H78 M22 55H78" stroke="#727cf5" stroke-width="1.5"/>
                            <path d="M40 15V80 M60 15V80" stroke="#727cf5" stroke-width="1.5"/>
                            
                            <rect x="42" y="8" width="16" height="7" rx="1" fill="#343a40"/>
                            
                            <circle cx="50" cy="65" r="5" fill="#fa5c7c"/>
                        </svg>
                        @endif
                    </div>
                    <div class="card-title-sub text-white fw-bold">{{$data->get($i)->code}}</div>
                    <div class="rut-nuoc">
                        <button class="btn btn-info" onclick="btnRut(event, this, '{{$data->get($i)->code}}', {{$data->get($i)->current_capacity}}, {{$data->get($i)->target_type}})">Rút</button>
                    </div>
                </div>
            </div>
            @else
            <div class="col-xl-3 col-lg-4 col-md-6 element_root" onclick="btnAdd(event, this)" data-order="{{$i}}">
                <div class="add-card text-center d-flex flex-column">
                    <div class="plus-icon element">+</div>
                </div>
            </div>
            @endif
            @endfor
        </div>
    </div>
</div>
@include('admin.map_order.modal-rut')
@include('admin.map_order.modal-trans-log')
@endsection
@push('js')
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>
        function btnAdd(e, div_element){
            let jquery_el = $(div_element);
            let order = jquery_el.data('order');
            let find_element_replace_to_input = jquery_el.find('.element');
            find_element_replace_to_input.html(`
                <div class="input-group">
                        <input type="text" 
                        class="form-control target_id" 
                        name="target_id" 
                        onclick="event.stopPropagation()">
                   <button type="button" class="btn btn-primary" onclick="handleAdd(event, this, ${order})">OK</button>
                </div>

            `);
        }

        function handleAdd(e, button, order) {
            e.stopPropagation();
            let jquery_button = $(button);
            let input = jquery_button.parent().find('.target_id');
            let value = input.val();
            let data = {
                _token: @json(csrf_token()),
                code: value,
                order: order
            };
            ajaxAddMapOrder(data, input)
        }

        function ajaxAddMapOrder(data, input){
            $.ajax({
                url: @json(route('admin.map_tank_vat.create')),
                type: "POST",
                data: data,
                beforeSend: function(){
                    input.prop('disabled', true)
                },
                success: function(res){
                    if(res.success) {
                        let data = res.data;
                        let svg = `
                                <svg width="65" height="65" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <ellipse cx="50" cy="25" rx="25" ry="10" fill="#0acf97" stroke="#08a075" stroke-width="2"/>
                                    
                                    <path d="M25 25V75C25 83.284 36.193 90 50 90C63.807 90 75 83.284 75 75V25 A25 10 0 0 1 25 25" fill="#0acf97" stroke="#08a075" stroke-width="2"/>
                                    
                                    <path d="M25 45H75" stroke="white" stroke-width="2" stroke-opacity="0.3"/>
                                    <path d="M25 65H75" stroke="white" stroke-width="2" stroke-opacity="0.3"/>
                                    
                                    <circle cx="50" cy="80" r="4" fill="white" fill-opacity="0.5"/>
                                </svg>
                        `;
                        if(data.target_type == 2) {
                            svg = `
                                <svg width="65" height="65" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect x="20" y="80" width="60" height="8" rx="2" fill="#adb5bd"/>
                                        
                                        <rect x="22" y="15" width="56" height="65" rx="5" fill="#f8f9fa" stroke="#dee2e6" stroke-width="2"/>
                                        
                                        <path d="M22 35H78 M22 55H78" stroke="#727cf5" stroke-width="1.5"/>
                                        <path d="M40 15V80 M60 15V80" stroke="#727cf5" stroke-width="1.5"/>
                                        
                                        <rect x="42" y="8" width="16" height="7" rx="1" fill="#343a40"/>
                                        
                                        <circle cx="50" cy="65" r="5" fill="#fa5c7c"/>
                                    </svg>
                            `;
                        }
                        let $parent = input.parents('.element_root');
                        $parent.removeAttr('onclick');
                        $parent.removeAttr('data-order');
                        $parent.html(`
                            <div class="add-card text-center d-flex flex-column active">
                                <button class="btn-delete btn-danger btn" title="Xoá bồn này" onclick="confirmDelete(${data.id}, ${data.order}, this)">
                                    <i class="bi bi-x-lg">×</i>
                                </button>
                                <div class="element">
                                    ${svg}
                                </div>
                                <div class="card-title-sub text-white fw-bold">${data.code}</div>
                                <div class="rut-nuoc">
                                    <button class="btn btn-info" onclick="btnRut(event, this, '${data.code}', ${data.current_capacity}, ${data.target_type})">Rút</button>
                                </div>
                            </div>
                        `);
                    }
                    createToast('success', res.message);
                    return;
                },
                error: function(err){
                    console.log(err);
                    
                },
                complete: function(){
                    input.prop('disabled', false)
                }
            });
        }

        function confirmDelete(id, order, button){
            let result = confirm("Có chắc muốn xoá dữ liệu?");
            if(!result) {
                return;
            }

            deleteRecord(id, order, button);
        }

        function deleteRecord(id, order, button){
            url = @json(route('admin.map_tank_vat.delete', ['id' => ':id']));
            $.ajax({
                url: url.replace(":id", id),
                type: "DELETE",
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                },
                beforeSend: function(){
                    $(button).prop('disabled', true);
                },
                success: function(res){
                    if(res.success) {
                        let $parent = $(button).parents('.element_root');
                        $parent.html(`
                            <div class="add-card text-center d-flex flex-column">
                                <div class="plus-icon element">+</div>
                            </div>
                        `);
                        $parent.attr('onclick', 'btnAdd(event, this)');
                        $parent.attr('data-order', order);
                        createToast('success', res.message);
                    }
                },
                error: function(err){
                    let data_error = err.responseJSON;
                    if(data_error.success === false) {
                        $("#not_fount_modal").find('#modal_title_not_found').text(message_errors.title_not_exists);
                        $("#not_fount_modal").find('#modal_text_not_found').text(data_error.message);
                        $("#not_fount_modal").modal('show');
                    }
                },
                complete: function(){
                    $(button).prop('disabled', false);
            }});
        }

        function btnRut(e, button, code, current_capacity, target_type){
            e.stopPropagation();
            $("#modal_rut").find('#target_type').attr('data-code', code);
            $("#modal_rut").find("#modal_label").html(`<h4>Rút nước từ</h4><h2><span class="badge badge-success-lighten">${code}</span></h2> - <h4>Dung tích hiện tại ${Number(current_capacity).toLocaleString('vi')} lít</h4>`);
            $("#modal_rut").modal('show');
            $("#modal_rut").find('#code_target_type').val(target_type)
            $("#modal_rut").find('#code_target_id').val(code)
        }

        $(document).on('focus', '.number', function () {
            $(this).val($(this).val().replaceAll('.', ''));
        });

        $(document).on('blur', '.number', function () {
            let value = $(this).val().replace(/,/g, '');

            if ($.isNumeric(value)) {
                $(this).val(Number(value).toLocaleString('vi'));
            } else {
                $(this).val("")
            }
        });

        $(document).on('change', '#target_type', function(){
            let $this = $(this);
            let target_type = $this.val();
            let target_id = $("#target_id");

            if(target_type) {
                $.ajax({
                    url: @json(route('admin.map_tank_vat.get_data_target')),
                    type: "GET",
                    data: {
                        target_type: target_type,
                        code: $this.data('code'),
                    },
                    beforeSend: function(){
                        target_id.empty().append('<option value="">Đang tải...</option>');
                    },
                    success: function(res){
                        if(res.success) {
                            let data = res.data;
                            target_id.empty();
                            let html = '<option value="">-- Chọn đối tượng --</option>';
                            $.each(data, function(index, item) {
                                html += `
                                <option value="${item.id}">
                                    ${item.code} - Dung tích hiện tại: ${Number(item.current_capacity).toLocaleString('vi')} lít
                                    - Dung tích tối đa: ${Number(item.max_capacity).toLocaleString('vi')} lít
                                </option>`;
                            });
                            target_id.prop('disabled', false);
                            target_id.html(html);
                        }
                    },
                    error: function(err){
                        alert('Không thể tải dữ liệu!');
                    },
                    complete: function(){
                        
                    }
                });
            } else {
                target_id.empty().append('<option value="">-- Vui lòng chọn loại trước --</option>');
            }
        })

        $(document).on('hide.bs.modal', '#modal_rut', function(e){
            let $this = $(this);
            $this.find('#target_type').html(`
                <option value="">-- Chọn loại --</option>
                <option value="BON">Bồn</option>
                <option value="THUNG">Thùng</option>
            `);
            $this.find('#target_id').empty();
            $this.find('#qty').val("");
            $this.find('#code_target_type').val("");
            $this.find('#code_target_id').val("");
            $this.find('#target_id').prop('disabled', true);
            $this.find('#target_id').append(`<option value="">-- Vui lòng chọn loại trước --</option>`);
        })

        $(document).on('click', '#btnHandleRut', function(e){
            e.preventDefault();
            let button = $(this);
            let modal = button.parents('#modal_rut');
            $.ajax({
                url: @json(route('admin.map_tank_vat.handle')),
                type: "POST",
                data: {
                    _token: @json(csrf_token()),
                    code_target_type: modal.find('#code_target_type').val(),
                    code_target_id: modal.find('#code_target_id').val(),
                    target_type: modal.find('#target_type').val(),
                    target_id: modal.find('#target_id').val(),
                    qty: modal.find('#qty').val(),
                },
                beforeSend: function(){
                    modal.find('.form-control').removeClass('is-invalid');
                    modal.find('.invalid-feedback').empty();
                    $(button).prop('disabled', true);
                },
                success: function(res){
                    if(res.success) {
                        createToast('success', res.message);
                       window.location.reload();
                    }
                },
                error: function(err){
                    let response_err = err.responseJSON;
                    if(response_err) {
                        $.each(response_err.errors, function(key, item){
                            modal.find("#"+key).addClass('is-invalid');
                            modal.find("#"+key).next().text(item[0]);
                        })
                    }
                    
                },
                complete: function(){
                    $(button).prop('disabled', false);
                }
            })
        })

        $(document).on('click', '.element_root > .active', function(e){
            let id = $(this).parent().data('id');
            let code = $(this).parent().data('code');

            swal({
                title: "Đang xử lý...",
                text: "Vui lòng đợi trong giây lát",
                showConfirmButton: false,
                allowOutsideClick: false
            });


            if ($.fn.DataTable.isDataTable('#history-table')) {
                $('#history-table').DataTable().destroy();
            }
            $('#history-table').DataTable({
                language: {
                    url: @json(asset('/assets/js/vi.json')),
                },
                ajax: {
                    url: @json(route('admin.map_tank_vat.trans_log')),
                    type: "GET",
                    data: {
                        id: id,
                    },
                },
                searching: false,
                stateSave: true,
                processing: true,
                serverSide: true,
                ordering: false,
                columns: [
                    { data: 'date', name: 'date', class: 'align-middle'},
                    { data: 'target_from_html', name: 'target_from_html', class: 'align-middle target_from position-relative'},
                    { data: 'target_to_html', name: 'target_to', class: 'align-middle target_to'},
                    { data: 'amount', name: 'amount', class: 'align-middle'},
                    { data: 'user_full_name', name: 'user_full_name', class: 'align-middle'},
                ],
                createdRow: function(row, data, dataIndex, cells){
                    if(code == data.target_from) {
                        $(row).find('.target_from').addClass('fw-bold text-decoration-underline')
                    } else if(code == data.target_to) {
                        $(row).find('.target_to').addClass('fw-bold text-decoration-underline')
                    }
                },
                drawCallback: function(){
                    $("#modal-trans-log").modal('show');
                    swal.close();
                }
            });
         
        })
    </script>
@endpush
