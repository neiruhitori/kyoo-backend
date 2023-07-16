<?php

namespace Database\Seeders;

use App\Models\MenuFeatures;
use Illuminate\Database\Seeder;

class MenuFeaturesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        MenuFeatures::insert([
            [
                'name' => 'Monitoring Department',
                'code' => 'MD',
                'name_label' => 'Departemen',
                'group_label' => 'Monitoring',
                'route' => '/cs/monitoring/department',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Monitoring Service',
                'code' => 'ML',
                'name_label' => 'Layanan',
                'group_label' => 'Monitoring',
                'route' => '/cs/monitoring/service',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Configuration Table',
                'code' => 'CT',
                'name_label' => 'Konfigurasi Meja',
                'group_label' => '-',
                'route' => '/cs/workstation-service',
                'created_at' => date('Y-m-d H:i:s')
            ]
        ]);
    }
}
