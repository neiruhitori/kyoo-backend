<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OMProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $provinces = [
            ['country' => 'Oman', 'name' => 'Muscat', 'timezone' => 'GST'],
            ['country' => 'Oman', 'name' => 'Dhofar', 'timezone' => 'GST'],
            ['country' => 'Oman', 'name' => 'North Al Batinah', 'timezone' => 'GST'],
            ['country' => 'Oman', 'name' => 'Al Dakhiliyah', 'timezone' => 'GST'],
            ['country' => 'Oman', 'name' => 'South Al Sharqiyah', 'timezone' => 'GST'],
            ['country' => 'Oman', 'name' => 'Al Dhahirah', 'timezone' => 'GST'],
            ['country' => 'Oman', 'name' => 'South Al Batinah', 'timezone' => 'GST'],
        ];

        DB::table('provinces')->insert($provinces);
    }
}
