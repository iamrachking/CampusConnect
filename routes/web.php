<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\SalleController;
use App\Http\Controllers\MaterielController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\AdminReservationController;
use App\Http\Controllers\Clean\AdminPanelController;
use App\Http\Controllers\Clean\TeacherPanelController;
use App\Http\Controllers\Clean\StudentPanelController;

Route::get('/', function () {
    return view('spa');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        $user = request()->user();
        if (!$user || !$user->role) {
            abort(403, 'Accès interdit');
        }

        $role = $user->role->name;
        if ($role === 'Administrateur') {
            return redirect()->route('clean.admin.reservations');
        }
        if ($role === 'Enseignant') {
            return redirect()->route('clean.teacher.index');
        }
        if ($role === 'Étudiant') {
            return redirect()->route('clean.student.availability');
        }

        return view('dashboard');
    })->name('dashboard');

    // Administration épurée: Réservations en attente (Administrateur uniquement)
    Route::middleware(['role:Administrateur'])->group(function () {
        Route::get('admin/reservations', [AdminPanelController::class, 'index'])->name('clean.admin.reservations');
        Route::post('admin/reservations/{reservation}/approve', [AdminPanelController::class, 'approve'])->name('clean.admin.reservations.approve');
        Route::post('admin/reservations/{reservation}/reject', [AdminPanelController::class, 'reject'])->name('clean.admin.reservations.reject');
    });

    // Enseignants épuré: liste et création
    Route::middleware(['role:Enseignant'])->group(function () {
        Route::get('teacher', [TeacherPanelController::class, 'index'])->name('clean.teacher.index');
        Route::get('teacher/create', [TeacherPanelController::class, 'create'])->name('clean.teacher.create');
        Route::post('teacher', [TeacherPanelController::class, 'store'])->name('clean.teacher.store');
    });

    // Étudiants épuré: disponibilité et demande
    Route::middleware(['role:Étudiant'])->group(function () {
        Route::get('student/availability', [StudentPanelController::class, 'availability'])->name('clean.student.availability');
        Route::get('student/create', [StudentPanelController::class, 'create'])->name('clean.student.create');
        Route::post('student', [StudentPanelController::class, 'store'])->name('clean.student.store');
    });
});
