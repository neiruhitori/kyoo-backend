<?php

namespace Database\Seeders;

use App\Models\SGProvince;
use Illuminate\Database\Seeder;

class TimezoneSGseeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        SGProvince::whereNotNull('id')->update(['timezone' => 'SGT']);
    }
}
