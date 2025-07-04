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
            if(Schema::hasColumn('customers', 'ward_text')) {
                $table->dropColumn('ward_text');
            }
            if(Schema::hasColumn('customers', 'district_text')) {
                $table->dropColumn('district_text');
            }
            if(Schema::hasColumn('customers', 'province_text')) {
                $table->dropColumn('province_text');
            }

            if(!Schema::hasColumn('customers', 'ward_code')) {
                $table->unsignedBigInteger('ward_code')->after('address');
            }
            if(!Schema::hasColumn('customers', 'district_code')) {
                $table->unsignedBigInteger('district_code')->after('ward_code');
            }
            if(!Schema::hasColumn('customers', 'province_code')) {
                $table->unsignedBigInteger('province_code')->after('district_code');
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
            //
        });
    }
};
