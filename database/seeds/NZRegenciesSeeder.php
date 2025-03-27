<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NZRegenciesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $regenciesMap = [
            'Auckland Region' => ['Auckland'],
            'Wellington Region' => ['Wellington'],
            'Canterbury' => ['Christchurch'],
            'Waikato' => ['Hamilton'],
            'Bay of Plenty' => ['Tauranga'],
            'Otago' => ['Dunedin','Queenstown'],
            'Manawatū-Whanganui' => ['Palmerston North'],
            'Hawke Bay' => ['Napier-Hastings'],
            'Nelson Region' => ['Nelson'],
            'Southland' => ['Invercargill'],
         ];
 
         $regencies = DB::table('provinces')->whereIn('name', array_keys($regenciesMap))->get()->keyBy('name');
     
         $arr = [];
         foreach ($regenciesMap as $provinceName => $regencyNames) {
             if (isset($regencies[$provinceName])) {
                 $provinceId = $regencies[$provinceName]->id;
                 foreach ($regencyNames as $regencyName) {
                     $arr[] = [
                         'province_id' => $provinceId,
                         'country' => 'New Zealand',
                         'name' => $regencyName
                     ];
                 }
             }
         }
 
         DB::table('regencies')->insert($arr);
    }
}
