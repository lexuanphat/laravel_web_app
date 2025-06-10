$("#btn_order").click(function(e){
    e.preventDefault();
    let $this = $(this);

    // thu thập dữ liệu khách mua hàng
    let get_customer = ELEMENTS_INFO_CUSTOMER.select_find_customer.select2('data');
    get_customer = get_customer && get_customer[0] ? get_customer[0] : [];

    if (get_customer.length === 0) {
        alert("Vui lòng chọn khách mua hàng");
        return;
    }

    let customer = get_customer.user_id;
    let full_name = CARD_INFO_CUSTOMER.find('#customer_full_name').val();
    let phone = CARD_INFO_CUSTOMER.find('#customer_phone').val();
    let address = CARD_INFO_CUSTOMER.find('#customer_address').val();
    let data_customer = {
        full_name: full_name,
        phone: phone,
        address: address,
        customer_id: customer,
        schedule_delivery_date: $("#schedule_delivery_date").val(),
        source: $("#source").val(),
    };

    // Thu thập dữ liệu sản phẩm order
    let form_table_product = $("#form_table_product");
    let product_items = form_table_product.find('tr.product_item')
    let products = [];
    $.each(product_items, function (index, item) {
        let $this = $(this);
        products.push({
            product_id: $this.attr('data-product'),
            quantity: $this.find('input.product_quantity').val().replaceAll(".", ""),
            is_option: $this.find(`[name="options_${index}"]:checked`).val(),
            discount: $this.find('input.input_discount').val().replaceAll(".", ""),
        });
    })
    
    // Thu thập dữ liệu tổng tiền sản phẩm
    let result_info_product = $("#result_info_product");
    let results = {
        note: result_info_product.find("#note_total").val(),
        discount_total: result_info_product.find("#discount_total").val().replaceAll(".", ""),
        customer_has_paid_total: result_info_product.find("#customer_has_paid_total").val().replaceAll(".", ""),
    };
    
    // Thu thập dữ liệu chọn Loại giao hàng nào
    let element_package_and_delivery = $("#package_and_delivery");
    let package_and_delivery = {
        type: element_package_and_delivery.find("input[type='radio'][name='options']:checked").val(),
        cod: element_package_and_delivery.find("#cod").val().replaceAll('.', ''),
        gam: element_package_and_delivery.find("#gam").val().replaceAll('.', ''),
        length: element_package_and_delivery.find("#length").val().replaceAll('.', ''),
        width: element_package_and_delivery.find("#width").val().replaceAll('.', ''),
        height: element_package_and_delivery.find("#height").val().replaceAll('.', ''),
        require_transport_option: element_package_and_delivery.find("#require_transport_option").val(),
        payment_type_id: element_package_and_delivery.find("#payment_type_id").val(),
        note_transport: element_package_and_delivery.find("#note_transport").val(),
    };

    if (Number(package_and_delivery.type) === 1) {
        package_and_delivery.is_ship = $("#option-transport").find('input[type="radio"]:checked').val();
    } else if (Number(package_and_delivery.type) === 2) {
        package_and_delivery.is_ship = $("[name='loai_giao_hang']:checked").val();
        package_and_delivery.ship_id = $("select#partner_transport").val();
        package_and_delivery.delivery_method_fee = $("#delivery_method_fee").val().replaceAll(".", "");
    } else if (Number(package_and_delivery.type) === 3) {
        package_and_delivery.is_ship = 0;
        package_and_delivery.cod = 0;
        package_and_delivery.gam = 0;
        package_and_delivery.length = 0;
        package_and_delivery.width = 0;
        package_and_delivery.height = 0;
        package_and_delivery.require_transport_option = null;
        package_and_delivery.payment_type_id = null;
        package_and_delivery.note_transport = null;
    } else {
        package_and_delivery.is_ship = 0;
        package_and_delivery.cod = 0;
        package_and_delivery.gam = 0;
        package_and_delivery.length = 0;
        package_and_delivery.width = 0;
        package_and_delivery.height = 0;
        package_and_delivery.require_transport_option = null;
        package_and_delivery.payment_type_id = null;
        package_and_delivery.note_transport = null;
    }

    let full_data = {
        'customer': data_customer,
        'products': products,
        'results': results,
        'package_and_delivery': package_and_delivery,
        'store_id': $("#store_id").val(),
        'response_transport': response_transport_fee,
    };
    console.log(full_data);
    
    $.ajax({
        url: "/admin/order/createOrder",
        type: "POST",
        data: {
            _token: $("[name='csrf-token']").attr('content'),
            data: JSON.stringify(full_data),
        },
        beforeSend: function () {
            $this.find('span:first-child').hide();
            $this.find('span:last-child').show();
            $this.prop('disabled', true);
        },
        success: function (res) {
            if (res.success) {
                createToast('success', res.message);
                setTimeout(() => {
                    window.location.href = res.data.link_redirect;
                }, 2000);
           }
            
        },
        error: function (err) {
            if (err.responseJSON.errors) {
                let html_error = '<ul class="list-group list-group-flush">';
                $.each(err.responseJSON.errors, function (key, message) {
                    html_error += `<li class="list-group-item"><span class='text-danger'>${message[0]}</span></li>`
                })
                html_error += `</ul>`;

                $("body").append(`
                    <div id="order_modal_error" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog modal-sm">
                            <div class="modal-content modal-filled bg-danger">
                                <div class="modal-body p-4">
                                    <div class="text-center">
                                        <i class="ri-close-circle-line h1"></i>
                                        <h4 class="mt-2">Có lỗi, vui lòng kiểm tra lại</h4>
                                        ${html_error}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `);

                $("body").find("#order_modal_error").modal('show');

                setTimeout(function () {
                    $("body").find("#order_modal_error").modal('hide');
                    $("body").find("#order_modal_error").remove();
                }, 5000);
            }
            
        },
        complete: function () {
            $this.find('span:first-child').show();
            $this.find('span:last-child').hide();
            $this.prop('disabled', false);
        }
    });
})