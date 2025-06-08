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
        Schema::create('order_details', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('order_id')->comment('ID đơn hàng');
            $table->unsignedBigInteger('product_id')->nullable()->comment('ID sản phẩm (nếu có)');
            $table->string('product_name')->comment('Tên sản phẩm');

            $table->integer('quantity')->default(1)->comment('Số lượng');
            $table->decimal('price', 15, 2)->default(0)->comment('Đơn giá');
            $table->boolean('is_discount')->comment('Loại giảm giá (Gía trị hoặc %)');
            $table->decimal('discount', 15, 2)->default(0)->comment('Chiết khấu');
            $table->decimal('total_price', 15, 2)->default(0)->comment('Tổng tiền');

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
        Schema::dropIfExists('order_details');
    }
};
