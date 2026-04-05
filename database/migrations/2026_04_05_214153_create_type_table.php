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
        Schema::create('list_type_report', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type_report', [
                'protein_level',
                'salt_level',
                'histamine_level',
                'acid_level',
                'amon_level',
                'color',
            ]);
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->bigInteger('create_user_id')->nullable();
            $table->bigInteger('update_user_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('list_type_report');
    }
};
