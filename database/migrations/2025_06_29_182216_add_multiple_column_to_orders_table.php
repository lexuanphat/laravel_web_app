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
            if(!Schema::hasColumn('orders', 'customer_ward')) {
                $table->string('customer_ward')->after('customer_address');
            }
            if(!Schema::hasColumn('orders', 'customer_district')) {
                $table->string('customer_district')->after('customer_ward');
            }
            if(!Schema::hasColumn('orders', 'customer_province')) {
                $table->string('customer_province')->after('customer_district');
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
            if(Schema::hasColumn('orders', 'customer_province')) {
                $table->dropColumn('customer_province');
            }
            if(Schema::hasColumn('orders', 'customer_district')) {
                $table->dropColumn('customer_district');
            }
            if(Schema::hasColumn('orders', 'customer_ward')) {
                $table->dropColumn('customer_ward');
            }
        });
    }
};
