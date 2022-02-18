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
                'is_active' => 't'
            ],
            [
                'name' => 'Beauty and Treatment',
                'icon' => '',
                'is_active' => 't'
            ],
            [
                'name' => 'Public Service',
                'icon' => '',
                'is_active' => 't'
            ],
            [
                'name' => 'Automotive',
                'icon' => '',
                'is_active' => 't'
            ],
            [
                'name' => 'Education',
                'icon' => '',
                'is_active' => 't'
            ],
            [
                'name' => 'Financial',
                'icon' => '',
                'is_active' => 't'
            ],
            [
                'name' => 'FnB',
                'icon' => '',
                'is_active' => 't'
            ],
            [
                'name' => 'Print and Design',
                'icon' => '',
                'is_active' => 't'
            ],
            [
                'name' => 'Telecom',
                'icon' => '',
                'is_active' => 't'
            ],
            [
                'name' => 'Entertainment',
                'icon' => '',
                'is_active' => 't'
            ],
            [
                'name' => 'Event and Exhibition',
                'icon' => '',
                'is_active' => 't'
            ]
        ];

        IndustryCategory::insert($categories);
    }
}
