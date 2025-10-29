<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\SalleController;
use App\Http\Controllers\MaterielController;
use App\Http\Controllers\ProjetController;
use App\Http\Controllers\EquipeController;
use App\Http\Controllers\LivrableController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    
    // Dashboard principal
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ========================================
    // MODULE 2 - RÉSERVATION DE SALLES/MATÉRIEL
    // ========================================
    
    // Routes pour les réservations
    Route::resource('reservations', ReservationController::class);
    Route::post('reservations/{reservation}/approve', [ReservationController::class, 'approve'])->name('reservations.approve')->middleware('role:Administrateur');
    Route::post('reservations/{reservation}/reject', [ReservationController::class, 'reject'])->name('reservations.reject')->middleware('role:Administrateur');
    
    // Routes pour les salles - index et show accessibles à tous les rôles, CRUD admin uniquement
    Route::get('salles', [SalleController::class, 'index'])->name('salles.index');
    
    // Routes admin uniquement pour salles (AVANT les routes avec paramètres)
    Route::middleware('role:Administrateur')->group(function () {
        Route::get('salles/create', [SalleController::class, 'create'])->name('salles.create');
        Route::post('salles', [SalleController::class, 'store'])->name('salles.store');
        Route::get('salles/{salle}/edit', [SalleController::class, 'edit'])->name('salles.edit');
        Route::put('salles/{salle}', [SalleController::class, 'update'])->name('salles.update');
        Route::delete('salles/{salle}', [SalleController::class, 'destroy'])->name('salles.destroy');
        Route::get('salles/{salle}/calendar', [SalleController::class, 'calendar'])->name('salles.calendar');
    });
    
    // Routes avec paramètres (APRÈS les routes spécifiques)
    Route::get('salles/{salle}', [SalleController::class, 'show'])->name('salles.show');
    Route::post('salles/{salle}/availability', [SalleController::class, 'availability'])->name('salles.availability');
    
    // Routes pour le matériel - index et show accessibles à tous les rôles, CRUD admin uniquement
    Route::get('materiels', [MaterielController::class, 'index'])->name('materiels.index');
    
    // Routes admin uniquement pour materiels (AVANT les routes avec paramètres)
    Route::middleware('role:Administrateur')->group(function () {
        Route::get('materiels/create', [MaterielController::class, 'create'])->name('materiels.create');
        Route::post('materiels', [MaterielController::class, 'store'])->name('materiels.store');
        Route::get('materiels/{materiel}/edit', [MaterielController::class, 'edit'])->name('materiels.edit');
        Route::put('materiels/{materiel}', [MaterielController::class, 'update'])->name('materiels.update');
        Route::delete('materiels/{materiel}', [MaterielController::class, 'destroy'])->name('materiels.destroy');
        Route::get('materiels/{materiel}/calendar', [MaterielController::class, 'calendar'])->name('materiels.calendar');
    });
    
    // Routes avec paramètres (APRÈS les routes spécifiques)
    Route::get('materiels/{materiel}', [MaterielController::class, 'show'])->name('materiels.show');
    Route::post('materiels/{materiel}/availability', [MaterielController::class, 'availability'])->name('materiels.availability');

    // ========================================
    // MODULE 3 - GESTION DE PROJETS ÉTUDIANTS
    // ========================================
    
    // Routes pour les projets
    Route::resource('projets', ProjetController::class);
    Route::post('projets/{projet}/join', [ProjetController::class, 'join'])->name('projets.join');
    Route::post('projets/{projet}/leave', [ProjetController::class, 'leave'])->name('projets.leave');
    Route::get('projets/{projet}/stats', [ProjetController::class, 'stats'])->name('projets.stats');
    
    // Routes pour les équipes (nested dans les projets)
    Route::resource('projets.equipes', EquipeController::class)->except(['index', 'show']);
    Route::get('projets/{projet}/equipes', [EquipeController::class, 'index'])->name('projets.equipes.index');
    Route::get('projets/{projet}/equipes/create', [EquipeController::class, 'create'])->name('projets.equipes.create');
    Route::post('projets/{projet}/equipes', [EquipeController::class, 'store'])->name('projets.equipes.store');
    Route::get('equipes/{equipe}', [EquipeController::class, 'show'])->name('equipes.show');
    Route::get('equipes/{equipe}/edit', [EquipeController::class, 'edit'])->name('equipes.edit');
    Route::put('equipes/{equipe}', [EquipeController::class, 'update'])->name('equipes.update');
    Route::delete('equipes/{equipe}', [EquipeController::class, 'destroy'])->name('equipes.destroy');
    Route::post('equipes/{equipe}/transfer-leadership', [EquipeController::class, 'transferLeadership'])->name('equipes.transfer-leadership');
    
    // Routes pour les livrables (nested dans les projets)
    Route::resource('projets.livrables', LivrableController::class)->except(['index']);
    Route::get('projets/{projet}/livrables', [LivrableController::class, 'index'])->name('projets.livrables.index');
    Route::get('projets/{projet}/livrables/create', [LivrableController::class, 'create'])->name('projets.livrables.create');
    Route::post('projets/{projet}/livrables', [LivrableController::class, 'store'])->name('projets.livrables.store');
    Route::get('livrables/{livrable}', [LivrableController::class, 'show'])->name('livrables.show');
    Route::get('livrables/{livrable}/edit', [LivrableController::class, 'edit'])->name('livrables.edit');
    Route::put('livrables/{livrable}', [LivrableController::class, 'update'])->name('livrables.update');
    Route::delete('livrables/{livrable}', [LivrableController::class, 'destroy'])->name('livrables.destroy');
    Route::get('livrables/{livrable}/download', [LivrableController::class, 'download'])->name('livrables.download');
    Route::get('projets/{projet}/livrables/stats', [LivrableController::class, 'stats'])->name('projets.livrables.stats');

    // ========================================
    // ROUTES GÉNÉRALES POUR LES ÉQUIPES ET LIVRABLES
    // ========================================
    
    // Routes pour lister toutes les équipes (pour les administrateurs uniquement)
    Route::get('equipes', [EquipeController::class, 'index'])->name('equipes.index')->middleware('role:Administrateur');
    
    // Routes pour lister tous les livrables (pour les administrateurs)
    Route::get('livrables', function() {
        return redirect()->route('projets.index');
    })->name('livrables.index');

    // ========================================
    // GESTION DES UTILISATEURS (ADMIN UNIQUEMENT)
    // ========================================
    
    // Routes pour la gestion des utilisateurs
    Route::middleware('role:Administrateur')->resource('users', UserController::class);

});
