<?php
namespace App\Helpers;

use App\Schedule;
use App\User;
use App\Department;
use App\Service;
use App\Workstation;
use App\WorkstationVct;
use App\WorkstationService;
use Str;

class AutoPopulate {
    public static function create($branch_id)
    {
        // auto populate schedule
        $days = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
        foreach ($days as $key => $day) {
            Schedule::create([
                'branch_id' => $branch_id,
                'day' => $day,
                'start_time' => '08:00:00',
                'end_time' => '17:00:00',
                'status' => 'open'
            ]);
        }

        // auto populate department
        $department = Department::create([
            'branch_id' => $branch_id,
            'name' => 'Department 1'
        ]);

        // auto populate service
        $service = Service::create([
            'branch_id' => $branch_id,
            'department_id' => $department->id,
            'name' => 'Service 1'
        ]);

        // auto populate workstation
        $workstation = Workstation::create([
            'department_id' => $department->id,
            'name' => 'Counter 1',
            'label' => 'Counter 1',
            'display_id' => 'Counter 1',
        ]);

        // auto populate workstation vct
        $vct = User::create([
            'name' => "KY{$branch_id}_",
            'email' => null,
            'email_verified_at' => date('Y-m-d h:i:s'),
            'password' => Str::random(),
            'role' => 'cs',
            'branch_id' => $branch_id,
            'is_password_changed' => false
        ]);

        WorkstationVct::create([
            'workstation_id' => $workstation->id,
            'vct_id' => $vct->id
        ]);

        // auto populate workstation service
        WorkstationService::create([
            'workstation_id' => $workstation->id,
            'service_id' => $service->id,
            'priority' => 1
        ]);
    }
}