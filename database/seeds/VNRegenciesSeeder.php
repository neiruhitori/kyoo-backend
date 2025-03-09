<?php

namespace Database\Seeders;

use App\Models\VNProvinces;
use App\Models\VNRegencies;
use Illuminate\Database\Seeder;

class VNRegenciesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    $regenciesData = [
        'An Giang' => ['Long Xuyen', 'Chau Doc'],
        'Ba Ria - Vung Tau' => ['Vung Tau', 'Ba Ria'],
        'Bac Giang' => ['Bac Giang City'],
        'Bac Kan' => ['Bac Kan City'],
        'Bac Lieu' => ['Bac Lieu City'],
        'Bac Ninh' => ['Bac Ninh City'],
        'Ben Tre' => ['Ben Tre City'],
        'Binh Dinh' => ['Quy Nhon'],
        'Binh Duong' => ['Thu Dau Mot'],
        'Binh Phuoc' => ['Dong Xoai'],
        'Binh Thuan' => ['Phan Thiet'],
        'Ca Mau' => ['Ca Mau City'],
        'Can Tho' => ['Can Tho'],
        'Da Nang' => ['Da Nang'],
        'Dak Lak' => ['Buon Ma Thuot'],
        'Dak Nong' => ['Gia Nghia'],
        'Dien Bien' => ['Dien Bien Phu'],
        'Dong Nai' => ['Bien Hoa'],
        'Dong Thap' => ['Cao Lanh'],
        'Gia Lai' => ['Pleiku'],
        'Ha Giang' => ['Ha Giang City'],
        'Ha Nam' => ['Phu Ly'],
        'Ha Tinh' => ['Ha Tinh City'],
        'Hai Phong' => ['Hai Phong'],
        'Hanoi' => ['Hanoi'],
        'Ho Chi Minh City' => ['Ho Chi Minh City'],
        'Hoa Binh' => ['Hoa Binh City'],
        'Hung Yen' => ['Hung Yen City'],
        'Khanh Hoa' => ['Nha Trang'],
        'Kien Giang' => ['Rach Gia', 'Ha Tien'],
        'Kon Tum' => ['Kon Tum City'],
        'Lai Chau' => ['Lai Chau City'],
        'Lam Dong' => ['Da Lat', 'Bao Loc'],
        'Lang Son' => ['Lang Son City'],
        'Lao Cai' => ['Lao Cai City'],
        'Long An' => ['Tan An'],
        'Nam Dinh' => ['Nam Dinh City'],
        'Nghe An' => ['Vinh City'],
        'Ninh Binh' => ['Ninh Binh City'],
        'Ninh Thuan' => ['Phan Rang - Thap Cham'],
        'Phu Tho' => ['Viet Tri'],
        'Phu Yen' => ['Tuy Hoa'],
        'Quang Binh' => ['Dong Hoi'],
        'Quang Nam' => ['Tam Ky', 'Hoi An'],
        'Quang Ngai' => ['Quang Ngai City'],
        'Quang Ninh' => ['Ha Long', 'Cam Pha', 'Mong Cai', 'Uong Bi'],
        'Quang Tri' => ['Dong Ha']
    ];


    $provinces = VNProvinces::whereIn('name', array_keys($regenciesData))->get()->keyBy('name');

    $regencies = [];
    foreach ($regenciesData as $provinceName => $regencyNames) {
        if (isset($provinces[$provinceName])) {
            $provinceId = $provinces[$provinceName]->id;
            foreach ($regencyNames as $regencyName) {
                $regencies[] = [
                    'province_id' => $provinceId,
                    'name' => $regencyName
                ];
            }
        }
    }

    VNRegencies::insert($regencies);
    }
}
