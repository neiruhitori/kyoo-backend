<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AdditionalFeature;

class AdditionalFeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $features = [
            [
                'code' => 'WST',
                'name' => 'Web Signage TV',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'code' => 'PGS',
                'name' => 'Panggilan Suara',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'code' => 'VCR',
                'name' => 'Voice Recording',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'code' => 'CFD',
                'name' => 'Customer Feedback',
                'created_at' => date('Y-m-d H:i:s')
            ]
        ];

        foreach ($features as $feature) {
            AdditionalFeature::firstOrCreate(['code' => $feature['code']], $feature);
        }
    }
}
