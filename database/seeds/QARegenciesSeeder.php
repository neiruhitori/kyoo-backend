<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QARegenciesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $regenciesMap = [
           'Ad-Dawhah' => ['Doha'],
           'Al Rayyan' => ['Al Rayyan'],
           'Al Wakrah' => ['Al Wakrah'],
           'Al Khor' => ['Al Khor'],
           'Umm Salal' => ['Umm Salal'],
           'Madinat ash Shamal' => ['Madinat ash Shamal'],
           'Al Daayen' => ['Al Daayen'],
        ];

        $regencies = DB::table('provinces')->whereIn('name', array_keys($regenciesMap))->get()->keyBy('name');
    
        $arr = [];
        foreach ($regenciesMap as $provinceName => $regencyNames) {
            if (isset($regencies[$provinceName])) {
                $provinceId = $regencies[$provinceName]->id;
                foreach ($regencyNames as $regencyName) {
                    $arr[] = [
                        'province_id' => $provinceId,
                        'country' => 'Qatar',
                        'name' => $regencyName
                    ];
                }
            }
        }

        DB::table('regencies')->insert($arr);
    }
}
