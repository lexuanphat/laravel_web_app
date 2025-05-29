<div class="card" id="card_info_product" data-route='{{route('admin.order.get_data_product')}}'>
    @include('admin.order.elements.product-items.header')
    @include('admin.order.elements.product-items.body')
    <hr>
    @include('admin.order.elements.product-items.footer')
</div>
@push('js_ready')
@php echo file_get_contents(asset('assets/js/order/card_prod.js')) @endphp
@endpush