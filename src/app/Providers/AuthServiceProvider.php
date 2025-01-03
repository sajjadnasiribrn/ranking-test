<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\Team;
use App\Models\Tournament;
use App\Policies\TeamPolicy;
use App\Policies\TournamentPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Team::class => TeamPolicy::class,
        Tournament::class => TournamentPolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
