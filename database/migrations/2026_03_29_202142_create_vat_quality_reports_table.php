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
        Schema::create('vat_quality_reports', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->dateTime('evaluation_date'); // Thời gian đánh giá
            $table->integer('vat_id');

            $table->tinyInteger('protein_level')->comment('Độ đạm');
            $table->tinyInteger('salt_level')->comment('Nồng độ muối');
            $table->tinyInteger('histamine_level')->comment('Histamin');
            $table->tinyInteger('acid_level')->comment('Admin');
            $table->tinyInteger('amon_level')->comment('Amon');
            $table->tinyInteger('color')->comment('Màu sắc');

            $table->integer('staff_id');
            $table->text('note')->nullable(); // Ghi chú thêm
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vat_quality_reports');
    }
};
