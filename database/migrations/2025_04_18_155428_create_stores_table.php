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
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('contact_phone');
            $table->string('address');
            $table->bigInteger('transport_id')->nullable();
            $table->bigInteger('transport_district_id')->nullable();
            $table->bigInteger('transport_ward_code')->nullable();
            $table->string('is_transport')->nullable();
            $table->json('response_transport')->nullable();
            $table->foreignId('user_id')->constrained('users');
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
        });

        if(Schema::hasTable('users') && Schema::hasColumn('users', 'store_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreignId('store_id')->after('id')->nullable()->constrained('stores');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stores');
    }
};
