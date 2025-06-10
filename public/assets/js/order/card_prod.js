$("#store_id").change(function () {
    CARD_PRODUCT.find('tbody').empty();
    ELEMENTS_PRODUCT.empty_prod.show();
    ELEMENTS_PRODUCT.table_product.addClass('d-none');
    ELEMENTS_PRODUCT.select_find_prod.select2('open');
    ELEMENTS_RESULT_PRODUCT.input_total_balance.val(0);
    ELEMENTS_RESULT_PRODUCT.customer_paid_total.val(0);
    ELEMENTS_RESULT_PRODUCT.customer_has_paid_total.val(0);
    ELEMENTS_RESULT_PRODUCT.discount_total.val(0);
    ELEMENTS_RESULT_PRODUCT.total_end.val(0);
    ELEMENTS_RESULT_PRODUCT.label_total_quantity_order.text(0);
});
const CARD_PRODUCT = $("#card_info_product");
const RESULT_PRODUCT = $("#result_info_product");

const ELEMENTS_PRODUCT = {
    select_find_prod: CARD_PRODUCT.find('#info_product'),
    table_product: CARD_PRODUCT.find('#table_product'),
    empty_prod: CARD_PRODUCT.find("#empty_prod"),
    btn_infor_prod: CARD_PRODUCT.find('#btn_infor_product'),
};
const ELEMENTS_RESULT_PRODUCT = {
    label_total_quantity_order: RESULT_PRODUCT.find("#cnt_total_product"),
    input_total_balance: RESULT_PRODUCT.find("#total"),
    discount_total: RESULT_PRODUCT.find("#discount_total"),
    discount_total_money: RESULT_PRODUCT.find("#discount_total_money"),
    customer_paid_total: RESULT_PRODUCT.find("#customer_paid_total"),
    customer_has_paid_total: RESULT_PRODUCT.find("#customer_has_paid_total"),
    total_end: RESULT_PRODUCT.find("#total_end"),
};

ELEMENTS_PRODUCT.select_find_prod.select2({
    closeOnSelect: false,
    minimumInputLength: 3,
    language: "vi",
    ajax: {
        url: CARD_PRODUCT.attr('data-route'),
        delay: 250,
        type: 'GET',
        data: function(params){
            var query = {
                search: params.term,
                page: params.page || 1,
                store_id: $("#store_id").val(),
            }

            return query;
        },
        processResults: function(response, params){
            params.page = params.page || 1;
        
            return {
                results: response.data.data,
                pagination: {
                    more: response.data.current_page < response.data.last_page
                }
            };
        },
    },
    templateResult: formatRepoProduct,
    templateSelection: formatRepoSelection
}).on("select2:select", function(e){
    let $this = $(this);
    let data = $this.select2('data')[0];
    ELEMENTS_PRODUCT.empty_prod.hide();
    ELEMENTS_PRODUCT.table_product.removeClass('d-none');
    $this.val("").trigger("change");
    let index = ELEMENTS_PRODUCT.table_product.find('tbody tr').length;
    ELEMENTS_PRODUCT.table_product.find('tbody').append(renderItem(data, index));
    $('[data-toggle="touchspin"], .touchspin').TouchSpin();

    data_prod.push({
        name: data.name,
        price: Number(data.price),
        quantity: 1,
        total: Number(data.price) * 1,
    });

    calculateTotalProduct();
});

ELEMENTS_PRODUCT.btn_infor_prod.click(function(e){
    e.preventDefault();
    ELEMENTS_PRODUCT.select_find_prod.select2('open');
})

CARD_PRODUCT.on('click', '.delete_item', function () {
    let $this = $(this);
    let index = $this.data('index');
    $this.parents('.product_item').remove();
    if(ELEMENTS_PRODUCT.table_product.find('tbody tr').length === 0) {
        ELEMENTS_PRODUCT.empty_prod.show();
        ELEMENTS_PRODUCT.table_product.addClass('d-none');
        ELEMENTS_PRODUCT.select_find_prod.select2('open');
        ELEMENTS_RESULT_PRODUCT.customer_paid_total.val(0);

        if ($("input[name='options']:checked").val() == 1) {
            $("#cod").val(0);
            $("#gam").val(0);
            $("#length").val(0);
            $("#width").val(0);
            $("#height").val(0);
            $("input[name='options']").prop("checked", false);
            $("#option-transport").empty();
            $("#left").hide();
        }
    }
    else {
        
        if ($("input[name='options']:checked").val() == 1) {
            $("#cod").val(0);
            $("#gam").val(0);
            $("#length").val(0);
            $("#width").val(0);
            $("#height").val(0);
            $("input[name='options'][value='1']").trigger('change');
        }
        changeHtml();
    }

    data_prod.splice(index, 1);
    calculateTotalProduct();
})

CARD_PRODUCT.on('keyup', '.input_discount', function(){
    let $this = $(this);
    let value = Number($this.val());
    let product_item = $this.parents('.product_item');
    let quantity = Number(product_item.find('.quantity').find('input').val());
    let parent_discount = $this.parents('.parent_discount');
    let index = parent_discount.data('index');
    
    let name_radio = parent_discount.data('name');
    let price = Number(parent_discount.data('price')) * quantity;
    let is_choose_discount = $(`[name='${name_radio}']:checked`).val();
    let total = 0;
    let unit = "";
    if(parseInt(is_choose_discount) === choose_discount.percent) {
        unit = "%";
        if(value > 100) {
            $this.val(100);
            value = 100;
        }

        let total_discount = (price * value) / 100;
        total = price - total_discount;

        if(value > 0) {
            parent_discount.next().find('span').text((total_discount * -1).toLocaleString('vi'));
            parent_discount.next().show();
        } else {
            parent_discount.next().find('span').empty();
            parent_discount.next().hide();
        }

    } else {
        unit = "đ";
        parent_discount.next().find('span').empty();
        parent_discount.next().hide();

        if(value > price) {
            $this.val(price)
            value = price;
        }
        total = price - value;
    }

    $this.val(value.toLocaleString('vi'));
    product_item.find('.total_price').find('div').text(total.toLocaleString('vi'))
    product_item.find('span.unit').text(unit);

    data_prod[index].total = total;
    calculateTotalProduct();
}).on('blur', '.input_discount', function () {
    let $this = $(this);
    if($this.val() === "" || $this.val() < 0) {
        $this.val(0);
    }
})

CARD_PRODUCT.on('change', '.btn_discount', function () {
    let $this = $(this);
    let product_item = $this.parents('.product_item');
    let parent_discount = $this.parents('.parent_discount');
    let quantity = Number(product_item.find('.quantity').find('input').val());
    let price = Number(parent_discount.data('price')) * quantity;
    let input_discount = parent_discount.find('.input_discount').val(0).trigger("change");
    product_item.find('.total_price').find('div').text(Number(price).toLocaleString('vi'));
    let unit = "";
    if(parseInt($this.val()) === choose_discount.percent) {
        unit = "%";
    } else {
        unit = "đ";
        parent_discount.next().find('span').empty();
        parent_discount.next().hide();
    }
    product_item.find('span.unit').text(unit);
})
CARD_PRODUCT.on('change', '.product_quantity', function (e) {
    e.preventDefault();
    let $this = $(this);
    let index = $this.data('index');
    let quantity = Number($this.val());
    let product_item = $this.parents('.product_item');
    let parent_discount = product_item.find('.parent_discount')
    let price = Number(parent_discount.data('price'));
    parent_discount.find('.input_discount').val(0).trigger('keyup');
    product_item.find('.total_price > div').text(Number(price * quantity).toLocaleString('vi'));

    data_prod[index].quantity = quantity;
    data_prod[index].total = Number(price * quantity);
    calculateTotalProduct();

    if ($("input[name='options']:checked").val() == 1) {
        $("#cod").val(0);
        $("#gam").val(0);
        $("#length").val(0);
        $("#width").val(0);
        $("#height").val(0);
        $("input[name='options'][value='1']").trigger('change');
    } else if ($("input[name='options']:checked").val() == 2) {
        let items = ELEMENTS_PRODUCT.table_product.find('tbody tr.product_item');
        let weight = 0;
        $.each(items, function () { 
            let $this = $(this);
            let units = $this.attr('data-units');
            weight += Number(JSON.parse(units).weight) * $this.find('.product_quantity').val();
        });


        let unit_suggest = suggestCubeDimensions(weight);
        if (!$("#length").val() || $("#length").val() === '0') {
            $("#length").val(unit_suggest.length.toLocaleString('vi'));
        }
        if (!$("#height").val() || $("#height").val() === '0') {
            $("#height").val(unit_suggest.height.toLocaleString('vi'));
        }
        if (!$("#width").val() || $("#width").val() === '0') {
            $("#width").val(unit_suggest.width.toLocaleString('vi'));
        }
        if (!$("#gam").val() || $("#gam").val() === '0') {
            $("#gam").val(weight.toLocaleString('vi'));
        }
    }
})

/**
 * Javascript Tổng đơn hàng
 */
ELEMENTS_RESULT_PRODUCT.discount_total.keyup(function(e){
    let $this = $(this);

    ELEMENTS_RESULT_PRODUCT.customer_has_paid_total.val(0).trigger('keyup');

    let total_balance = Number(ELEMENTS_RESULT_PRODUCT.input_total_balance.val().replaceAll('.', ''));
    if(total_balance === 0) {
        let $this = $(this).val(0);
        return;
    }

    let value = Number($this.val().replaceAll('.', ''));

    if(value >= 100) {
        $this.val(100);
        value = 100;
    }

    if(value <= 0 || value === "") {
        $this.val(0);
        value = 0;
        ELEMENTS_RESULT_PRODUCT.discount_total_money.addClass('d-none');
        ELEMENTS_RESULT_PRODUCT.discount_total_money.find('span').text(0);
    }

    let discount_total_money = -(total_balance * value) / 100;
    
    if(discount_total_money !== -0){
        ELEMENTS_RESULT_PRODUCT.discount_total_money.removeClass('d-none');                
        ELEMENTS_RESULT_PRODUCT.discount_total_money.find('span').text(discount_total_money.toLocaleString('vi'))
    }

    let customer_paid_total = total_balance + discount_total_money;
    ELEMENTS_RESULT_PRODUCT.customer_paid_total.val(customer_paid_total.toLocaleString('vi'))

})
ELEMENTS_RESULT_PRODUCT.customer_has_paid_total.keyup(function(e){
    let $this = $(this);

    let value = Number($this.val().replaceAll('.', ''));

    let customer_paid_total = Number(ELEMENTS_RESULT_PRODUCT.customer_paid_total.val().replaceAll('.', ''));
    let total_balance = Number(ELEMENTS_RESULT_PRODUCT.input_total_balance.val().replaceAll('.', ''));
    if(total_balance === 0 && customer_paid_total === 0) {
        $this.val(0);
        value = 0;
        return;
    }

    if(customer_paid_total === 0) {
        $this.val(0);
        value = 0;
        ELEMENTS_RESULT_PRODUCT.total_end.val(0);
        return;
    }

    if(value === 0) {
        ELEMENTS_RESULT_PRODUCT.total_end.val(0);
        return;
    }

    let total_end = customer_paid_total - value;
    ELEMENTS_RESULT_PRODUCT.total_end.val(total_end.toLocaleString('vi'));

    $("#cod").val(total_end.toLocaleString('vi'));
})

function changeHtml(){
    let index = 1;
    let index_name_option = 0;
    ELEMENTS_PRODUCT.table_product.find('tbody tr.product_item').each(function(item){
        let $this = $(this);

        $this.find('.quantity').find('.product_quantity').data('index', index_name_option);
        console.log($this.find('.quantity').find('.product_quantity').data('index'));
        
        $this.find('td:last-child').find('.delete_item').data('index', index_name_option);
        $this.find('td:eq(0)').text(index);
        let parent_discount = $this.find('td:eq(4)').find('.parent_discount');
        parent_discount.data('name', `options_${index_name_option}`);
        parent_discount.data('index', index_name_option)
        
        let btn_left_input = parent_discount.find('div:eq(0)');
        btn_left_input.find('input.btn_discount').attr('name', `options_${index_name_option}`);
        btn_left_input.find('input.btn_discount').attr('id', `option_left_${index_name_option}`);
        btn_left_input.find('label').attr('for', `option_left_${index_name_option}`);

        let btn_right_input = parent_discount.find('div:eq(1)');
        btn_right_input.find('input.btn_discount').attr('name', `options_${index_name_option}`);
        btn_right_input.find('input.btn_discount').attr('id', `option_right_${index_name_option}`);
        btn_right_input.find('label').attr('for', `option_right_${index_name_option}`);
        
        index++;
        index_name_option++;
    })
}

function renderItem(data, index){    
    let image = data.image_url ? ASSETS.url_storage.replace(':image_url', data.image_url) : false;
    if(!image) {
        image = ASSETS.url_no_image;
    }   
    return `
        <tr class="product_item" data-product="${data.id}" data-units='${JSON.stringify({
            length: data.length,
            width: data.width,
            height: data.height,
            weight: data.weight,
        })}'>
            <td>
                <div class="text-center">${index+1}</div>
            </td>
            <td>
                <img src="${image}" alt="contact-img" title="contact-img" class="rounded me-1" height="64">
                <p class="m-0 d-inline-block align-middle font-16">
                    <a href="apps-ecommerce-products-details.html" class="text-body">${data.name}</a>
                    <br>
                    <small><b>Danh mục:</b> ${data.category.name}
                    </small>
                    <input type="hidden" name="product_name[]" value="${data.name}"/>
                </p>
            </td>
            <td class="quantity">
                <input data-toggle="touchspin" name="product_quantity[]" data-index=${index} data-bts-min="1" data-bts-max="${data.product_stock.available_quantity}" value="1" data-btn-vertical="true" type="text" class="form-control text-center product_quantity">
            </td>
            <td>
                <div class="text-center">${Number(data.price).toLocaleString('vi')} <input type="hidden" name="product_price[]" value="${data.price}"/></div>
            </td>
            <td>
                <div class="d-flex flex-wrap align-items-center parent_discount" data-index="${index}" data-name="options_${index}" data-price="${Number(data.price)}">
                    <div class="">
                        <input type="radio" class="btn-check btn_discount" name="options_${index}" id="option_left_${index}" autocomplete="off" value="1" checked>
                        <label class="btn btn-outline-primary" for="option_left_${index}">Giá trị</label>
                    </div>
                    <div class="">
                        <input type="radio" class="btn-check btn_discount" name="options_${index}" id="option_right_${index}" autocomplete="off" value="2">
                        <label class="btn btn-outline-primary" for="option_right_${index}">%</label>
                    </div>
                    <div class="col">
                        <div class="input-group bootstrap-touchspin bootstrap-touchspin-injected">
                            <input type="text" name="product_discount[]" oninput="this.value = this.value.replace(/[^0-9]/g, '');" class="form-control text-end input_discount" value="0">
                            <span class="input-group-addon bootstrap-touchspin-postfix input-group-append">
                                <span class="input-group-text unit">đ</span>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="discount text-end" style="display:none;">
                    <span class="small text-danger"></span>
                </div>
            </td>
            <td class="total_price">
                <div class="text-center">${Number(data.price).toLocaleString('vi')}</div>
            </td>
            <td class="bg-danger text-white">
                <a href="javascript:void(0);" class="action-icon delete_item" data-index=${index}> <i class="ri-close-line"></i></a>
            </td>
        </tr>
    `;
}

function formatRepoProduct(repo){
    if (repo.loading) {
        return repo.text;
    }
    let assets_storage = ASSETS.url_storage;
    let image = repo.image_url ? assets_storage.replace(':image_url', repo.image_url) : false;
    if(!image) {
        image = ASSETS.url_no_image;
    }
    var $container = $(`
        <div class="list-group-item d-flex justify-content-between align-items-center">
            <div>
                <div class="d-flex">
                    <div style="width: 50px; height: 50px;">
                        <img src="${image}" class="img-fluid">
                    </div>
                    <div class="ms-2">
                        <div class="fw-bold">${repo.name}</div>
                        <div class="">${repo.sku}</div>
                    </div>
                </div>
            </div>
            <div class="text-end">
                <span class="fw-bold">${Number(repo.price).toLocaleString("vi")}</span>
                <div class="">Tồn: <span class="link-danger">${Number(repo.product_stock.stock_quantity).toLocaleString('vi')}</span> | Có thể bán: <span class="link-danger">${Number(repo.product_stock.available_quantity).toLocaleString('vi')}</span></div>
            </div>
        </div>
    `);

    return $container;
    
}

function formatRepoSelection(repo){
   return repo.text;
}