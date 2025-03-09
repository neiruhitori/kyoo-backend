<?php

namespace Database\Seeders;

use App\Models\Province;
use Illuminate\Database\Seeder;

class TimezoneIDseeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //adjust id with in the database!

        // WIB
       Province::whereIn('id', [11, 12, 13, 14, 15, 16, 17, 18, 19, 21, 31, 32, 33, 34, 35, 36])
        ->update(['timezone' => 'WIB']);

        // WITA
       Province::whereIn('id', [51, 52, 53, 61, 62, 63, 64, 65, 71, 72, 73, 74, 75, 76])
            ->update(['timezone' => 'WITA']);

        // WIT
       Province::whereIn('id', [81, 82, 91, 94])
            ->update(['timezone' => 'WIT']);

    }
}
