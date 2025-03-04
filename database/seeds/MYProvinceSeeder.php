<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MYProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $provinces = [
            ['country' => 'Malaysia','name' => 'Federal Territory','timezone' => 'MYT'],
            ['country' => 'Malaysia','name' => 'Johor','timezone' => 'MYT'],
            ['country' => 'Malaysia','name' => 'Penang','timezone' => 'MYT'],
            ['country' => 'Malaysia','name' => 'Perak','timezone' => 'MYT'],
            ['country' => 'Malaysia','name' => 'Selangor','timezone' => 'MYT'],
            ['country' => 'Malaysia','name' => 'Sabah','timezone' => 'MYT'],
            ['country' => 'Malaysia','name' => 'Sarawak','timezone' => 'MYT'],
            ['country' => 'Malaysia','name' => 'Kedah','timezone' => 'MYT'],
            ['country' => 'Malaysia','name' => 'Negeri Sembilan','timezone' => 'MYT'],
            ['country' => 'Malaysia','name' => 'Pahang','timezone' => 'MYT'],
            ['country' => 'Malaysia','name' => 'Kelantan','timezone' => 'MYT'],
            ['country' => 'Malaysia','name' => 'Terengganu','timezone' => 'MYT'],
            ['country' => 'Malaysia','name' => 'Melaka','timezone' => 'MYT'],
        ];
    
        DB::table('provinces')->insert($provinces);
    }
}
