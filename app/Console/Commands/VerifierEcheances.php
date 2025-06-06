<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NotificationService;

class VerifierEcheances extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:verifier-echeances';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Vérifier les échéances proches et envoyer des notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Vérification des échéances en cours...');
        
        NotificationService::verifierEcheancesProches();
        
        $this->info('Vérification terminée !');
        
        return Command::SUCCESS;
    }
}
