<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class USStateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         $provinces = [
            ['country' => 'United States', 'name' => 'New York', 'timezone' => 'ET'],
            ['country' => 'United States', 'name' => 'Georgia', 'timezone' => 'ET'],
            ['country' => 'United States', 'name' => 'Massachusetts', 'timezone' => 'ET'],
            ['country' => 'United States', 'name' => 'Florida', 'timezone' => 'ET'],
            ['country' => 'United States', 'name' => 'District of Columbia', 'timezone' => 'ET'],

            ['country' => 'United States', 'name' => 'Illinois', 'timezone' => 'CT'],
            ['country' => 'United States', 'name' => 'Texas', 'timezone' => 'CT'],
            ['country' => 'United States', 'name' => 'Louisiana', 'timezone' => 'CT'],
            ['country' => 'United States', 'name' => 'Minnesota', 'timezone' => 'CT'],

            ['country' => 'United States', 'name' => 'Colorado', 'timezone' => 'MT'],
            ['country' => 'United States', 'name' => 'Utah', 'timezone' => 'MT'],
            ['country' => 'United States', 'name' => 'New Mexico', 'timezone' => 'MT'],

            ['country' => 'United States', 'name' => 'Arizona', 'timezone' => 'MST'],

            ['country' => 'United States', 'name' => 'California', 'timezone' => 'PT'],
            ['country' => 'United States', 'name' => 'Washington', 'timezone' => 'PT'],
            ['country' => 'United States', 'name' => 'Oregon', 'timezone' => 'PT'],
            ['country' => 'United States', 'name' => 'Nevada', 'timezone' => 'PT'],

            ['country' => 'United States', 'name' => 'Alaska', 'timezone' => 'AKT'],

            ['country' => 'United States', 'name' => 'Hawaii', 'timezone' => 'HAT'],

            ['country' => 'United States', 'name' => 'American Samoa', 'timezone' => 'ST'],

            ['country' => 'United States', 'name' => 'Guam', 'timezone' => 'ChST'],
            ['country' => 'United States', 'name' => 'Northern Mariana Islands', 'timezone' => 'ChST'],
        ];

        DB::table('provinces')->insert($provinces);
    }
}
