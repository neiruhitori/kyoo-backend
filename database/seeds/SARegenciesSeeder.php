<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SARegenciesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $regenciesMap = [
            'Riyadh' => ['Riyadh'],
            'Makkah' => ['Jeddah','Mecca'],
            'Al Madinah' => ['Medina'],
            'Eastern Province' => ['Dammam','Khobar'],
            'Asir' => ['Abha'],
            'Tabuk' => ['Tabuk'],
            'Hail' => ['Hail'],
            'Al-Qassim' => ['Buraidah'],
         ];
 
         $regencies = DB::table('provinces')->whereIn('name', array_keys($regenciesMap))->get()->keyBy('name');
     
         $arr = [];
         foreach ($regenciesMap as $provinceName => $regencyNames) {
             if (isset($regencies[$provinceName])) {
                 $provinceId = $regencies[$provinceName]->id;
                 foreach ($regencyNames as $regencyName) {
                     $arr[] = [
                         'province_id' => $provinceId,
                         'country' => 'Saudi Arabia',
                         'name' => $regencyName
                     ];
                 }
             }
         }
 
         DB::table('regencies')->insert($arr);
    }
}
