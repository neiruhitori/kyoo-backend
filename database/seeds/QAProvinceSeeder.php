<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QAProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $provinces = [
            ['country' => 'Qatar', 'name' => 'Ad-Dawhah', 'timezone' => 'AST'],
            ['country' => 'Qatar', 'name' => 'Al Rayyan', 'timezone' => 'AST'],
            ['country' => 'Qatar', 'name' => 'Al Wakrah', 'timezone' => 'AST'],
            ['country' => 'Qatar', 'name' => 'Al Khor', 'timezone' => 'AST'],
            ['country' => 'Qatar', 'name' => 'Umm Salal', 'timezone' => 'AST'],
            ['country' => 'Qatar', 'name' => 'Madinat ash Shamal', 'timezone' => 'AST'],
            ['country' => 'Qatar', 'name' => 'Al Daayen', 'timezone' => 'AST']
        ];

        DB::table('provinces')->insert($provinces);
    }
}
