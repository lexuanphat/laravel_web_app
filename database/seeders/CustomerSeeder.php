<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = fake('vi_VN');

        $gender = [
            'male' => 0,
            'female' => 1,
        ];


        $count = 10000;

        $data_insert = [];

        $arr_gender = ['male' => 'male', 'female' => 'female'];
        
        for($i = 0; $i <= $count; $i++) {

            $get_gender = array_rand($arr_gender);

            $data_insert[] = [
                'code' => Customer::generateCodeCustomer(),
                'full_name' => $faker->name($get_gender),
                'email' => $faker->unique()->email(),
                'phone' => str_replace(['-', ' '],'', $faker->unique()->phoneNumber()),
                'gender' => $gender[$get_gender],
                'date_of_birth' =>$faker->date('Y-m-d'),
                'user_id' => 1,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => null,
            ];
        }

        foreach(array_chunk($data_insert, 500) as $items) {
            \DB::table("customers")->insert($items);
        }
    }
}
