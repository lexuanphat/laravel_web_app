<?php

namespace Database\Seeders;

use A6digital\Image\Facades\DefaultProfileImage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data_user_example = [
            [
                'full_name' => 'Le Xuan Phat',
                'email' => 'lexuanphat@gmail.com',
                'phone' => '0783635340',
                'password' => Hash::make('123123123'),
                'role' => 'admin',
                'created_at' => date("Y-m-d H:i:s"),
            ],
            [
                'full_name' => 'Quoc Thai',
                'email' => 'quocthai@gmail.com',
                'phone' => '0906365263',
                'password' => Hash::make('123123123'),
                'role' => 'admin',
                'created_at' => date("Y-m-d H:i:s"),
            ],
            [
                'full_name' => 'Ngoc Chau',
                'email' => 'ngocchau@gmail.com',
                'phone' => '0908547152',
                'password' => Hash::make('123123123'),
                'role' => 'admin',
                'created_at' => date("Y-m-d H:i:s"),
            ]
        ];

        $data_insert = [];
        foreach($data_user_example as $key => $user) {
            $link = "profiles/".Str::slug($user['full_name'])."_".strtotime(date("Ymd")).".png";
            $img = DefaultProfileImage::create($user['full_name']);
            Storage::disk('public')->put("/$link", $img->encode());
            $user['profile_default'] = $link;
            $data_insert[$key] = $user; 
        }

        \DB::table("users")->insert($data_insert);
    }
}
