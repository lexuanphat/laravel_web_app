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
        Schema::dropIfExists('orders');
        
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->unsignedBigInteger('customer_id')->nullable();

            $table->string('customer_phone')->nullable();
            $table->string('customer_full_name')->nullable();
            $table->text('customer_address')->nullable();

            $table->unsignedBigInteger('store_id');

            $table->decimal('total_price', 15, 2)->default(0)->comment('Tổng tiền'); // tổng tiền sp
            $table->decimal('total_discount', 5, 2)->default(0)->comment('% giảm / tổng tiền'); // %
            $table->decimal('total_amount', 15, 2)->default(0)->comment('Khách hàng phải trả'); // phải trả
            $table->decimal('paid_amount', 15, 2)->default(0)->comment('Khách hàng đã trả');

            $table->enum('shipping_fee_payer', ['shop', 'customer'])->default('customer')->comment('Người chịu tiền SHOP');

            $table->string('status')->nullable(); // pending, completed, canceled, etc.
            $table->enum('source', ['facebook','shoppe', 'tiktok', 'other'])->nullable(); // fb, web, zalo, etc.
            $table->text('note')->nullable();

            $table->unsignedBigInteger('user_id')->nullable(); // người tạo

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
