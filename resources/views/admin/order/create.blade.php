@extends('_blank')
@push('style')
<style>
    .table-responsive {
        max-height:300px;
    }
</style>
@endpush
@section('content')
    <div class="row g-2 py-2 max-vh-50">
        @include('admin.order.elements.info_customer')
        @include('admin.order.elements.info_additional')
    </div>
    <div class="col-md-12">
        @include('admin.order.elements.card_prod')
    </div>
    <div class="card" style="display:none" id="package_and_delivery">
        @include('admin.order.elements.package_and_delivery')
    </div>
    <div class="list-button mb-3" style="display:none" id="list_button_action">
        <div class="item d-flex flex-wrap gap-2 justify-content-end">
            <button type="button" class="btn btn-outline-primary">Thoát</button>
            <button type="button" class="btn btn-primary" id="btn_order">
                <span>Tạo đơn hàng</span>
                <span class="spinner-border spinner-border-sm" style="display:none" role="status" aria-hidden="true"></span>
            </button>
        </div>
    </div>
@endsection
@push('js')
    <script>
        let data_prod = [];

        const choose_discount = {
            value : 1,
            percent: 2
        };
        const elements_card = {
            info_customer: $("#card_info_customer"),
            info_product: $("#card_info_product"),
            info_addition: $("#card_info_addition"),
            result_info_product: $("#result_info_product"),
        };

        const card_customer = {
            select_find_customer: elements_card.info_customer.find("#info_customer"),
            empty_customer: elements_card.info_customer.find("#empty"),
        };
        const card_product = {
            select_find_prod: elements_card.info_product.find('#info_product'),
            table_product: elements_card.info_product.find('#table_product'),
            empty_prod: elements_card.info_product.find("#empty_prod"),
            btn_infor_prod: elements_card.info_product.find('#btn_infor_product'),
        };

        const card_result_info_product = {
            label_total_quantity_order: elements_card.result_info_product.find("#cnt_total_product"),
            input_total_balance: elements_card.result_info_product.find("#total"),
            discount_total: elements_card.result_info_product.find("#discount_total"),
            discount_total_money: elements_card.result_info_product.find("#discount_total_money"),
            customer_paid_total: elements_card.result_info_product.find("#customer_paid_total"),
            customer_has_paid_total: elements_card.result_info_product.find("#customer_has_paid_total"),
            total_end: elements_card.result_info_product.find("#total_end"),
        };
    </script>
    <script>
        // const assets_storage = @json(asset("storage/:image_url"));
        // function formatRepo(repo){
        //     if (repo.loading) {
        //         return repo.text;
        //     }
            
        //     var $box_item = $(`
        //         <div class="customer-item row">
        //             <div class="col-auto">
        //                 <div class="avatar-sm">
        //                     <img src="/assets/images/avatar-trang.jpg" alt="" class="rounded-circle img-thumbnail">
        //                 </div>    
        //             </div>
        //             <div class="col">
        //                 <div>${repo.full_name}</div>
        //                 <div>${repo.phone}</div>
        //             </div>
        //         </div>
        //     `);

        //     return $box_item;
            
        // }

        // function formatRepoProduct(repo){
        //     if (repo.loading) {
        //         return repo.text;
        //     }
        //     let assets_storage = @json(asset("storage/:image_url"));
        //     let image = repo.image_url ? assets_storage.replace(':image_url', repo.image_url) : false;
        //     if(!image) {
        //         image = @json(asset("assets/images/no-image.jpg"));
        //     }
        //     var $container = $(`
        //         <div class="list-group-item d-flex justify-content-between align-items-center">
        //             <div>
        //                 <div class="d-flex">
        //                     <div style="width: 50px; height: 50px;">
        //                         <img src="${image}" class="img-fluid">
        //                     </div>
        //                     <div class="ms-2">
        //                         <div class="fw-bold">${repo.name}</div>
        //                         <div class="">${repo.sku}</div>
        //                     </div>
        //                 </div>
        //             </div>
        //             <div class="text-end">
        //                 <span class="fw-bold">${Number(repo.price).toLocaleString("vi")}</span>
        //                 <div class="">Tồn: <span class="link-danger">${Number(repo.product_stock.stock_quantity).toLocaleString('vi')}</span> | Có thể bán: <span class="link-danger">${Number(repo.product_stock.available_quantity).toLocaleString('vi')}</span></div>
        //             </div>
        //         </div>
        //     `);

        //     return $container;
            
        // }

        // function formatRepoSelection(repo){
        //    return repo.text;
        // }

        // function renderItem(data, index){    
        //     let image = data.image_url ? assets_storage.replace(':image_url', data.image_url) : false;
        //     if(!image) {
        //         image = @json(asset("assets/images/no-image.jpg"));
        //     }   
        //     return `
        //         <tr class="product_item">
        //             <td>
        //                 <div class="text-center">${index+1}</div>
        //             </td>
        //             <td>
        //                 <img src="${image}" alt="contact-img" title="contact-img" class="rounded me-1" height="64">
        //                 <p class="m-0 d-inline-block align-middle font-16">
        //                     <a href="apps-ecommerce-products-details.html" class="text-body">${data.name}</a>
        //                     <br>
        //                     <small><b>Danh mục:</b> ${data.category.name}
        //                     </small>
        //                 </p>
        //             </td>
        //             <td class="quantity">
        //                 <input data-toggle="touchspin" data-index=${index} data-bts-min="1" data-bts-max="${data.product_stock.available_quantity}" value="1" data-btn-vertical="true" type="text" class="form-control text-center product_quantity">
        //             </td>
        //             <td>
        //                 <div class="text-center">${Number(data.price).toLocaleString('vi')}</div>
        //             </td>
        //             <td>
        //                 <div class="d-flex flex-wrap align-items-center parent_discount" data-index="${index}" data-name="options_${index}" data-price="${Number(data.price)}">
        //                     <div class="">
        //                         <input type="radio" class="btn-check btn_discount" name="options_${index}" id="option_left_${index}" autocomplete="off" value="1" checked>
        //                         <label class="btn btn-outline-primary" for="option_left_${index}">Giá trị</label>
        //                     </div>
        //                     <div class="">
        //                         <input type="radio" class="btn-check btn_discount" name="options_${index}" id="option_right_${index}" autocomplete="off" value="2">
        //                         <label class="btn btn-outline-primary" for="option_right_${index}">%</label>
        //                     </div>
        //                     <div class="col">
        //                         <div class="input-group bootstrap-touchspin bootstrap-touchspin-injected">
        //                             <input type="text" oninput="this.value = this.value.replace(/[^0-9]/g, '');" class="form-control text-end input_discount" value="0">
        //                             <span class="input-group-addon bootstrap-touchspin-postfix input-group-append">
        //                                 <span class="input-group-text unit">đ</span>
        //                             </span>
        //                         </div>
        //                     </div>
        //                 </div>
        //                 <div class="discount text-end" style="display:none;">
        //                     <span class="small text-danger"></span>
        //                 </div>
        //             </td>
        //             <td class="total_price">
        //                 <div class="text-center">${Number(data.price).toLocaleString('vi')}</div>
        //             </td>
        //             <td class="bg-danger text-white">
        //                 <a href="javascript:void(0);" class="action-icon delete_item" data-index=${index}> <i class="ri-close-line"></i></a>
        //             </td>
        //         </tr>
        //     `;
        // }

        // function changeHtml(){
        //     let index = 1;
        //     let index_name_option = 0;
        //     card_product.table_product.find('tbody tr.product_item').each(function(item){
        //         let $this = $(this);

        //         $this.find('.quantity').find('.product_quantity').data('index', index_name_option);
        //         console.log($this.find('.quantity').find('.product_quantity').data('index'));
                
        //         $this.find('td:last-child').find('.delete_item').data('index', index_name_option);
        //         $this.find('td:eq(0)').text(index);
        //         let parent_discount = $this.find('td:eq(4)').find('.parent_discount');
        //         parent_discount.data('name', `options_${index_name_option}`);
        //         parent_discount.data('index', index_name_option)
                
        //         let btn_left_input = parent_discount.find('div:eq(0)');
        //         btn_left_input.find('input.btn_discount').attr('name', `options_${index_name_option}`);
        //         btn_left_input.find('input.btn_discount').attr('id', `option_left_${index_name_option}`);
        //         btn_left_input.find('label').attr('for', `option_left_${index_name_option}`);

        //         let btn_right_input = parent_discount.find('div:eq(1)');
        //         btn_right_input.find('input.btn_discount').attr('name', `options_${index_name_option}`);
        //         btn_right_input.find('input.btn_discount').attr('id', `option_right_${index_name_option}`);
        //         btn_right_input.find('label').attr('for', `option_right_${index_name_option}`);
                
        //         index++;
        //         index_name_option++;
        //     })
        // }

        function calculateTotalProduct(){
            card_result_info_product.label_total_quantity_order.text(data_prod.length);
            card_result_info_product.input_total_balance.val(data_prod.reduce((tich_tru, item) => tich_tru + item.total, 0 ).toLocaleString('vi'));
            card_result_info_product.discount_total.val(0).trigger('keyup')
        }

        $(document).ready(function(){

            // card_product.select_find_prod.select2({
            //     closeOnSelect: false,
            //     minimumInputLength: 3,
            //     language: "vi",
            //     ajax: {
            //         url: @json(route('admin.order.get_data_product')),
            //         delay: 250,
            //         type: 'GET',
            //         data: function(params){
            //             var query = {
            //                 search: params.term,
            //                 page: params.page || 1
            //             }

            //             return query;
            //         },
            //         processResults: function(response, params){
            //             params.page = params.page || 1;
                    
            //             return {
            //                 results: response.data.data,
            //                 pagination: {
            //                     more: response.data.current_page < response.data.last_page
            //                 }
            //             };
            //         },
            //     },
            //     templateResult: formatRepoProduct,
            //     templateSelection: formatRepoSelection
            // }).on("select2:select", function(e){
            //     let $this = $(this);
            //     let data = $this.select2('data')[0];
            //     card_product.empty_prod.hide();
            //     card_product.table_product.removeClass('d-none');
            //     $this.val("").trigger("change");
            //     let index = card_product.table_product.find('tbody tr').length;
            //     card_product.table_product.find('tbody').append(renderItem(data, index));
            //     $('[data-toggle="touchspin"], .touchspin').TouchSpin();

            //     data_prod.push({
            //         name: data.name,
            //         price: Number(data.price),
            //         quantity: 1,
            //         total: Number(data.price) * 1,
            //     });

            //     calculateTotalProduct();
            // });

            // card_product.btn_infor_prod.click(function(e){
            //     e.preventDefault();
            //     card_product.select_find_prod.select2('open');
            // })

            // $(".input_money").on('keyup', function(e){
            //     let $this = $(this);
            //     let value = Number($this.val().replace(/\D/g,''));
            //     $this.val(value.toLocaleString('vi'))
            // }).on('focus', function(e){
            //     if(Number($(this).val()) === 0) {
            //         $(this).val("")
            //     }
            // }).on('blur', function(e){
            //     if($(this).val() === "") {
            //         $(this).val(0)
            //     }
            // });

            // card_result_info_product.discount_total.keyup(function(e){
            //     let $this = $(this);

            //     card_result_info_product.customer_has_paid_total.val(0).trigger('keyup');

            //     let total_balance = Number(card_result_info_product.input_total_balance.val().replaceAll('.', ''));
            //     if(total_balance === 0) {
            //         let $this = $(this).val(0);
            //         return;
            //     }

            //     let value = Number($this.val().replaceAll('.', ''));

            //     if(value >= 100) {
            //         $this.val(100);
            //         value = 100;
            //     }

            //     if(value <= 0 || value === "") {
            //         $this.val(0);
            //         value = 0;
            //         card_result_info_product.discount_total_money.addClass('d-none');
            //         card_result_info_product.discount_total_money.find('span').text(0);
            //     }

            //     let discount_total_money = -(total_balance * value) / 100;
                
            //     if(discount_total_money !== -0){
            //         card_result_info_product.discount_total_money.removeClass('d-none');                
            //         card_result_info_product.discount_total_money.find('span').text(discount_total_money.toLocaleString('vi'))
            //     }

            //     let customer_paid_total = total_balance + discount_total_money;
            //     card_result_info_product.customer_paid_total.val(customer_paid_total.toLocaleString('vi'))

            // })

            // card_result_info_product.customer_has_paid_total.keyup(function(e){
            //     let $this = $(this);

            //     let value = Number($this.val().replaceAll('.', ''));

            //     let customer_paid_total = Number(card_result_info_product.customer_paid_total.val().replaceAll('.', ''));
            //     let total_balance = Number(card_result_info_product.input_total_balance.val().replaceAll('.', ''));
            //     if(total_balance === 0 && customer_paid_total === 0) {
            //         $this.val(0);
            //         value = 0;
            //         return;
            //     }

            //     if(customer_paid_total === 0) {
            //         $this.val(0);
            //         value = 0;
            //         card_result_info_product.total_end.val(0);
            //         return;
            //     }

            //     if(value === 0) {
            //         card_result_info_product.total_end.val(0);
            //         return;
            //     }

            //     let total_end = customer_paid_total - value;
            //     card_result_info_product.total_end.val(total_end.toLocaleString('vi'));
            // })

            // $("input[name='options']").on('change', function(){
            //     let $this = $(this);
            //     let id = $this.attr('id');
            //     $(".tab-content").find('.tab-pane.active').removeClass('active');

            //     $(".tab-content").find(`#tab-${id}`).addClass('active');

            //     if(id === 'option4') {
            //         $("#left").addClass('d-none');
            //         $("#right").removeClass('d-none');
            //     } else if(id === 'option3'){
            //         $("#left").addClass('d-none');
            //         $("#right").addClass('d-none');
            //     }else {
            //         $("#left").removeClass('d-none');
            //         $("#right").removeClass('d-none');
            //     }
            // })
        })

        $(document).on('keydown', function(e){
            if(event.keyCode === 114) {
                e.preventDefault();
                card_product.select_find_prod.select2('open');

                setTimeout(() => {
                    $('.select2-container--open .select2-search__field').focus();
                }, 0);
            } else if(event.keyCode === 115) {
                e.preventDefault();
                if(card_customer.select_find_customer.next('.select2-container').is(":hidden") === false) {
                    card_customer.select_find_customer.select2('open');

                    setTimeout(() => {
                        $('.select2-container--open .select2-search__field').focus();
                    }, 0);
                }

            }
            
        })

        // $(document).on('click', '#clear_customer', function(e){
        //     e.preventDefault();
        //     card_customer.select_find_customer.val("").trigger("change");
        //     $(".result > .data-customer").remove()
        //     card_customer.empty_customer.removeClass('d-none');
        //     card_customer.select_find_customer.next(".select2-container").show();
        //     card_customer.select_find_customer.select2('open');
        // })

        // $(document).on("click", ".delete_item", function(){
        //     let $this = $(this);
        //     let index = $this.data('index');
        //     $this.parents('.product_item').remove();
        //     if(card_product.table_product.find('tbody tr').length === 0) {
        //         card_product.empty_prod.show();
        //         card_product.table_product.addClass('d-none');
        //         card_product.select_find_prod.select2('open');
        //     }
        //     else {
        //         changeHtml();
        //     }

        //     data_prod.splice(index, 1);
        //     calculateTotalProduct();
        // })

        // $(document).on('keyup', '.input_discount', function(e){
        //     let $this = $(this);
        //     let value = Number($this.val());
        //     let product_item = $this.parents('.product_item');
        //     let quantity = Number(product_item.find('.quantity').find('input').val());
        //     let parent_discount = $this.parents('.parent_discount');
        //     let index = parent_discount.data('index');
            
        //     let name_radio = parent_discount.data('name');
        //     let price = Number(parent_discount.data('price')) * quantity;
        //     let is_choose_discount = $(`[name='${name_radio}']:checked`).val();
        //     let total = 0;
        //     let unit = "";
        //     if(parseInt(is_choose_discount) === choose_discount.percent) {
        //         unit = "%";
        //         if(value > 100) {
        //             $this.val(100);
        //             value = 100;
        //         }

        //         let total_discount = (price * value) / 100;
        //         total = price - total_discount;

        //         if(value > 0) {
        //             parent_discount.next().find('span').text((total_discount * -1).toLocaleString('vi'));
        //             parent_discount.next().show();
        //         } else {
        //             parent_discount.next().find('span').empty();
        //             parent_discount.next().hide();
        //         }

        //     } else {
        //         unit = "đ";
        //         parent_discount.next().find('span').empty();
        //         parent_discount.next().hide();

        //         if(value > price) {
        //             $this.val(price)
        //             value = price;
        //         }
        //         total = price - value;
        //     }

        //     $this.val(value.toLocaleString('vi'));
        //     product_item.find('.total_price').find('div').text(total.toLocaleString('vi'))
        //     product_item.find('span.unit').text(unit);

        //     data_prod[index].total = total;
        //     calculateTotalProduct();
            
        // }).on('blur', '.input_discount', function(){
        //     let $this = $(this);
        //     if($this.val() === "" || $this.val() < 0) {
        //         $this.val(0);
        //     }
        // })

        // $(document).on("change", ".btn_discount", function(){
        //     let $this = $(this);
        //     let product_item = $this.parents('.product_item');
        //     let parent_discount = $this.parents('.parent_discount');
        //     let quantity = Number(product_item.find('.quantity').find('input').val());
        //     let price = Number(parent_discount.data('price')) * quantity;
        //     let input_discount = parent_discount.find('.input_discount').val(0).trigger("change");
        //     product_item.find('.total_price').find('div').text(Number(price).toLocaleString('vi'));
        //     let unit = "";
        //     if(parseInt($this.val()) === choose_discount.percent) {
        //         unit = "%";
        //     } else {
        //         unit = "đ";
        //         parent_discount.next().find('span').empty();
        //         parent_discount.next().hide();
        //     }
        //     product_item.find('span.unit').text(unit);
        // })

        // $(document).on('change', '.product_quantity', function(e){
        //     e.preventDefault();
        //     let $this = $(this);
        //     let index = $this.data('index');
        //     let quantity = Number($this.val());
        //     let product_item = $this.parents('.product_item');
        //     let parent_discount = product_item.find('.parent_discount')
        //     let price = Number(parent_discount.data('price'));
        //     parent_discount.find('.input_discount').val(0).trigger('keyup');
        //     product_item.find('.total_price > div').text(Number(price * quantity).toLocaleString('vi'));

        //     data_prod[index].quantity = quantity;
        //     data_prod[index].total = Number(price * quantity);
        //     calculateTotalProduct()
        // })
    </script>
@endpush
@push('js_ready')
@php echo file_get_contents(asset('/assets/js/order/order.js')) @endphp
@endpush