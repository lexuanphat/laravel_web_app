<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        DB::statement("ALTER TABLE tanks MODIFY COLUMN type ENUM('thanh_pham','ra_chai','nhua','bon_ra') DEFAULT NULL");
        DB::statement("ALTER TABLE transfer_logs MODIFY COLUMN from_type_tanks ENUM('thanh_pham','ra_chai','nhua','bon_ra') DEFAULT NULL");
        DB::statement("ALTER TABLE transfer_logs MODIFY COLUMN to_type_tanks ENUM('thanh_pham','ra_chai','nhua','bon_ra') DEFAULT NULL");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tanks', function (Blueprint $table) {
            //
        });
    }
};
