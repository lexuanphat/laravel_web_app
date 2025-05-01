<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = fake('vi_VN');

        $count = 100;

        $data_insert = [];
        
        for($i = 0; $i <= $count; $i++) {

            $data_insert[] = [
                'code' => Category::generateCode(),
                'name' => $faker->unique()->domainName(),
                'user_id' => 1,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => null,
            ];
        }

        foreach(array_chunk($data_insert, 500) as $items) {
            \DB::table("categories")->insert($items);
        }
    }
}
