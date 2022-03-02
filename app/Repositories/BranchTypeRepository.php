<?php

namespace App\Repositories;

use App\Interfaces\BranchTypeRepositoryInterface;
use App\BranchType;

class BranchTypeRepository implements BranchTypeRepositoryInterface 
{
    public function getFreeExhibitionLicense()
    {
        return BranchType::where([
            ['is_premium', false],
            ['is_exhibition', true]
        ])
            ->orderBy('created_at')
            ->first();
    }

    public function getFreeAppointmentLicense()
    {
        return BranchType::where([
            ['is_premium', false],
            ['is_appointment', true]
        ])
            ->orderBy('created_at')
            ->first();
    }

    public function getFreeOnsiteLicense()
    {
        return BranchType::where([
            ['is_premium', false],
            ['is_direct_queue', true]
        ])
            ->orderBy('created_at')
            ->first();
    }
}