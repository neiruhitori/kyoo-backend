<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AERegenciesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $regenciesMap = [
            'Abu Dhabi' => ['Abu Dhabi','Al Ain'],
            'Dubai' => ['Dubai'],
            'Sharjah' => ['Sharjah'],
            'Ajman' => ['Ajman'],
            'Ras Al Khaimah' => ['Ras Al Khaimah'],
            'Fujairah' => ['Fujairah'],
            'Umm Al Quwain' => ['Umm Al Quwain'],
         ];
 
         $regencies = DB::table('provinces')->whereIn('name', array_keys($regenciesMap))->get()->keyBy('name');
     
         $arr = [];
         foreach ($regenciesMap as $provinceName => $regencyNames) {
             if (isset($regencies[$provinceName])) {
                 $provinceId = $regencies[$provinceName]->id;
                 foreach ($regencyNames as $regencyName) {
                     $arr[] = [
                         'province_id' => $provinceId,
                         'country' => 'United Arab Emirates',
                         'name' => $regencyName
                     ];
                 }
             }
         }
 
         DB::table('regencies')->insert($arr);
    }
}
