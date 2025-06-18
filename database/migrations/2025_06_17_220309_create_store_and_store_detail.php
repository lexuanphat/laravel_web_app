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
        Schema::create('store_details', function (Blueprint $table) {
            $table->bigInteger('store_id');
            $table->text('address');
            $table->integer('transport_id');
            $table->json('response_transport');
            $table->string('is_transport');
        });

        Schema::table('stores', function(Blueprint $table){
            if(Schema::hasColumn('stores', 'address')) {
                $table->dropColumn('address');
            }
            if(Schema::hasColumn('stores', 'transport_id')) {
                $table->dropColumn('transport_id');
            }
            if(Schema::hasColumn('stores', 'transport_district_id')) {
                $table->dropColumn('transport_district_id');
            }
            if(Schema::hasColumn('stores', 'transport_ward_code')) {
                $table->dropColumn('transport_ward_code');
            }
            if(Schema::hasColumn('stores', 'is_transport')) {
                $table->dropColumn('is_transport');
            }
            if(Schema::hasColumn('stores', 'response_transport')) {
                $table->dropColumn('response_transport');
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
        Schema::dropIfExists('store_and_store_detail');
    }
};
