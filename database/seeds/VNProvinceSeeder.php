<?php

namespace Database\Seeders;

use App\Models\VNProvinces;
use Illuminate\Database\Seeder;

class VNProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    $provinces = [
        'An Giang',
        'Ba Ria - Vung Tau',
        'Bac Giang',
        'Bac Kan',
        'Bac Lieu',
        'Bac Ninh',
        'Ben Tre',
        'Binh Dinh',
        'Binh Duong',
        'Binh Phuoc',
        'Binh Thuan',
        'Ca Mau',
        'Can Tho',
        'Da Nang',
        'Dak Lak',
        'Dak Nong',
        'Dien Bien',
        'Dong Nai',
        'Dong Thap',
        'Gia Lai',
        'Ha Giang',
        'Ha Nam',
        'Ha Tinh',
        'Hai Phong',
        'Hanoi',
        'Ho Chi Minh City',
        'Hoa Binh',
        'Hung Yen',
        'Khanh Hoa',
        'Kien Giang',
        'Kon Tum',
        'Lai Chau',
        'Lam Dong',
        'Lang Son',
        'Lao Cai',
        'Long An',
        'Nam Dinh',
        'Nghe An',
        'Ninh Binh',
        'Ninh Thuan',
        'Phu Tho',
        'Phu Yen',
        'Quang Binh',
        'Quang Nam',
        'Quang Ngai',
        'Quang Ninh',
        'Quang Tri'
    ];

    $data = [];
    foreach ($provinces as $province) {
        $data[] = ['name' => $province];
    }

        VNProvinces::insert($data);
    }
}
