<?php

namespace Database\Seeders;

use App\Models\VNProvinces;
use Illuminate\Database\Seeder;

class TimezoneVNseeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        VNProvinces::whereNotNull('id')->update(['timezone' => 'ICT']);
    }
}
