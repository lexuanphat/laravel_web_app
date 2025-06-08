$("input[name='options']").on('change', async function () {
    $("input[name='options']").prop("disabled", true);
    let $this = $(this);
    let id = $this.attr('id');
    $(".tab-content").find('.tab-pane.active').removeClass('active');

    $(".tab-content").find(`#tab-${id}`).addClass('active');

    if ($this.val() == 1) {
        let weight = 0;
        try {
            let items = ELEMENTS_PRODUCT.table_product.find('tbody tr.product_item');
            let data = [];
            $.each(items, function () { 
                let $this = $(this);
                let units = $this.attr('data-units');
                weight += Number(JSON.parse(units).weight) * $this.find('.product_quantity').val();
                data.push({
                    product_id: $this.attr('data-product'),
                    quantity: $this.find('.product_quantity').val(),
                });
            });
            data = JSON.stringify(data);
            data.weight = weight;
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
            let data_fee = await apiGetFee("/admin/order/apiGetFee", data);
            if (data_fee.success) {
                html = "";
                let data = data_fee.data;
                response_transport_fee = {...data};
                delete data['data_send_get_fee'];
                $.each(data, function (key, item) {
                    image = "";
                    if (key === "GHN") {
                        image = "/assets/images/transport/logo-ghn-new.png";
                    }

                    let from_date = handleDateLeadTime(item.get_leadtime.leadtime_order.from_estimate_date);
                    let to_date = handleDateLeadTime(item.get_leadtime.leadtime_order.to_estimate_date);
                    
                    html += `
                        <div class="form-check mb-2 item d-flex align-items-center gap-2">
                            <input type="radio" id="${key}" name="${key}" value="${key}" class="form-check-input" checked>
                            <label class="form-check-label" for="${key}">
                                <img src="${image}" alt="image" class="img-fluid">
                                <span><b>Giao hàng nhanh</b></span>
                                <div class="leadtime">
                                    Ngày giao dự kiến: <b>${from_date} - ${to_date}</b>
                                </div>
                                <div class="fee">
                                    Cước phí: <b>${(item.fee.total).toLocaleString("vi")}</b>
                                </div>
                            </label>
                        </div>
                    `;
                    
                })
                $("#option-transport").html(html);
            }
            
        } catch (error) {
            $("body").find("#popup_show_err").remove();
            $("body").append(`
                <div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="popup_show_err">
                    <div class="modal-dialog modal-sm">
                        <div class="modal-content modal-filled bg-danger">
                            <div class="modal-body p-4">
                                <div class="text-center">
                                    <i class="ri-close-circle-line h1"></i>
                                    <h4 class="mt-2">Có lỗi!</h4>
                                    <p class="mt-3">${error.responseJSON.message}</p>
                                    <button type="button" class="btn btn-light my-2" data-bs-dismiss="modal">Đóng</button>
                                </div>
                            </div>
                        </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                </div><!-- /.modal --> 
                `);
            $("body").find("#popup_show_err").modal('show');
            $("input[name='options']").prop("disabled", false);
            return;
        }
    } else if ($this.val() == 2) {
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

    if (id === 'option4') {
        $("#left").addClass('d-none');
        $("#right").removeClass('d-none');
    } else if (id === 'option3') {
        $("#left").addClass('d-none');
        $("#right").addClass('d-none');
    } else {
        $("#left").removeClass('d-none');
        $("#right").removeClass('d-none');
    }

    $("input[name='options']").prop("disabled", false);
});

$("#delivery_method_fee").on('keyup', function (e) {

    if ($("[name='options']:checked").val() != 2) {
        return;
    }
    
    let $this = $(this);
    let fee = $this.val().replaceAll(".", "") * 1;
    let total_end = ELEMENTS_RESULT_PRODUCT.total_end.val().replaceAll(".", "") * 1;

    if ($("#payment_type_id").val() == 1) {
        $("#cod").val(Number(total_end).toLocaleString('vi'))
    } else {
        $("#cod").val(Number(total_end + fee).toLocaleString('vi'))
    }
    
})

$("#payment_type_id").change(function () {
    if ($("[name='options']:checked").val() == 2) {
        $(`[name='fee_transport']`).prop('checked', false);
        $(`[name='fee_transport'][value='${$(this).val()}']`).prop('checked', true);
    }

    if ($("[name='options']:checked").val() == 1 || $("[name='options']:checked").val() == 2) {
        let total_end = ELEMENTS_RESULT_PRODUCT.total_end.val().replaceAll(".", "") * 1;
        let fee = $("#delivery_method_fee").val().replaceAll(".", "") * 1;
        if ($(this).val() == 2) {
            $("#cod").val(Number(total_end + fee).toLocaleString('vi'))
        } else {
            $("#cod").val(Number(total_end).toLocaleString('vi'))
        }
    }
    
})

$(`[name='fee_transport']`).change(function () {
    $("#payment_type_id").val($(this).val()).trigger('change');
})

$(document).on('change', '#gam, #length, #height, #width', $.debounce(500, function(e){
    if ($("[name='options']:checked").val() == 1) {
        $("[name='options'][value='1']").trigger('change');
    }
}))

function suggestCubeDimensions(weightGram) {
    const side = Math.ceil(Math.cbrt(weightGram));

    return {
        length: 10,
        width: 10,
        height: 10,
    };
}

function handleDateLeadTime(value) {
    let date = value
    date = new Date(date);

    // Lấy ngày/tháng/năm
    let day = String(date.getUTCDate()).padStart(2, '0');
    let month = String(date.getUTCMonth() + 1).padStart(2, '0'); // Tháng bắt đầu từ 0
    let year = date.getUTCFullYear();

    date = `${day}/${month}/${year}`;

    return date
}

function apiGetFee(url, params, method = 'POST') {
    return $.ajax({
        url: url,
        type: method,
        data: {
            _token: $("[name='csrf-token']").attr('content'),
            data: params,
            store_id: $("#store_id").val(),
            customer_id: $("#info_customer").val(),
            length: $("#length").val().replaceAll('.', ''),
            height: $("#height").val().replaceAll('.', ''),
            width: $("#width").val().replaceAll('.', ''),
            weight: $("#gam").val().replaceAll('.', ''),
            address: $("#customer_address").val(),
        },
      });
}

$("input[type='radio'][name='loai_giao_hang']").change(function (e) {
            
    let $this = $(this);
    let value = $this.val();
    
    let data_transport = get_transport.filter(function (item) {
        return item.role === value;
    });

    $("select#partner_transport").val("");
    $("select#partner_transport option[value!='']").remove();
    $("select#partner_transport").select2({
        data: data_transport
    })
});