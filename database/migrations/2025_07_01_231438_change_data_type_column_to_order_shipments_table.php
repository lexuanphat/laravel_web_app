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
        Schema::table('order_shipments', function (Blueprint $table) {
            if(Schema::hasColumn('order_shipments', 'shipping_partner_id')) {
                DB::statement('
                    ALTER TABLE `order_shipments` CHANGE `shipping_partner_id` `shipping_partner_id` INT NULL DEFAULT NULL COMMENT "Đối tượng vận chuyển: GHTK, GHN, VNPOST, Chành Xe, Shipper ngoài"
                ');
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
        Schema::table('order_shipments', function (Blueprint $table) {
            //
        });
    }
};
