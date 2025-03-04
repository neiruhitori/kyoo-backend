<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TLProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $provinces = [
            ['country' => 'Timor-Leste','name' => 'Dili','timezone' => 'TLT'],
            ['country' => 'Timor-Leste','name' => 'Baucau','timezone' => 'TLT'],
            ['country' => 'Timor-Leste','name' => 'Cova Lima','timezone' => 'TLT'],
            ['country' => 'Timor-Leste','name' => 'Bobonaro','timezone' => 'TLT'],
            ['country' => 'Timor-Leste','name' => 'Viqueque','timezone' => 'TLT'],
            ['country' => 'Timor-Leste','name' => 'Lautém','timezone' => 'TLT'],
            ['country' => 'Timor-Leste','name' => 'Manufahi','timezone' => 'TLT'],
            ['country' => 'Timor-Leste','name' => 'Ainaro','timezone' => 'TLT'],
            ['country' => 'Timor-Leste','name' => 'Ermera','timezone' => 'TLT'],
        ];
    
        DB::table('provinces')->insert($provinces);
    }
}
