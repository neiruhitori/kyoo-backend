<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FreeExhibitionLicenseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('branch_types')->insert([
            'name' => 'Free Exhibition Service',
            'code' => 'FES',
            'is_premium' => false,
            'is_exhibition' => true
        ]);
    }
}
