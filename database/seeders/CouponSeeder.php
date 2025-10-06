<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CouponSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [];

        for ($i = 1; $i <= 250; $i++) {
            $type = fake()->randomElement(['PHAN_TRAM', 'TIEN']);

            $data[] = [
                'name' => fake()->words(3, true),
                'code' => strtoupper(Str::random(8)), // random code unique
                'type' => $type,
                'fee' => $type === 'PHAN_TRAM'
                    ? fake()->numberBetween(5, 100)     // từ 5% -> 50%
                    : fake()->numberBetween(10000, 200000), // từ 10k -> 200k
                'date_start_apply' => Carbon::now()->subDays(rand(0, 10))->toDateString(),
                'date_end_apply'   => Carbon::now()->addDays(rand(5, 60))->toDateString(),
                'created_user_id'  => 1,
                'updated_user_id'  => null,
                'created_at'       => now(),
                'updated_at'       => null,
            ];
        }

        \DB::table('coupon')->insert($data);
    }
}
