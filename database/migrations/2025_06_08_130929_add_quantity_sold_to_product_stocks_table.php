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
        Schema::table('product_stocks', function (Blueprint $table) {
            if(!Schema::hasColumn('product_stocks', 'quantity_sold')) {
                $table->unsignedBigInteger('quantity_sold')->default(0)->comment('SL đã bán')->after('available_quantity');
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
        Schema::table('product_stocks', function (Blueprint $table) {
            if(Schema::hasColumn('product_stocks', 'quantity_sold')) {
                $table->dropColumn('quantity_sold');
            }
        });
    }
};
