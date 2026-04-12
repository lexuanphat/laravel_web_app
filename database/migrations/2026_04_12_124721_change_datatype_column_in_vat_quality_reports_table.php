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
        Schema::table('vat_quality_reports', function (Blueprint $table) {
            DB::statement("ALTER TABLE `vat_quality_reports` CHANGE `protein_level` `protein_level` VARCHAR(255) NOT NULL COMMENT 'Độ đạm'");
            DB::statement("ALTER TABLE `vat_quality_reports` CHANGE `salt_level` `salt_level` VARCHAR(255) NOT NULL COMMENT 'Nồng độ muối'");
            DB::statement("ALTER TABLE `vat_quality_reports` CHANGE `histamine_level` `histamine_level` VARCHAR(255) NOT NULL COMMENT 'Histamin'");
            DB::statement("ALTER TABLE `vat_quality_reports` CHANGE `acid_level` `acid_level` VARCHAR(255) NOT NULL COMMENT 'Acid'"); 
            DB::statement("ALTER TABLE `vat_quality_reports` CHANGE `amon_level` `amon_level` VARCHAR(255) NOT NULL COMMENT 'Amon'");
            DB::statement("ALTER TABLE `vat_quality_reports` CHANGE `color` `color` VARCHAR(255) NOT NULL COMMENT 'Màu sắc'");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vat_quality_reports', function (Blueprint $table) {
            //
        });
    }
};
