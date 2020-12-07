<?php

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
                "isPremium" => false,
                "isAppointment" => true,
                "isDirectQueue" => false
            ],
            [
                "code" => "FDQ",
                "name" => "Free Direct Queue Service",
                "isPremium" => false,
                "isAppointment" => false,
                "isDirectQueue" => true
            ],
            [
                "code" => "FAE",
                "name" => "Free Appointment Event",
                "isPremium" => false,
                "isAppointment" => true,
                "isDirectQueue" => false
            ],
            [
                "code" => "FAS",
                "name" => "Free Appointment Space",
                "isPremium" => false,
                "isAppointment" => true,
                "isDirectQueue" => false
            ],
            [
                "code" => "PAE",
                "name" => "Premium Appointment Event",
                "isPremium" => true,
                "isAppointment" => true,
                "isDirectQueue" => false
            ],
            [
                "code" => "PA",
                "name" => "Premium Appointment Service",
                "isPremium" => true,
                "isAppointment" => true,
                "isDirectQueue" => false
            ],
            [
                "code" => "PDQ",
                "name" => "Premium Direct Queue",
                "isPremium" => true,
                "isAppointment" => false,
                "isDirectQueue" => true
            ],
            [
                "code" => "PAS",
                "name" => "Premium Appointment Space",
                "isPremium" => true,
                "isAppointment" => true,
                "isDirectQueue" => false
            ],
            [
                "code" => "PADQ",
                "name" => "Premium Appointment and Direct Queue",
                "isPremium" => true,
                "isAppointment" => true,
                "isDirectQueue" => true
            ],
        ];
        BranchType::insert($types);
    }
}
