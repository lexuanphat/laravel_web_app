$("#btn_order").click(function(e){
    e.preventDefault();
    let $this = $(this);
    $this.find('span:first-child').hide();
    $this.find('span:last-child').show();
    $this.prop('disabled', true);

    // thu thập dữ liệu khách mua hàng
    let get_customer = ELEMENTS_INFO_CUSTOMER.select_find_customer.select2('data');
    get_customer = get_customer && get_customer[0] ? get_customer[0] : [];

    if (get_customer.length === 0) {
        alert("Vui lòng chọn khách mua hàng");
        return;
    }

    let customer_id = get_customer.user_id;

    // Thu thập dữ liệu sản phẩm order
    let form_table_product = $("#form_table_product");
    let product_items = form_table_product.find('tr.product_item')
    let products = [];
    $.each(product_items, function (index, item) {
        let $this = $(this);
        products.push({
            product_id: $this.attr('data-product'),
            quantity: $this.find('input.product_quantity').val(),
            is_option: $this.find(`[name="options_${index}"]:checked`).val(),
            discount: $this.find('input.input_discount').val(),
        });
    })
    
    // Thu thập dữ liệu tổng tiền sản phẩm
    let result_info_product = $("#result_info_product");
    let results = {
        note: result_info_product.find("#note_total").val(),
        discount_total: result_info_product.find("#discount_total").val(),
        customer_has_paid_total: result_info_product.find("#customer_has_paid_total").val(),
    };
    
    // Thu thập dữ liệu chọn Loại giao hàng nào
    let element_package_and_delivery = $("#package_and_delivery");
    let package_and_delivery = {
        type: element_package_and_delivery.find("input[type='radio'][name='options']:checked").val(),
        cod: element_package_and_delivery.find("#cod").val(),
        gam: element_package_and_delivery.find("#gam").val(),
        length: element_package_and_delivery.find("#length").val(),
        height: element_package_and_delivery.find("#height").val(),
        require_transport_option: element_package_and_delivery.find("#require_transport_option").val(),
        note_transport: element_package_and_delivery.find("#note_transport").val(),
    };

    if (Number(package_and_delivery.type) === 1) {
        
    } else if (Number(package_and_delivery.type) === 2) {
        package_and_delivery.is_ship = $("[name='loai_giao_hang']:checked").val();
        package_and_delivery.ship_id = $("select#partner_transport").val();
        package_and_delivery.delivery_method_fee = $("#delivery_method_fee").val();
        package_and_delivery.fee_transport = $("[type='radio'][name='fee_transport']:checked").val();
    } else if (Number(package_and_delivery.type) === 3) {
        
    } else {

    }

    

    setTimeout(function(){
        $this.find('span:first-child').show();
        $this.find('span:last-child').hide();
        $this.prop('disabled', false);
    }, 200)
})