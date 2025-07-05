<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TokenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(\DB::table("tokens")->count() <= 0) {
            $user_id = \DB::table("users")->where("email", "admin@gmail.com")->value("id");
            \DB::table("tokens")->insert([
                [
                    'is_transport' => "GHN",
                    'token' => 'eac8f778-54b1-11f0-9b81-222185cb68c8',
                    'api' => 'https://dev-online-gateway.ghn.vn',
                    'user_id' => $user_id,
                    'created_at' => date("Y-m-d H:i:s"),
                ],
                [
                    'is_transport' => "GHTK",
                    'token' => '9BwVGsTLSSImlBJVas4faJWnFw3J4jvYjQAFSk',
                    'api' => 'https://services-staging.ghtklab.com',
                    'user_id' => $user_id,
                    'created_at' => date("Y-m-d H:i:s"),
                ],
            ]);
        }
    }
}
