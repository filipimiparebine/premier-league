<?php

namespace App\Providers;

use App\Repositories\TeamRepository;
use App\Repositories\WeekRepository;
use App\Repositories\SeasonRepository;
use Illuminate\Support\ServiceProvider;
use App\Interfaces\TeamRepositoryInterface;
use App\Interfaces\WeekRepositoryInterface;
use App\Interfaces\SeasonRepositoryInterface;


class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->app->bind(TeamRepositoryInterface::class, TeamRepository::class);
        $this->app->bind(SeasonRepositoryInterface::class, SeasonRepository::class);
        $this->app->bind(WeekRepositoryInterface::class, WeekRepository::class);
    }
}
