<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Vérifier les échéances tous les jours à 9h
        $schedule->command('notifications:verifier-echeances')
                 ->dailyAt('09:00');
        
        // Nettoyer les anciennes notifications toutes les semaines
        $schedule->call(function () {
            \App\Services\NotificationService::nettoyerAnciennesNotifications();
        })->weekly();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
