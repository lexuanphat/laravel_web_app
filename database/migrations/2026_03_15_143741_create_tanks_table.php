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
        Schema::create('tanks', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            // Phân loại bồn trực tiếp
            $table->enum('type', [
                'thanh_pham', // Bồn chứa thành phẩm (nước mắm thô)
                'ra_chai',    // Bồn chứa ra chai
                'nhua'        // Bồn nhựa (tạm chứa)
            ]);
            $table->decimal('max_capacity', 12, 2);
            $table->decimal('current_capacity', 12, 2)->default(0);


           
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
        Schema::dropIfExists('tanks');
    }
};
