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
        Schema::table('customers', function (Blueprint $table) {
            if(!Schema::hasColumn('customers', 'ward_text')) {
                $table->string('ward_text')->after('address');
            }
            if(!Schema::hasColumn('customers', 'district_text')) {
                $table->string('district_text')->after('ward_text');
            }
            if(!Schema::hasColumn('customers', 'province_text')) {
                $table->string('province_text')->after('district_text');
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
        Schema::table('customers', function (Blueprint $table) {
            if(Schema::hasColumn('customers', 'province_text')) {
                $table->dropColumn('province_text');
            }
            if(Schema::hasColumn('customers', 'district_text')) {
                $table->dropColumn('district_text');
            }
            if(Schema::hasColumn('customers', 'ward_text')) {
                $table->dropColumn('ward_text');
            }
        });
    }
};
