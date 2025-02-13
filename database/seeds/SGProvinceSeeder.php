<?php

namespace Database\Seeders;

use App\Models\SGProvince;
use Illuminate\Database\Seeder;

class SGProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    $provinces = [
        ['name' => 'Central Region'],
        ['name' => 'East Region'],
        ['name' => 'North Region'],
        ['name' => 'North-East Region'],
        ['name' => 'West Region']
    ];

        SGProvince::insert($provinces);
    }
}
