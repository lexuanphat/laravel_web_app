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
        Schema::table('order_items', function (Blueprint $table) {
            if(!Schema::hasColumn('order_items', 'is_discount')) {
                $table->integer('is_discount')->default(0)->after('product_price')->comment('1 Là giá trị, 2 là phần trăm');
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
        Schema::table('order_items', function (Blueprint $table) {
            if(Schema::hasColumn('order_items', 'is_discount')) {
                $table->dropColumn('is_discount');
            }
        });
    }
};
