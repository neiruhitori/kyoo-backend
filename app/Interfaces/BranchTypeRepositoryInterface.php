<?php

namespace App\Interfaces;

interface BranchTypeRepositoryInterface 
{
    public function getFreeExhibitionLicense();
    public function getFreeAppointmentLicense();
    public function getFreeOnsiteLicense();
}