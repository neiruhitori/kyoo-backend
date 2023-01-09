<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

use App\Branch;
use App\BranchType;
use App\IndustryCategory;
use App\Department;
use App\Service;
use App\Workstation;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected $branch, $department, $service, $workstation;

    public function setupOrganization($licenseType, $queueType)
    {
        $this->branch = $this->createBranch($licenseType, $queueType);
        $this->department = $this->createDepartment();
        $this->service = $this->createService();
        $this->workstation = $this->createWorkstation();
    }

    protected function createBranch($licenseType, $queueType)
    {
        $branchType = null;

        if ($licenseType == 'free' && $queueType == 'appointment') {
            $branchType = BranchType::factory()->free()->appointment();
        }

        if ($licenseType == 'free' && $queueType == 'onsite') {
            $branchType = BranchType::factory()->free()->onsite();
        }

        if ($licenseType == 'free' && $queueType == 'exhibition') {
            $branchType = BranchType::factory()->free()->exhibition();
        }

        $industryCategory = IndustryCategory::factory();

        return Branch::factory()
            ->for($branchType)
            ->for($industryCategory)
            ->create();
    }

    protected function createDepartment()
    {
        return Department::factory()
            ->state(['branch_id' => $this->branch->id])
            ->create();
    }

    protected function createService()
    {
        return Service::factory()
            ->state([
                'branch_id' => $this->branch->id,
                'department_id' => $this->department->id
            ])
            ->create();
    }

    protected function createWorkstation()
    {
        return Workstation::factory()
            ->state([
                'department_id' => $this->department->id
            ])
            ->create();
    }
}
