<?php

namespace App\Providers;

use App\Services\LeagueService;
use App\Services\FixtureGenerator;
use Illuminate\Support\ServiceProvider;
use App\Interfaces\LeagueServiceInterface;
use App\Interfaces\FixtureGeneratorInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(LeagueServiceInterface::class, LeagueService::class);
        $this->app->bind(FixtureGeneratorInterface::class, FixtureGenerator::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
