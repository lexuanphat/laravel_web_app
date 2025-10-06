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
        Schema::create('fee_product_province', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('province_id');
            $table->unsignedBigInteger('product_id');
            $table->decimal('fee', 10, 2);
            $table->unsignedBigInteger('created_user_id')->nullable();
            $table->unsignedBigInteger('updated_user_id')->nullable();
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
        Schema::dropIfExists('fee_product_province');
    }
};
