<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\BranchType;
use App\Models\LicenseType;

class CorporateLicenseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $corporate_license = LicenseType::where('name', 'Corporate')->first();
        
        if (!BranchType::where('code', 'CDQ')->first()) {
            BranchType::create([
                'code' => 'CDQ',
                'name' => 'Corporate Direct Queue',
                'is_premium' => true,
                'license_type_id' => $corporate_license->id,
                'is_appointment' => false,
                'is_direct_queue' => true,
                'is_exhibition' => false,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }

        if (!BranchType::where('code', 'CAQ')->first()) {
            BranchType::create([
                'code' => 'CAQ',
                'name' => 'Corporate Appointment Queue',
                'is_premium' => true,
                'license_type_id' => $corporate_license->id,
                'is_appointment' => true,
                'is_direct_queue' => false,
                'is_exhibition' => false,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }

        if (!BranchType::where('code', 'CEQ')->first()) {
            BranchType::create([
                'code' => 'CEQ',
                'name' => 'Corporate Exhibition Queue',
                'is_premium' => true,
                'license_type_id' => $corporate_license->id,
                'is_appointment' => false,
                'is_direct_queue' => false,
                'is_exhibition' => true,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }
    }
}
