<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\CompteController;
use App\Http\Controllers\ObjectifController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfilController;

// Routes d'authentification
Route::get('/login', [AuthController::class, 'connexionpage'])->name('login');
Route::post('/login', [AuthController::class, 'connexion'])->name('login.post');
Route::get('/register', [AuthController::class, 'inscription'])->name('register');
Route::post('/register', [AuthController::class, 'enregistrer'])->name('register.post');
Route::post('/logout', [AuthController::class, 'deconnexion'])->name('logout');

// Routes protégées par auth
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Transactions
    Route::resource('transactions', TransactionController::class);
    
    // Comptes
    Route::resource('comptes', CompteController::class);
    
    // Objectifs
    Route::resource('objectifs', ObjectifController::class);
    Route::post('/objectifs/{objectif}/contribuer', [ObjectifController::class, 'contribuer'])->name('objectifs.contribuer');
    
    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::put('/notifications/{notification}/lire', [NotificationController::class, 'marquerCommeLu'])->name('notifications.lire');
    Route::put('/notifications/lire-tout', [NotificationController::class, 'marquerToutCommeLu'])->name('notifications.lire-tout');
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    
    // Profil
    Route::get('/profil', [ProfilController::class, 'index'])->name('profil.index');
    Route::get('/profil/edit', [ProfilController::class, 'edit'])->name('profil.edit');
    Route::put('/profil', [ProfilController::class, 'update'])->name('profil.update');
    Route::get('/profil/change-password', [ProfilController::class, 'changePassword'])->name('profil.change-password');
    Route::put('/profil/password', [ProfilController::class, 'updatePassword'])->name('profil.update-password');
});

// Route de redirection
Route::get('/', function () {
    return redirect()->route('login');
});
