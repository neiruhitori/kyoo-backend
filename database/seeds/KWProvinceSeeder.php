<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KWProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $provinces = [
            ['country' => 'Kuwait', 'name' => 'Al Asimah', 'timezone' => 'AST'],
            ['country' => 'Kuwait', 'name' => 'Hawalli', 'timezone' => 'AST'],
            ['country' => 'Kuwait', 'name' => 'Al Farwaniyah', 'timezone' => 'AST'],
            ['country' => 'Kuwait', 'name' => 'Al Ahmadi', 'timezone' => 'AST'],
            ['country' => 'Kuwait', 'name' => 'Al Jahra', 'timezone' => 'AST'],
        ];

        DB::table('provinces')->insert($provinces);
    }
}
