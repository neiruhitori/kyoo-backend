<?php

namespace Database\Seeders;

use App\IndustryCategory;
use Illuminate\Database\Seeder;

class IndustryCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            [
                'name' => 'Healthcare',
                'icon' => '',
                'is_active' => 1
            ],
            [
                'name' => 'Beauty and Treatment',
                'icon' => '',
                'is_active' => 1
            ],
            [
                'name' => 'Public Service',
                'icon' => '',
                'is_active' => 1
            ],
            [
                'name' => 'Automotive',
                'icon' => '',
                'is_active' => 1
            ],
            [
                'name' => 'Education',
                'icon' => '',
                'is_active' => 1
            ],
            [
                'name' => 'Financial',
                'icon' => '',
                'is_active' => 1
            ],
            [
                'name' => 'FnB',
                'icon' => '',
                'is_active' => 1
            ],
            [
                'name' => 'Print and Design',
                'icon' => '',
                'is_active' => 1
            ],
            [
                'name' => 'Telecom',
                'icon' => '',
                'is_active' => 1
            ],
            [
                'name' => 'Entertainment',
                'icon' => '',
                'is_active' => 1
            ],
            [
                'name' => 'Event and Exhibition',
                'icon' => '',
                'is_active' => 1
            ]
        ];

        foreach ($categories as $category) {
            IndustryCategory::firstOrCreate(['name' => $category['name']], $category);
        }
    }
}
