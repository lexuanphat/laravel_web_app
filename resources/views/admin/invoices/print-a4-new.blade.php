<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Hóa đơn bán hàng - SON01339</title>
    <style>
        * {
            box-sizing: border-box;
            font-family: Arial, sans-serif;
            font-size: 13px;
            line-height: 1.4;
        }

        body {
            background: #f5f5f5;
            margin: 0;
            padding: 0;
        }

        .invoice-container {
            width: 210mm;
            min-height: 297mm;
            padding: 15mm;
            margin: 10px auto;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .header {
            display: flex;
            justify-content: space-between;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }

        .title {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 30px 0;
        }

        .customer-section {
            display: grid;
            grid-template-columns: 1.5fr 1.5fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }

        .product-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .product-table th, .product-table td {
            border: 1px solid #333;
            padding: 8px;
            text-align: left;
        }

        .product-table th {
            background-color: #f9f9f9;
            font-weight: bold;
        }

        .summary-section {
            width: 40%;
            margin-left: auto;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            border-bottom: 1px solid #eee;
        }

        .summary-row.total {
            font-weight: bold;
            border-bottom: 2px solid #333;
            padding-top: 10px;
        }

        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 60px;
            text-align: center;
        }

        .sign-box {
            width: 30%;
        }

        /* Định dạng khi in */
        @media print {
            body {
                background: none;
            }
            .invoice-container {
                margin: 0;
                box-shadow: none;
                width: 100%;
            }
            @page {
                size: A4;
                margin: 10mm;
            }
            .product-table th {
                -webkit-print-color-adjust: exact;
                background-color: #f9f9f9 !important;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="header">
            <div class="company-info">
                <strong>DNTN Phú Hà</strong><br>
                Tổ 2, đường 30/4, ĐĐ, PQ, KG<br>
                0766890889<br>
                info@phuhafoods.com
            </div>
            <div class="invoice-meta">
                Mã đơn hàng: <strong>SON01339</strong><br>
                Ngày tạo: 28-08-2025
            </div>
        </div>

        <div class="title">Đơn hàng</div>

        <div class="customer-section">
            <div class="customer-col">
                <strong>Hóa đơn đến:</strong><br>
                Chị Hương<br>
                24c1 Kdc Nam Long - Phường Thạnh Lộc - Quận 12 - TP Hồ Chí Minh
            </div>
            <div class="customer-col">
                <strong>Giao hàng đến:</strong><br>
                Chị Hương<br>
                24c1 Kdc Nam Long - Phường Thạnh Lộc - Quận 12 - TP Hồ Chí Minh
            </div>
            <div class="customer-col contact-info">
                Điện thoại: 0878513993<br>
                Email:
            </div>
        </div>

        <table class="product-table">
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Mã sản phẩm</th>
                    <th>Tên sản phẩm</th>
                    <th>Đơn vị</th>
                    <th>Số lượng</th>
                    <th>Đơn giá</th>
                    <th>Chiết khấu</th>
                    <th>Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="text-align: center;">1</td>
                    <td>PVN105</td>
                    <td>tài liệu</td>
                    <td></td>
                    <td style="text-align: right;">1</td>
                    <td style="text-align: right;">0</td>
                    <td style="text-align: right;">0%</td>
                    <td style="text-align: right;">0</td>
                </tr>
            </tbody>
        </table>

        <div class="summary-section">
            <div class="summary-row">
                <span>Tổng số lượng</span>
                <span>1</span>
            </div>
            <div class="summary-row">
                <span>Tổng Tiền</span>
                <span>0</span>
            </div>
            <div class="summary-row">
                <span>VAT</span>
                <span>0</span>
            </div>
            <div class="summary-row">
                <span>Chiết khấu</span>
                <span>0</span>
            </div>
            <div class="summary-row">
                <span>Phí giao hàng</span>
                <span>0</span>
            </div>
            <div class="summary-row total">
                <span>Khách phải trả</span>
                <span>0</span>
            </div>
        </div>

        <div class="signature-section">
            <div class="sign-box">
                <strong>Người mua hàng</strong><br>
                (Ký, họ tên)
            </div>
            <div class="sign-box">
                <strong>Người giao hàng</strong><br>
                (Ký, họ tên)
            </div>
            <div class="sign-box">
                <strong>Kế toán trưởng</strong><br>
                (Ký, họ tên)
            </div>
        </div>
    </div>
</body>
</html>