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
        Schema::create('map_order_tank_vat', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('order')->unique();
            $table->tinyInteger('target_type')->comment('1 là bồn, 2 là thùng');
            $table->bigInteger('target_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('map_order_tank_vat');
    }
};
