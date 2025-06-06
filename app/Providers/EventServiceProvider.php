<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

// Ajouter les imports
use App\Models\Transaction;
use App\Models\Objectif;
use App\Observers\TransactionObserver;
use App\Observers\ObjectifObserver;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        // Enregistrer les observers
        Transaction::observe(TransactionObserver::class);
        Objectif::observe(ObjectifObserver::class);
    }

    /**
     * Determine if events and listeners should be discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
