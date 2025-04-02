<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KWRegenciesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $regenciesMap = [
            'Al Asimah' => ['Kuwait City'],
            'Hawalli' => ['Hawalli','Salmiya'],
            'Al Farwaniyah' => ['Farwaniya'],
            'Al Ahmadi' => ['Ahmadi'],
            'Al Jahra' => ['Jahra'],
         ];
 
         $regencies = DB::table('provinces')->whereIn('name', array_keys($regenciesMap))->get()->keyBy('name');
     
         $arr = [];
         foreach ($regenciesMap as $provinceName => $regencyNames) {
             if (isset($regencies[$provinceName])) {
                 $provinceId = $regencies[$provinceName]->id;
                 foreach ($regencyNames as $regencyName) {
                     $arr[] = [
                         'province_id' => $provinceId,
                         'country' => 'Kuwait',
                         'name' => $regencyName
                     ];
                 }
             }
         }
 
         DB::table('regencies')->insert($arr);
    }
}
