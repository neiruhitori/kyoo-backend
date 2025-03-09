<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AUProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $provinces = [
            ['country' => 'Australia','name' => 'New South Wales','timezone' => 'AEST'],
            ['country' => 'Australia','name' => 'Victoria','timezone' => 'AEST'],
            ['country' => 'Australia','name' => 'Queensland','timezone' => 'AEST'],
            ['country' => 'Australia','name' => 'South Australia','timezone' => 'ACST'],
            ['country' => 'Australia','name' => 'Western Australia','timezone' => 'AWST'],
            ['country' => 'Australia','name' => 'Tasmania','timezone' => 'AEST'],
            ['country' => 'Australia','name' => 'Northern Territory','timezone' => 'ACST'],
            ['country' => 'Australia','name' => 'Australian Capital Territory','timezone' => 'AEST'],
        ];
    
        DB::table('provinces')->insert($provinces);
    }
}
