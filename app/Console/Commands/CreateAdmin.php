<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateAdmin extends Command
{
    protected $signature = 'admin:create {name} {email} {password}';
    protected $description = 'Créer un nouvel administrateur';

    public function handle()
    {
        $user = User::create([
            'name' => $this->argument('name'),
            'email' => $this->argument('email'),
            'password' => Hash::make($this->argument('password')),
            'is_admin' => true,
            'email_verified_at' => now(),
        ]);

        $this->info("Admin créé avec succès : {$user->email}");
    }
}