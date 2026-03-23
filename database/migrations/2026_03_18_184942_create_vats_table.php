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
        Schema::create('vats', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Mã số thùng
            $table->decimal('max_capacity', 12, 2);
            $table->decimal('current_capacity', 12, 2)->default(0);
            $table->enum('status', [
                'nguyen',              // Thùng nguyên
                'long_tron',           // Thùng đang long trộn
                'keo_rut',             // Thùng đang kéo rút
                'keo_ra_bon_chua',     // Thùng đang kéo ra bồn chứa
                'keo_ra_bon_chai',     // Thùng đang kéo ra bồn ra chai
                'ban_xac',             // Thùng chuẩn bị bán xác mắm
                'danh_nuoc_muoi'       // Thùng đang đánh nước muối
            ])->default('nguyen');
            // Trạng thái cá theo 4 loại
            $table->enum('fish_status', [
                'ca_dep',        // Cá đẹp
                'ca_binh_thuong', // Cá bình thường
                'ca_xau',        // Cá xấu
                'ca_cuc_xau'     // Cá cực xấu
            ])->default('ca_binh_thuong');
            
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
        Schema::dropIfExists('vats');
    }
};
