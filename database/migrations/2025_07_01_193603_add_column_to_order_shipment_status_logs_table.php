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
        Schema::table('order_shipment_status_logs', function (Blueprint $table) {
            if(!Schema::hasColumn('order_shipment_status_logs', 'note')) {
                $table->string('note')->after('status_time')->nullable();
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
        Schema::table('order_shipment_status_logs', function (Blueprint $table) {
            if(Schema::hasColumn('order_shipment_status_logs', 'note')) {
                $table->dropColumn('note');
            }
        });
    }
};
