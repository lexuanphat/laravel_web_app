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
        Schema::create('order_shipment_status_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_shipment_id');
        
            $table->string('status_code')->nullable();
            $table->string('status_text')->nullable();
            $table->timestamp('status_time');
            $table->json('raw_payload')->nullable();

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
        Schema::dropIfExists('order_shipment_status_logs');
    }
};
