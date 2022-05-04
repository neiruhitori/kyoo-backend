<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\BranchType;

class BranchTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = [
            [
                "code" => "FAS",
                "name" => "Free Appointment Service",
                "is_premium" => false,
                "is_appointment" => true,
                "is_direct_queue" => false
            ],
            [
                "code" => "FDQ",
                "name" => "Free Direct Queue Service",
                "is_premium" => false,
                "is_appointment" => false,
                "is_direct_queue" => true
            ],
            [
                "code" => "FAE",
                "name" => "Free Appointment Event",
                "is_premium" => false,
                "is_appointment" => true,
                "is_direct_queue" => false
            ],
            [
                "code" => "FAS",
                "name" => "Free Appointment Space",
                "is_premium" => false,
                "is_appointment" => true,
                "is_direct_queue" => false
            ],
            [
                "code" => "PAE",
                "name" => "Premium Appointment Event",
                "is_premium" => true,
                "is_appointment" => true,
                "is_direct_queue" => false
            ],
            [
                "code" => "PA",
                "name" => "Premium Appointment Service",
                "is_premium" => true,
                "is_appointment" => true,
                "is_direct_queue" => false
            ],
            [
                "code" => "PDQ",
                "name" => "Premium Direct Queue",
                "is_premium" => true,
                "is_appointment" => false,
                "is_direct_queue" => true
            ],
            [
                "code" => "PAS",
                "name" => "Premium Appointment Space",
                "is_premium" => true,
                "is_appointment" => true,
                "is_direct_queue" => false
            ],
            [
                "code" => "PADQ",
                "name" => "Premium Appointment and Direct Queue",
                "is_premium" => true,
                "is_appointment" => true,
                "is_direct_queue" => true
            ],
        ];
        BranchType::insert($types);
    }
}
