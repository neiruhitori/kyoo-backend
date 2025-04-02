<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NZProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $provinces = [
            ['country' => 'New Zealand', 'name' => 'Auckland Region', 'timezone' => 'NZST'],
            ['country' => 'New Zealand', 'name' => 'Wellington Region', 'timezone' => 'NZST'],
            ['country' => 'New Zealand', 'name' => 'Canterbury', 'timezone' => 'NZST'],
            ['country' => 'New Zealand', 'name' => 'Waikato', 'timezone' => 'NZST'],
            ['country' => 'New Zealand', 'name' => 'Bay of Plenty', 'timezone' => 'NZST'],
            ['country' => 'New Zealand', 'name' => 'Otago', 'timezone' => 'NZST'],
            ['country' => 'New Zealand', 'name' => 'Manawatū-Whanganui', 'timezone' => 'NZST'],
            ['country' => 'New Zealand', 'name' => 'Hawke Bay', 'timezone' => 'NZST'],
            ['country' => 'New Zealand', 'name' => 'Nelson Region', 'timezone' => 'NZST'],
            ['country' => 'New Zealand', 'name' => 'Southland', 'timezone' => 'NZST'],
        ];

        DB::table('provinces')->insert($provinces);
    }
}
