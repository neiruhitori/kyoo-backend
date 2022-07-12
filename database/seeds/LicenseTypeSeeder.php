<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LicenseType;

class LicenseTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        LicenseType::insert([
            [
                'name' => 'Umum',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Corporate',
                'created_at' => date('Y-m-d H:i:s')
            ]
        ]);
    }
}
