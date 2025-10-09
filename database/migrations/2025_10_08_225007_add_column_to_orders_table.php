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
            if(!Schema::hasColumn('orders', 'coupon_id')) {
                $table->integer('coupon_id')->default(0);
            }
            if(!Schema::hasColumn('orders', 'total_apply_coupon')) {
                $table->integer('total_apply_coupon')->after('total_price')->default(0);
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
            if(Schema::hasColumn('orders', 'coupon_id')) {
                $table->dropColumn('coupon_id');
            }
            if(Schema::hasColumn('orders', 'total_apply_coupon')) {
                $table->dropColumn('total_apply_coupon');
            }
        });
    }
};
