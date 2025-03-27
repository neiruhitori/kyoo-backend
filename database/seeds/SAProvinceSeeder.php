<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SAProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $provinces = [
            ['country' => 'Saudi Arabia', 'name' => 'Riyadh', 'timezone' => 'AST'],
            ['country' => 'Saudi Arabia', 'name' => 'Makkah', 'timezone' => 'AST'],
            ['country' => 'Saudi Arabia', 'name' => 'Al Madinah', 'timezone' => 'AST'],
            ['country' => 'Saudi Arabia', 'name' => 'Eastern Province', 'timezone' => 'AST'],
            ['country' => 'Saudi Arabia', 'name' => 'Asir', 'timezone' => 'AST'],
            ['country' => 'Saudi Arabia', 'name' => 'Tabuk', 'timezone' => 'AST'],
            ['country' => 'Saudi Arabia', 'name' => 'Hail', 'timezone' => 'AST'],
            ['country' => 'Saudi Arabia', 'name' => 'Al-Qassim', 'timezone' => 'AST']
        ];

        DB::table('provinces')->insert($provinces);
    }
}
