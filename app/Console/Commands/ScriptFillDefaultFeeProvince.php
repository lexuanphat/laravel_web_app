<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class ScriptFillDefaultFeeProvince extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ScriptFillFeeProvince';

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
        $find_default = DB::table("shipping_fees")->where("province_id", -1)->first();

        $is_create_user = DB::table("users")->where("email", "admin@gmail.com")->value('id');
        $is_create_user = $is_create_user ? $is_create_user : 1;

        if(!$find_default) {
            DB::table("shipping_fees")->insert([
                'province_id' => -1,
                'fee' => 30000,
                'created_user_id' => $is_create_user,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => null,
            ]);
        }
    }
}
