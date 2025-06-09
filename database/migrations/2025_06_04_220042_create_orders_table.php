<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id(); // id tự tăng

            $table->string('code_transport')->nullable()->unique()->comment('Mã đơn hàng từ DVVC');
            $table->string('code_order')->nullable()->unique()->comment('Mã đơn hàng');

            $table->unsignedBigInteger('store_id')->comment('ID cửa hàng');
            $table->unsignedBigInteger('customer_id')->comment('ID khách hàng');
            $table->string('full_name')->comment('Tên khách hàng');
            $table->string('phone')->comment('SĐT khách hàng');
            $table->text('address')->comment('Địa chỉ giao hàng');

            $table->decimal('total_product', 15, 2)->default(0)->comment('Tổng số sản phẩm');
            $table->decimal('total_price', 15, 2)->default(0)->comment('Tổng tiền');
            $table->decimal('total_discount', 15, 2)->default(0)->comment('Chiết khấu');
            $table->decimal('customer_paid_total', 15, 2)->default(0)->comment('Khách cần trả');
            $table->decimal('customer_has_paid_total', 15, 2)->default(0)->comment('Khách đã trả');

            $table->unsignedBigInteger('user_create_order')->nullable()->comment('Người tạo đơn');
            $table->string('source')->nullable()->comment('Nguồn bán hàng');

            $table->date('delivery_date')->nullable()->comment('Ngày giao hàng');
            $table->dateTime('create_date')->nullable()->comment('Ngày tạo đơn hàng');

            $table->boolean('delivery_method')->nullable()->comment('Phương thức giao hàng (Hãng vận chuyển, vận chuyển ngoài, nhận tại cửa hàng, giao hàng sau)');
            $table->enum('partner_transport_type', ['DVVC', 'SHIPPER', 'CHANH_XE'])->nullable()->comment('Đối tác vận chuyển');
            $table->integer('partner_transport_id')->nullable()->comment('Lưu ID của vận chuyển, nếu partner_transport_type = DVVC thì sẽ lưu -1');
            $table->decimal('delivery_method_fee', 10, 2)->default(0)->comment('Phí giao hàng');
            $table->enum('payer_fee', ['shop', 'customer'])->nullable()->comment('Người trả phí giao hàng');

            $table->decimal('cod', 15, 2)->default(0)->comment('Thu hộ COD');
            $table->integer('gam')->default(0)->comment('Khối lượng (gram)');
            $table->integer('height')->nullable()->comment('Chiều cao (cm)');
            $table->integer('width')->nullable()->comment('Chiều rộng (cm)');
            $table->integer('length')->nullable()->comment('Chiều dài (cm)');

            $table->enum('require_transport_option', ['KHONGCHOXEMHANG','CHOXEMHANG','CHOXEMHANGKHONGTHU'])->nullable()->comment('Yêu cầu vận chuyển');
            $table->string("status_transport")->nullable()->comment('Trạng thái đơn hàng của DVVC');
            $table->string("status_order")->nullable()->comment('Trạng thái đơn hàng');
            $table->text('note_transport')->nullable()->comment('Ghi chú đơn hàng DVVC');

            $table->text('note_order')->nullable()->comment('Ghi chú đơn hàng');

            $table->json('response_transport')->nullable()->comment('Phản hồi từ DVVC');
            $table->json('response_transport_hook')->nullable()->comment('Phản hồi từ DVVC');

            $table->unsignedBigInteger('user_id');
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
