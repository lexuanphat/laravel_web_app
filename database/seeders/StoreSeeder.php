<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $address_fake = [
            'Số 12, Phường Bến Nghé, Quận 1, Thành phố Hồ Chí Minh, Việt Nam',
            'Số 25, Phường Tràng Tiền, Quận Hoàn Kiếm, Thành phố Hà Nội, Việt Nam',
            'Số 89, Phường Hòa Cường Bắc, Quận Hải Châu, Thành phố Đà Nẵng, Việt Nam',
            'Số 45, Phường Cái Khế, Quận Ninh Kiều, Thành phố Cần Thơ, Việt Nam',
            'Số 78, Phường Minh Khai, Quận Hồng Bàng, Thành phố Hải Phòng, Việt Nam',
            'Số 103, Phường Tân Phong, Thành phố Biên Hòa, Tỉnh Đồng Nai, Việt Nam',
            'Số 27, Phường 7, Thành phố Vũng Tàu, Tỉnh Bà Rịa - Vũng Tàu, Việt Nam',
            'Số 38, Phường Lộc Thọ, Thành phố Nha Trang, Tỉnh Khánh Hòa, Việt Nam',
            'Số 56, Phường 9, Thành phố Đà Lạt, Tỉnh Lâm Đồng, Việt Nam',
            'Số 112, Phường Tân Lập, Thành phố Buôn Ma Thuột, Tỉnh Đắk Lắk, Việt Nam',
            'Số 9, Phường Tây Sơn, Thành phố Pleiku, Tỉnh Gia Lai, Việt Nam',
            'Số 61, Phường Lê Lợi, Thành phố Vinh, Tỉnh Nghệ An, Việt Nam',
            'Số 80, Phường Trường Thi, Thành phố Thanh Hóa, Tỉnh Thanh Hóa, Việt Nam',
            'Số 15, Phường Nhơn Phú, Thành phố Quy Nhơn, Tỉnh Bình Định, Việt Nam',
            'Số 44, Phường 5, Thành phố Mỹ Tho, Tỉnh Tiền Giang, Việt Nam'
        ];

        $store_names_fake = [
            'Cửa Hàng Tiện Lợi Sài Gòn Xanh',
            'Shop Thời Trang Mùa Hè',
            'Nhà Sách Tri Thức Việt',
            'Tiệm Bánh Ngọt Hương Quê',
            'Siêu Thị Mini Gia Đình',
            'Điện Máy An Khang',
            'Cửa Hàng Nội Thất Phú Mỹ',
            'Shop Mỹ Phẩm Sen Hồng',
            'Tiệm Vàng Kim Long',
            'Cửa Hàng Hoa Tươi Thanh Xuân',
            'Hiệu Thuốc Minh Tâm',
            'Shop Đồ Ăn Vặt Cô Ba',
            'Trà Sữa 1989',
            'Cafe Sách Mộc Miên',
            'Cửa Hàng Đặc Sản Miền Trung'
        ];

        $phone_fake = [
            '0901234567', // MobiFone
            '0912345678', // Vinaphone
            '0987654321', // Viettel
            '0923456789', // Vietnamobile
            '0934567890', // MobiFone
            '0945678901', // Vinaphone
            '0976543210', // Viettel
            '0961122334', // Viettel
            '0322233445', // Viettel (đầu số mới)
            '0333344556', // Viettel
            '0354455667', // Viettel
            '0365566778', // Viettel
            '0376677889', // Viettel
            '0387788990', // Viettel
            '0398899001'  // Viettel
        ];

        $stores = [];

        foreach($store_names_fake as $k => $name) {
            $stores[] = [
                'name' => $name,
                'address' => $address_fake[$k],
                'contact_phone' => $phone_fake[$k],
                'user_id' => 1,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => null,
            ];
        }

        \DB::table("stores")->insert($stores);
    }
}
