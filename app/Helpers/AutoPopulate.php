<?php
namespace App\Helpers;

use App\Schedule;
use App\Department;
use App\Service;
use App\Workstation;

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
        Service::create([
            'branch_id' => $branch_id,
            'name' => 'Service 1'
        ]);

        // auto populate workstation
        $workstation = Workstation::create([
            'department_id' => $department->id,
            'name' => 'Counter 1',
            'label' => 'Counter 1',
            'display_id' => 'Counter 1',
        ]);
    }
}