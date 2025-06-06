<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\CompteController;
use App\Http\Controllers\ObjectifController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\InvitationController;

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
    Route::get('/dashboard/chart-data', [DashboardController::class, 'getChartData']);
    Route::get('/dashboard/objectifs-chart-data', [DashboardController::class, 'getObjectifsChartData']);
    Route::get('/dashboard/debug', [DashboardController::class, 'debug']);
    // APIs pour les graphiques
    Route::get('/dashboard/chart-data', [DashboardController::class, 'getChartData']);
    Route::get('/dashboard/expenses-data', [DashboardController::class, 'getExpensesData']);
    Route::get('/dashboard/objectifs-data', [DashboardController::class, 'getObjectifsData']);
    Route::get('/dashboard/comptes-data', [DashboardController::class, 'getComptesData']);
    // Transactions
    Route::resource('transactions', TransactionController::class);
    Route::get('/transactions/debug', [TransactionController::class, 'debug']);
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

    // Routes pour les notifications
      Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::put('/notifications/{id}/lire', [NotificationController::class, 'marquerCommeLu'])->name('notifications.lire');
    Route::put('/notifications/lire-tout', [NotificationController::class, 'marquerToutCommeLu'])->name('notifications.lire-tout');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::delete('/notifications/supprimer-lues', [NotificationController::class, 'supprimerLues'])->name('notifications.supprimer-lues');
    Route::get('/notifications/test', [NotificationController::class, 'creerTest'])->name('notifications.test');
    Route::get('/notifications/non-lues', [NotificationController::class, 'getNonLues']);
    // Notifications 
    // Routes existantes... 
    Route::resource('objectifs', ObjectifController::class);
    Route::post('/objectifs/{id}/contribuer', [ObjectifController::class, 'contribuer'])->name('objectifs.contribuer');
    Route::post('/objectifs/{id}/corriger', [ObjectifController::class, 'corriger'])->name('objectifs.corriger');
    Route::post('/objectifs/corriger-tous', [ObjectifController::class, 'corrigerTous'])->name('objectifs.corriger-tous');
    Route::get('/objectifs/debug', [ObjectifController::class, 'debug']);

    Route::get('/dashboard/chart-data', [DashboardController::class, 'getChartData'])->name('dashboard.chart-data');
    Route::get('/dashboard/expenses-data', [DashboardController::class, 'getExpensesData'])->name('dashboard.expenses-data');
    Route::get('/dashboard/objectifs-data', [DashboardController::class, 'getObjectifsData'])->name('dashboard.objectifs-data');
    Route::get('/dashboard/comptes-data', [DashboardController::class, 'getComptesData'])->name('dashboard.comptes-data');
    

  });
  Route::middleware(['auth'])->group(function () {
    Route::get('/admin', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users');
    Route::get('/admin/statistics', [AdminController::class, 'statistics'])->name('admin.statistics');
    Route::get('/admin/logs', [AdminController::class, 'logs'])->name('admin.logs');  



Route::get('/admin/statistics', [StatisticsController::class, 'index'])->name('admin.statistics');
Route::get('/admin/statistics/data', [StatisticsController::class, 'getData'])->name('admin.statistics.data');
Route::get('/admin/users/{user}', [UserController::class, 'show'])->name('admin.users.show');
Route::delete('/admin/users/{id}', [UserController::class, 'destroy'])->name('admin.users.destroy');

    Route::get('/admin/invitations', [InvitationController::class, 'index'])->name('admin.invitations.index');
    Route::post('/admin/invitations', [InvitationController::class, 'send'])->name('admin.invitations.send');


  });
// Route de redirection
Route::get('/', function () {
    return redirect()->route('login');
});
