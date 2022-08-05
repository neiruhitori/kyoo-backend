<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Interfaces\BranchTypeRepositoryInterface;
use App\Repositories\BranchTypeRepository;
use App\Interfaces\ExhibitionRepositoryInterface;
use App\Repositories\ExhibitionRepository;
use App\Interfaces\DirectQueueRepositoryInterface;
use App\Repositories\DirectQueueRepository;
use App\Interfaces\AudioRecordingRepositoryInterface;
use App\Repositories\AudioRecordingRepository;
use App\Interfaces\ReportingDepartmentRepositoryInterface;
use App\Repositories\ReportingDepartmentRepository;
use App\Interfaces\ReportingServiceRepositoryInterface;
use App\Repositories\ReportingServiceRepository;
use App\Interfaces\ReportingWorkstationRepositoryInterface;
use App\Repositories\ReportingWorkstationRepository;
use App\Interfaces\ReportingVctRepositoryInterface;
use App\Repositories\ReportingVctRepository;
use App\Interfaces\ReportingServiceDistributionRepositoryInterface;
use App\Repositories\ReportingServiceDistributionRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ExhibitionRepositoryInterface::class, ExhibitionRepository::class);
        $this->app->bind(BranchTypeRepositoryInterface::class, BranchTypeRepository::class);
        $this->app->bind(DirectQueueRepositoryInterface::class, DirectQueueRepository::class);
        $this->app->bind(AudioRecordingRepositoryInterface::class, AudioRecordingRepository::class);
        $this->app->bind(ReportingDepartmentRepositoryInterface::class, ReportingDepartmentRepository::class);
        $this->app->bind(AudioRecordingRepositoryInterface::class, AudioRecordingRepository::class);
        $this->app->bind(ReportingServiceRepositoryInterface::class, ReportingServiceRepository::class);
        $this->app->bind(ReportingWorkstationRepositoryInterface::class, ReportingWorkstationRepository::class);
        $this->app->bind(ReportingVctRepositoryInterface::class, ReportingVctRepository::class);
        $this->app->bind(ReportingServiceDistributionRepositoryInterface::class, ReportingServiceDistributionRepository::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
