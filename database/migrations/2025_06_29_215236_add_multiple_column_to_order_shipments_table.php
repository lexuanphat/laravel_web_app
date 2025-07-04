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
            if(!Schema::hasColumn('order_shipments', 'height')) {
                $table->integer('height')->nullable()->after('shipping_fee');
            }
            if(!Schema::hasColumn('order_shipments', 'width')) {
                $table->integer('width')->nullable()->after('height');
            }
            if(!Schema::hasColumn('order_shipments', 'length')) {
                $table->integer('length')->nullable()->after('width');
            }
            if(!Schema::hasColumn('order_shipments', 'weight')) {
                $table->integer('weight')->nullable()->after('length');
            }
            if(!Schema::hasColumn('order_shipments', 'cod')) {
                $table->integer('cod')->nullable()->after('weight');
            }
            if(!Schema::hasColumn('order_shipments', 'note')) {
                $table->string('note')->nullable()->after('cod');
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
            if(Schema::hasColumn('order_shipments', 'height')) {
                $table->dropColumn('height');
            }
            if(Schema::hasColumn('order_shipments', 'width')) {
                $table->dropColumn('width');
            }
            if(Schema::hasColumn('order_shipments', 'length')) {
                $table->dropColumn('length');
            }
            if(Schema::hasColumn('order_shipments', 'weight')) {
                $table->dropColumn('weight');
            }
            if(Schema::hasColumn('order_shipments', 'cod')) {
                $table->dropColumn('cod');
            }
            if(Schema::hasColumn('order_shipments', 'note')) {
                $table->dropColumn('note');
            }
        });
    }
};
