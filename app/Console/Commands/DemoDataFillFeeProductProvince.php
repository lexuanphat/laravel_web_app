<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class DemoDataFillFeeProductProvince extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'DemoDataFillFeeProductProvince';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $get_product_ids = DB::SELECT("
            SELECT id FROM products
        ");

        $get_province_ids = DB::SELECT("
            SELECT code as id FROM provinces
        ");

        $get_product_ids = collect($get_product_ids)->pluck('id')->toArray();
        $get_province_ids = collect($get_province_ids)->pluck('id')->toArray();

        $list_price = $this->_randomFee(10000, 100000, 100);

        $data_insert = [];

        foreach($get_product_ids as $key_product => $product_id) {
            foreach($get_province_ids as $key_province => $province_id) {
                $random_fee = $list_price[array_rand($list_price)];
                $data_insert[] = [
                    'product_id' => $product_id,
                    'province_id' => $province_id,
                    'fee' => round($random_fee),
                    'created_user_id' => 1,
                    'updated_user_id' => null,
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => null,
                ];
            }
        }

        if($data_insert) {
            DB::table("fee_product_province")->delete();
            DB::table("fee_product_province")->insert($data_insert);
        } 
    }

    private function _randomFee($min, $max, $count = 100){
        $prices = [];
        
            for ($i = 0; $i < $count; $i++) {
                // random giá trong khoảng min - max
                $prices[] = rand($min, $max);
            }
        
            return $prices;
    }
}
