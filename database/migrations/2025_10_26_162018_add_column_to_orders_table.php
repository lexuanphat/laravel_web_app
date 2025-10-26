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
        Schema::table('orders', function (Blueprint $table) {
            if(!Schema::hasColumn('orders', 'user_order')) {
                $table->integer('user_order')->default(0)->comment('Người đặt hàng');
            }
            if(!Schema::hasColumn('orders', 'user_consignee')) {
                $table->integer('user_consignee')->default(0)->comment('Người nhận hàng');
            }
            if(!Schema::hasColumn('orders', 'user_payer')) {
                $table->integer('user_payer')->default(0)->comment('Người trả tiền');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            if(Schema::hasColumn('orders', 'user_order')) {
                $table->dropColumn('user_order');
            }
            if(Schema::hasColumn('orders', 'user_consignee')) {
                $table->dropColumn('user_consignee');
            }
            if(Schema::hasColumn('orders', 'user_payer')) {
                $table->dropColumn('user_payer');
            }
        });
    }
};
