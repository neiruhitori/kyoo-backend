<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class USCitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       $regenciesMap = [
            'New York' => ['New York City'],
            'Georgia' => ['Atlanta'],
            'Massachusetts' => ['Boston'],
            'Florida' => ['Miami'],
            'District of Columbia' => ['Washington D.C'],

            'Illinois' => ['Chicago'],
            'Texas' => ['Houston','Dallas'],
            'Louisiana' => ['New Orleans'],
            'Minnesota' => ['Minneapolis'],

            'Colorado' => ['Denver'],
            'Utah' => ['Salt Lake City'],
            'New Mexico' => ['Albuquerque'],

            'Arizona' => ['Phoenix'],

            'California' => ['Los Angeles','San Francisco'],
            'Washington' => ['Seattle'],
            'Oregon' => ['Portland'],
            'Nevada' => ['Las Vegas'],

            'Alaska' => ['Anchorage'],

            'Hawaii' => ['Honolulu'],

            'American Samoa' => ['Pago Pago'],

            'Guam' => ['Hagåtña'],
            'Northern Mariana Islands' => ['Saipan'],
         ];
 
         $regencies = DB::table('provinces')->whereIn('name', array_keys($regenciesMap))->get()->keyBy('name');
     
         $arr = [];
         foreach ($regenciesMap as $provinceName => $regencyNames) {
             if (isset($regencies[$provinceName])) {
                 $provinceId = $regencies[$provinceName]->id;
                 foreach ($regencyNames as $regencyName) {
                     $arr[] = [
                         'province_id' => $provinceId,
                         'country' => 'United States',
                         'name' => $regencyName
                     ];
                 }
             }
         }
 
         DB::table('regencies')->insert($arr);
    }
}
