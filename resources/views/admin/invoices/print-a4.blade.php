<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Đơn hàng {{$order->code}}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 13px;
            color: #000;
        }

        .hr{
            display: block;
            height: 2px;
            border: 0;
            border-top: 2px solid #333;
            margin: 1em 0;
            padding: 0;
        }

        .page {
            width: 100%;
        }

        .row {
            width: 100%;
            clear: both;
        }

        .col-left {
            width: 60%;
            float: left;
        }

        .col-right {
            width: 40%;
            float: right;
            text-align: right;
        }

        .company-name {
            font-weight: bold;
        }

        .line {
            border-bottom: 1px solid #999;
            margin: 15px 0;
        }

        h2 {
            text-align: center;
            margin: 20px 0;
            text-transform: uppercase;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table.info td {
            padding: 4px 0;
            vertical-align: top;
        }

        table.info td:last-child{
            text-align: right;
        }

        table.items th,
        table.items td {
            border: 1px solid #555;
            padding: 6px;
            font-size: 12px;
        }

        table.items th {
            text-align: center;
            font-weight: bold;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .summary {
            width: 55%;
            float: right;
            margin-top: 50px;
        }

        .summary td {
            padding: 6px;
            border-bottom: 1px solid #555;
        }

        .summary .total {
            font-weight: bold;
        }

        .signatures {
            width: 100%;
            text-align: center;
            margin-top: 80px;
        }

        .signature {
            width: 33%;
            float: left;
            font-size: 12px;
        }

        .signature p {
            margin-top: 60px;
        }
        
    </style>
</head>
<body>

<div class="page">

    <!-- HEADER -->
    <div class="row">
        <div class="col-left">
            <div class="company-name">DNTN Phú Hà</div>
            <div>Tổ 2, đường 30/4, ĐĐ, PQ, KG</div>
            <div>0766890889</div>
            <div>info@phuhafoods.com</div>
        </div>

        <div class="col-right">
            <div><strong>Mã đơn hàng:</strong> {{$order->code}}</div>
            <div><strong>Ngày tạo:</strong> {{date("d/m/Y", strtotime($order->created_at))}}</div>
        </div>
    </div>




    <!-- TITLE -->
   <div class="row">
    <div class="hr"></div>
    <h2 >Đơn hàng</h2>
   </div>

    <!-- CUSTOMER INFO -->
    <table class="info">
        <tr>
            <td width="33%">
                <strong>Hóa đơn đến:</strong><br>
                {{$order->customer->full_name}}<br>
                {{$order->customer->address}}, {{$order->customer->ward_name}}, {{$order->customer->province_name}}
            </td>
            <td width="33%">
                <strong>Giao hàng đến:</strong><br>
                Nguoi nhan hang<br>
                Dia chi nhan hang
            </td>
            <td width="33%">
                <strong>Điện thoại:</strong> {{$order->customer_phone}}<br>
                <strong>Email:</strong>{{$order->customer->email}}
            </td>
        </tr>
    </table>

    <br>

    <!-- ITEMS TABLE -->
    <table class="items">
        <thead>
        <tr>
            <th width="5%">STT</th>
            <th width="12%">Mã sản phẩm</th>
            <th>Tên sản phẩm</th>
            <th width="8%">Đơn vị</th>
            <th width="8%">Số lượng</th>
            <th width="12%">Đơn giá</th>
            <th width="10%">Chiết khấu</th>
            <th width="15%">Thành tiền</th>
        </tr>
        </thead>
        <tbody>
            @php $index = 1; @endphp
            @foreach($order->items as $item)
            <tr>
                <td class="text-center">{{$index++}}</td>
                <td>{{$item->product_code}}</td>
                <td>{{$item->product_name}}</td>
                <td class="text-center">X</td>
                <td class="text-center">{{number_format($item->product_quantity)}}</td>
                <td class="text-right">{{number_format($item->product_price)}}</td>
                <td class="text-center">
                    @if($item->is_discount === 1)
                    {{number_format($item->product_discount)}}
                    @else
                    {{number_format($item->product_discount)}}%
                    @endif
                </td>
                <td class="text-right">
                    {{number_format($item->product_total_discount)}}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- SUMMARY -->
    <table class="summary">
        <tr>
            <td>Tổng số lượng</td>
            <td class="text-right">
                {{$order->items->sum('product_quantity')}}
            </td>
        </tr>
        <tr>
            <td>Tổng tiền</td>
            <td class="text-right">
                {{number_format($order->total_price)}}
            </td>
        </tr>
        <tr>
            <td>VAT</td>
            <td class="text-right">
                X
            </td>
        </tr>
        <tr>
            <td>Chiết khấu</td>
            <td class="text-right">
                {{number_format($order->total_discount)}}%
            </td>
        </tr>
        <tr>
            <td>Phí giao hàng</td>
            <td class="text-right">
                @if($order->shipping_fee_payer !== 'shop')
                    {{number_format($order->shipments->shipping_fee)}}
                @else
                    0
                @endif
            </td>
        </tr>
        <tr class="total">
            <td>Khách phải trả</td>
            <td class="text-right">
                {{number_format(round($order->total_amount))}}
            </td>
        </tr>
        <tr class="total">
            <td>Khách đã trả</td>
            <td class="text-right">
                {{number_format(round($order->paid_amount))}}
            </td>
        </tr>
        <tr class="total">
            <td>Còn lại</td>
            <td class="text-right">
                {{number_format(round($order->total_amount - $order->paid_amount))}}
            </td>
        </tr>
    </table>

    <div style="clear: both"></div>

    <!-- SIGNATURES -->
    <div class="signatures">
        <div class="signature">
            <strong>Người mua hàng</strong><br>(Ký, họ tên)
            <p>&nbsp;</p>
        </div>
        <div class="signature">
            <strong>Người giao hàng</strong><br>(Ký, họ tên)
            <p>&nbsp;</p>
        </div>
        <div class="signature">
            <strong>Kế toán trưởng</strong><br>(Ký, họ tên)
            <p>&nbsp;</p>
        </div>
    </div>

</div>

</body>
</html>
