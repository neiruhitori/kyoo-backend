<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class THProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $provinces = [
            ['country' => 'Thailand','name' => 'Bangkok','timezone' => 'ICT'],
            ['country' => 'Thailand','name' => 'Chiang Mai','timezone' => 'ICT'],
            ['country' => 'Thailand','name' => 'Chiang Rai','timezone' => 'ICT'],
            ['country' => 'Thailand','name' => 'Phuket','timezone' => 'ICT'],
            ['country' => 'Thailand','name' => 'Chonburi','timezone' => 'ICT'],
            ['country' => 'Thailand','name' => 'Songkhla','timezone' => 'ICT'],
            ['country' => 'Thailand','name' => 'Nakhon Ratchasima','timezone' => 'ICT'],
            ['country' => 'Thailand','name' => 'Udon Thani','timezone' => 'ICT'],
            ['country' => 'Thailand','name' => 'Khon Kaen','timezone' => 'ICT'],
            ['country' => 'Thailand','name' => 'Surat Thani','timezone' => 'ICT'],
            ['country' => 'Thailand','name' => 'Nakhon Si Thammarat','timezone' => 'ICT'],
            ['country' => 'Thailand','name' => 'Prachuap Khiri Khan','timezone' => 'ICT'],
            ['country' => 'Thailand','name' => 'Phra Nakhon Si Ayutthaya','timezone' => 'ICT'],
            ['country' => 'Thailand','name' => 'Trang','timezone' => 'ICT'],
            ['country' => 'Thailand','name' => 'Rayong','timezone' => 'ICT'],
            ['country' => 'Thailand','name' => 'Tak','timezone' => 'ICT'],
        ];
    
        DB::table('provinces')->insert($provinces);
    }
}
