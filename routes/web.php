<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SalleController;
use App\Http\Controllers\MaterielController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\AdminReservationController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Administration: Salles & Matériels (Administrateur uniquement)
    Route::middleware(['role:Administrateur'])->group(function () {
        Route::resource('admin/salles', SalleController::class)->names('admin.salles');
        Route::post('admin/salles/{salle}/toggle', [SalleController::class, 'toggle'])->name('admin.salles.toggle');

        Route::resource('admin/materiels', MaterielController::class)->names('admin.materiels');
        Route::post('admin/materiels/{materiel}/toggle', [MaterielController::class, 'toggle'])->name('admin.materiels.toggle');

        // Réservations en attente
        Route::get('admin/reservations', [AdminReservationController::class, 'index'])->name('admin.reservations.index');
        Route::post('admin/reservations/{reservation}/approve', [AdminReservationController::class, 'approve'])->name('admin.reservations.approve');
        Route::post('admin/reservations/{reservation}/reject', [AdminReservationController::class, 'reject'])->name('admin.reservations.reject');
    });

    // Enseignants: créer et consulter ses réservations
    Route::middleware(['role:Enseignant'])->group(function () {
        Route::get('reservations', [ReservationController::class, 'index'])->name('reservations.index');
        Route::get('reservations/create', [ReservationController::class, 'create'])->name('reservations.create');
        Route::post('reservations', [ReservationController::class, 'store'])->name('reservations.store');
    });

    // Étudiants: consulter disponibilité des salles
    Route::middleware(['role:Étudiant'])->group(function () {
        Route::get('availability', [ReservationController::class, 'availability'])->name('availability.index');
    });
});
