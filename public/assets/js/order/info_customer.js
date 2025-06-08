const CARD_INFO_CUSTOMER = $("#card_info_customer");
const ELEMENTS_INFO_CUSTOMER = {
    select_find_customer: CARD_INFO_CUSTOMER.find("#info_customer"),
    empty_customer: CARD_INFO_CUSTOMER.find("#empty"),
};

ELEMENTS_INFO_CUSTOMER.select_find_customer.select2({
    minimumInputLength: 3,
    language: "vi",
    ajax: {
        url: CARD_INFO_CUSTOMER.attr('data-route'),
        delay: 250,
        type: 'GET',
        data: function(params){
            var query = {
                search: params.term,
                page: params.page || 1
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
    templateResult: formatCustomer,
    templateSelection: formatCustomerSelection
}).on('select2:select', function(e){
    let $this = $(this);
    let data = $this.select2('data')[0];
    
    ELEMENTS_INFO_CUSTOMER.empty_customer.addClass('d-none');
    template = renderCustomer(data);
    ELEMENTS_INFO_CUSTOMER.select_find_customer.next(".select2-container").hide();
    ELEMENTS_INFO_CUSTOMER.empty_customer.after(template);
    $("#list_info_customer").find('li:first-child > a').attr('href', `tel:${data.phone}`);
    $("#list_info_customer").find('li:first-child > a').text(data.phone);
    $("#list_info_customer").find('li:last-child > span').text(data.address);
    $("#package_and_delivery").show();
    $("#list_button_action").show();
});

CARD_INFO_CUSTOMER.on('click', '#clear_customer', function (e) {
    e.preventDefault();
    ELEMENTS_INFO_CUSTOMER.select_find_customer.val("").trigger("change");
    $(".result > .data-customer").remove()
    ELEMENTS_INFO_CUSTOMER.empty_customer.removeClass('d-none');
    ELEMENTS_INFO_CUSTOMER.select_find_customer.next(".select2-container").show();
    ELEMENTS_INFO_CUSTOMER.select_find_customer.select2('open');
    $("#package_and_delivery").hide();
    $("#list_button_action").hide();
})

CARD_INFO_CUSTOMER.on('click', '#change_info_customer', function (e) {
    e.preventDefault();
    console.log("Change");
})

CARD_INFO_CUSTOMER.on('keyup', '#customer_address, #customer_phone', $.debounce(500, function () {
    let $this = $(this);

    if ($this.attr('id') === "customer_address") {
        $("#list_info_customer").find('li:last-child span').text($this.val());

        if ($("input[name='options']:checked").val() == 1) {
            $("input[name='options'][value='1']").trigger('change');
        }
    } else {
        $("#list_info_customer").find('li:first-child a').attr("href", `tel:${$this.val()}`)
        $("#list_info_customer").find('li:first-child a').text($this.val())
    }
    
}));

function formatCustomer(repo) {
    if (repo.loading) {
        return repo.text;
    }
    
    var $box_item = $(`
        <div class="customer-item row">
            <div class="col-auto">
                <div class="avatar-sm">
                    <img src="/assets/images/avatar-trang.jpg" alt="" class="rounded-circle img-thumbnail">
                </div>    
            </div>
            <div class="col">
                <div>${repo.full_name}</div>
                <div>${repo.phone}</div>
            </div>
        </div>
    `);

    return $box_item;
}

function formatCustomerSelection(repo) {
    return repo.text;
}

function renderCustomer(data) {
    return `
        <div class="data-customer">
            <div class="d-flex flex-wrap align-items-center gap-1">
                <strong class='text-primary'>${data.full_name}</strong> - <strong>${data.phone}</strong>
                <a href="javascript:;" id="clear_customer" class""><i class="ri-close-circle-fill fs-3"></i></a>
            </div>
            <hr>
            <div class="address">
                <div class="title d-flex flex-wrap gap-1 align-items-center">
                    <h5>Địa chỉ giao hàng</h5>
                    <a href="javascript:;" class="d-none" id="change_info_customer">Thay đổi</a>
                </div>
                <div class="info mt-1">
                    <div class="d-flex flex-column gap-2">
                        <input type="text" name="customer_full_name" class="form-control" id="customer_full_name" value="${data.full_name}" />
                        <input type="text" name="customer_phone" class="form-control" id="customer_phone" value="${data.phone}" />
                        <input type="text" name="customer_address" class="form-control" id="customer_address" value="${data.address}" />
                    </div>
                </div>
            </div>
        </div>
    `;
}
