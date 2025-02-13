<?php

namespace Database\Seeders;

use App\Models\SGProvince;
use App\Models\SGRegencies;
use Illuminate\Database\Seeder;

class SGRegenciesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // You must insert data sg province first!
        $regenciesData = [
            'Central Region' => ['Orchard', 'Marina Bay', 'Novena', 'Newton', 'Bukit Merah'],
            'East Region' => ['Tampines', 'Bedok', 'Changi', 'Pasir Ris'],
            'North Region' => ['Woodlands', 'Yishun', 'Sembawang'],
            'North-East Region' => ['Serangoon', 'Hougang', 'Punggol', 'Sengkang'],
            'West Region' => ['Jurong', 'Bukit Batok', 'Clementi', 'Boon Lay']
        ];

        $provinces = SGProvince::whereIn('name', array_keys($regenciesData))->get()->keyBy('name');

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

       SGRegencies::insert($regencies);
    }
}
