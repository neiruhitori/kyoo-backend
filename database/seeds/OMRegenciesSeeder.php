<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OMRegenciesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $regenciesMap = [
            'Muscat' => ['Muscat'],
            'Dhofar' => ['Salalah'],
            'North Al Batinah' => ['Sohar'],
            'Al Dakhiliyah' => ['Nizwa'],
            'South Al Sharqiyah' => ['Sur'],
            'Al Dhahirah' => ['Ibri'],
            'South Al Batinah' => ['Rustaq'],
         ];
 
         $regencies = DB::table('provinces')->whereIn('name', array_keys($regenciesMap))->get()->keyBy('name');
     
         $arr = [];
         foreach ($regenciesMap as $provinceName => $regencyNames) {
             if (isset($regencies[$provinceName])) {
                 $provinceId = $regencies[$provinceName]->id;
                 foreach ($regencyNames as $regencyName) {
                     $arr[] = [
                         'province_id' => $provinceId,
                         'country' => 'Oman',
                         'name' => $regencyName
                     ];
                 }
             }
         }
 
         DB::table('regencies')->insert($arr);
    }
}
