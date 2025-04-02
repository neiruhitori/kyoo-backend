<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AEProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $provinces = [
            ['country' => 'United Arab Emirates', 'name' => 'Abu Dhabi', 'timezone' => 'GST'],
            ['country' => 'United Arab Emirates', 'name' => 'Dubai', 'timezone' => 'GST'],
            ['country' => 'United Arab Emirates', 'name' => 'Sharjah', 'timezone' => 'GST'],
            ['country' => 'United Arab Emirates', 'name' => 'Ajman', 'timezone' => 'GST'],
            ['country' => 'United Arab Emirates', 'name' => 'Ras Al Khaimah', 'timezone' => 'GST'],
            ['country' => 'United Arab Emirates', 'name' => 'Fujairah', 'timezone' => 'GST'],
            ['country' => 'United Arab Emirates', 'name' => 'Umm Al Quwain', 'timezone' => 'GST'],
        ];

        DB::table('provinces')->insert($provinces);
    }
}
