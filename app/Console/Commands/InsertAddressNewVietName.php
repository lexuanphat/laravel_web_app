<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class InsertAddressNewVietName extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'InsertAddressNewVietName';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tạo tỉnh thành Việt Nam mới';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $get_provinces = Storage::disk('local')->get('province.json');
        $get_wards = Storage::disk('local')->get('ward.json');

        $get_provinces = json_decode(json_encode(json_decode($get_provinces)), true);
        $get_wards = json_decode(json_encode(json_decode($get_wards)), true);

        \DB::beginTransaction();

        try {
            \DB::table("provinces")->insert($get_provinces);
           
            foreach(array_chunk($get_wards, 500) as $items) {
                \DB::table("wards")->insert($items);
            }
        
            \DB::commit();
            $this->alert('Đã chạy xong');
        } catch (\Exception $e) {
            \DB::rollback();
            $this->alert("Có lỗi {$e->getMessage()}");
        }

        
    }
}
