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
        Schema::create('transfer_logs', function (Blueprint $table) {
            $table->id();

            // THÔNG TIN NGUỒN (FROM)
            $table->string('from_type'); // Lưu: 'vat' hoặc 'tank'
            $table->unsignedBigInteger('from_id');

            // THÔNG TIN ĐÍCH (TO)
            $table->string('to_type');   // Lưu: 'vat' hoặc 'tank'
            $table->unsignedBigInteger('to_id');

            $table->decimal('amount', 12, 2); // Số lượng lít

            // Trạng thái cá tại thời điểm đổ
            $table->enum('from_fish_status_vats', [
                'ca_dep', 'ca_binh_thuong', 'ca_xau', 'ca_cuc_xau'
            ])->nullable();
            // Trạng thái thùng tại thời điểm đổ
            $table->enum('from_status_vats', [
                'nguyen',              // Thùng nguyên
                'long_tron',           // Thùng đang long trộn
                'keo_rut',             // Thùng đang kéo rút
                'keo_ra_bon_chua',     // Thùng đang kéo ra bồn chứa
                'keo_ra_bon_chai',     // Thùng đang kéo ra bồn ra chai
                'ban_xac',             // Thùng chuẩn bị bán xác mắm
                'danh_nuoc_muoi'  
            ])->nullable();
            // Trạng thái bồn tại thời điểm đổ
            $table->enum('from_type_tanks', [
                'thanh_pham','ra_chai','nhua'
            ])->nullable();

            // Trạng thái cá tại thời điểm đổ
            $table->enum('to_fish_status_vats', [
                'ca_dep', 'ca_binh_thuong', 'ca_xau', 'ca_cuc_xau'
            ])->nullable();
            // Trạng thái thùng tại thời điểm đổ
            $table->enum('to_status_vats', [
                'nguyen',              // Thùng nguyên
                'long_tron',           // Thùng đang long trộn
                'keo_rut',             // Thùng đang kéo rút
                'keo_ra_bon_chua',     // Thùng đang kéo ra bồn chứa
                'keo_ra_bon_chai',     // Thùng đang kéo ra bồn ra chai
                'ban_xac',             // Thùng chuẩn bị bán xác mắm
                'danh_nuoc_muoi'  
            ])->nullable();
            // Trạng thái bồn tại thời điểm đổ
            $table->enum('to_type_tanks', [
                'thanh_pham','ra_chai','nhua'
            ])->nullable();

            // Lưu dung tích hiện tại của Bồn
            $table->decimal('from_tank_current_capacity', 12, 2)->default(0);
            $table->decimal('to_tank_current_capacity', 12, 2)->default(0);

            // Lưu dung tích hiện tại của Thùng
            $table->decimal('from_vat_current_capacity', 12, 2)->default(0);
            $table->decimal('to_vat_current_capacity', 12, 2)->default(0);

            $table->dateTime('created_at')->nullable();
            $table->bigInteger('create_user_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transfer_logs');
    }
};
