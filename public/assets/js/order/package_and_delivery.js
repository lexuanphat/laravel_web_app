$("input[name='options']").on('change', function () {
    let $this = $(this);
    let id = $this.attr('id');
    $(".tab-content").find('.tab-pane.active').removeClass('active');

    $(".tab-content").find(`#tab-${id}`).addClass('active');

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
});

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