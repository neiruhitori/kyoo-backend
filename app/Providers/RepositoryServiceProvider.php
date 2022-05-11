<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Interfaces\BranchTypeRepositoryInterface;
use App\Repositories\BranchTypeRepository;

use App\Interfaces\ExhibitionRepositoryInterface;
use App\Repositories\ExhibitionRepository;

use App\Interfaces\DirectQueueRepositoryInterface;
use App\Repositories\DirectQueueRepository;

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
