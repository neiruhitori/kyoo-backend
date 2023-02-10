<?php

namespace Database\Seeders;

use App\Models\AdditionalFeature;
use Illuminate\Database\Seeder;

class PromotionFeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        AdditionalFeature::create([
            'name' => 'Promosi',
            'code' => 'PMS',
        ]);
    }
}
