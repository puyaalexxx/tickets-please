<?php

namespace App\Providers;

use App\Models\Ticket;
use App\Policies\V1\TicketPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Ticket::class => TicketPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        /*
        // Optional: define gates here
        Gate::define('view-dashboard', function ($user) {
            return $user->is_admin;
        });*/
    }
}
