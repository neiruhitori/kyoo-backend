<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BNProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $provinces = [
            ['country' => 'Brunei Darussalam','name' => 'Brunei-Muara','timezone' => 'BNT'],
            ['country' => 'Brunei Darussalam','name' => 'Belait','timezone' => 'BNT'],
            ['country' => 'Brunei Darussalam','name' => 'Tutong','timezone' => 'BNT'],
            ['country' => 'Brunei Darussalam','name' => 'Temburong','timezone' => 'BNT']
        ];
    
        DB::table('provinces')->insert($provinces);
    }
}
