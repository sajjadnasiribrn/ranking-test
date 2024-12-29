<?php

namespace App\Providers;

use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;
use App\Repositories\TeamRepository;
use App\Repositories\TournamentRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(RepositoryInterface::class, Repository::class);

        $this->app->bind(UserRepository::class, function ($app) {
            return new UserRepository($app->make(\App\Models\User::class));
        });

        $this->app->bind(TeamRepository::class, function ($app) {
            return new TeamRepository($app->make(\App\Models\Team::class));
        });

        $this->app->bind(TournamentRepository::class, function ($app) {
            return new TournamentRepository($app->make(\App\Models\Tournament::class));
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
