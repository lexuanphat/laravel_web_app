<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $photo_path = storage_path('public/storage/products');
        $faker = fake('vi_VN');
        $data_insert = [];
        $count = 100000;

        if (!file_exists($photo_path)) {
            mkdir($photo_path, 0777, true);
        }

        $categories = \DB::SELECT("SELECT id FROM categories");
        $categories = array_column($categories, 'id');
        $list_price = [10000, 20000, 30000, 40000, 50000, 60000, 70000, 80000, 90000, 100000, 200000, 300000, 400000, 500000];

        for($i = 1; $i <= $count; $i++) {
            $code = str_pad($i, 8, '0', STR_PAD_LEFT);
            $data_insert[] = [
                'name' => "Sản phẩm thứ ".$i,
                'code' => "SKU-SP-{$code}",
                'sku' => "SKU-SP-{$code}",
                'price' => $list_price[array_rand($list_price)],
                'desc' => $faker->randomHtml(4,4),
                'image_url' => $faker->imageUrl(),
                'category_id' => $categories[array_rand($categories)],
                'user_id' => 1,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => null,
            ];
        }

        foreach(array_chunk($data_insert, 500) as $item) {
            \DB::table('products')->insert($item);
        }
    }
}
