<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Reservation;
use App\Models\Salle;
use App\Models\Materiel;
use App\Http\Controllers\Api\AuthController;

// Auth API (stateless token)
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user()->load('role');
    });

    Route::post('/logout', [AuthController::class, 'logout']);

    // Items lists with real-time availability
    Route::get('/items/salles', function () {
        $busyIds = Reservation::where('item_type', Salle::class)
            ->where('statut', 'approved')
            ->where('date_debut', '<=', DB::raw('CURRENT_TIMESTAMP'))
            ->where('date_fin', '>=', DB::raw('CURRENT_TIMESTAMP'))
            ->pluck('item_id')
            ->all();

        return Salle::orderBy('nom_salle')->get()->map(function ($s) use ($busyIds) {
            $s->setAttribute('disponible', !in_array($s->id, $busyIds));
            return $s;
        });
    });

    Route::get('/items/materiels', function () {
        $busyIds = Reservation::where('item_type', Materiel::class)
            ->where('statut', 'approved')
            ->where('date_debut', '<=', DB::raw('CURRENT_TIMESTAMP'))
            ->where('date_fin', '>=', DB::raw('CURRENT_TIMESTAMP'))
            ->pluck('item_id')
            ->all();

        return Materiel::orderBy('nom_materiel')->get()->map(function ($m) use ($busyIds) {
            $m->setAttribute('disponible', !in_array($m->id, $busyIds));
            return $m;
        });
    });

    // Admin: réservations en attente + actions
    Route::middleware('role:Administrateur')->group(function () {
        Route::get('/reservations/pending', function () {
            return Reservation::where('statut', 'pending')
                ->orderBy('date_debut')
                ->paginate(15);
        });

        Route::post('/reservations/{reservation}/approve', function (Reservation $reservation) {
            $reservation->update(['statut' => 'approved']);
            return response()->json(['ok' => true]);
        });

        Route::post('/reservations/{reservation}/reject', function (Reservation $reservation) {
            $reservation->update(['statut' => 'rejected']);
            return response()->json(['ok' => true]);
        });

        // Admin: création de salles et matériels (avec unicité)
        Route::post('/admin/items/salles', function (Request $request) {
            $validated = $request->validate([
                'nom_salle' => 'required|string|max:255|unique:salles,nom_salle',
                'capacite' => 'nullable|integer|min:1',
                'localisation' => 'nullable|string|max:255',
            ]);
            $s = Salle::create([
                'nom_salle' => $validated['nom_salle'],
                'capacite' => $validated['capacite'] ?? null,
                'localisation' => $validated['localisation'] ?? null,
                'disponible' => true,
            ]);
            return response()->json(['ok' => true, 'salle' => $s], 201);
        });

        Route::post('/admin/items/materiels', function (Request $request) {
            $validated = $request->validate([
                'nom_materiel' => 'required|string|max:255|unique:materiels,nom_materiel',
            ]);
            $m = Materiel::create([
                'nom_materiel' => $validated['nom_materiel'],
                'disponible' => true,
            ]);
            return response()->json(['ok' => true, 'materiel' => $m], 201);
        });

        // Admin: liste et suppression des éléments
        Route::get('/admin/items/salles', function () {
            $busyIds = Reservation::where('item_type', Salle::class)
                ->where('statut', 'approved')
                ->where('date_debut', '<=', DB::raw('CURRENT_TIMESTAMP'))
                ->where('date_fin', '>=', DB::raw('CURRENT_TIMESTAMP'))
                ->pluck('item_id')
                ->all();
            return Salle::orderBy('nom_salle')->get()->map(function ($s) use ($busyIds) {
                $s->setAttribute('disponible', !in_array($s->id, $busyIds));
                return $s;
            });
        });
        Route::get('/admin/items/materiels', function () {
            $busyIds = Reservation::where('item_type', Materiel::class)
                ->where('statut', 'approved')
                ->where('date_debut', '<=', DB::raw('CURRENT_TIMESTAMP'))
                ->where('date_fin', '>=', DB::raw('CURRENT_TIMESTAMP'))
                ->pluck('item_id')
                ->all();
            return Materiel::orderBy('nom_materiel')->get()->map(function ($m) use ($busyIds) {
                $m->setAttribute('disponible', !in_array($m->id, $busyIds));
                return $m;
            });
        });
        Route::delete('/admin/items/salles/{salle}', function (Salle $salle) {
            $hasReservations = Reservation::where('item_type', Salle::class)->where('item_id', $salle->id)->exists();
            if ($hasReservations) {
                return response()->json(['ok' => false, 'error' => 'Impossible de supprimer: des réservations existent'], 422);
            }
            $salle->delete();
            return response()->json(['ok' => true]);
        });
        Route::delete('/admin/items/materiels/{materiel}', function (Materiel $materiel) {
            $hasReservations = Reservation::where('item_type', Materiel::class)->where('item_id', $materiel->id)->exists();
            if ($hasReservations) {
                return response()->json(['ok' => false, 'error' => 'Impossible de supprimer: des réservations existent'], 422);
            }
            $materiel->delete();
            return response()->json(['ok' => true]);
        });
    });

    // Enseignant: liste + création de réservation
    Route::middleware('role:Enseignant')->group(function () {
        Route::get('/teacher/reservations', function (Request $request) {
            return Reservation::where('user_id', $request->user()->id)
                ->orderByDesc('date_debut')
                ->paginate(15);
        });

        Route::post('/teacher/reservations', function (Request $request) {
            $validated = $request->validate([
                'item_type' => 'required|in:salle,materiel',
                'item_id' => 'required|integer',
                'date_debut' => 'required|date',
                'date_fin' => 'required|date|after:date_debut',
                'motif' => 'nullable|string|max:255',
            ]);

            $start = Carbon::parse($validated['date_debut']);
            $end = Carbon::parse($validated['date_fin']);
            $itemClass = $validated['item_type'] === 'salle' ? Salle::class : Materiel::class;

            $overlap = Reservation::where('item_type', $itemClass)
                ->where('item_id', $validated['item_id'])
                ->where(function ($q) use ($start, $end) {
                    $q->whereBetween('date_debut', [$start, $end])
                      ->orWhereBetween('date_fin', [$start, $end])
                      ->orWhere(function ($q2) use ($start, $end) {
                          $q2->where('date_debut', '<=', $start)
                             ->where('date_fin', '>=', $end);
                      });
                })
                ->exists();

            if ($overlap) {
                return response()->json(['ok' => false, 'error' => 'Créneau déjà réservé'], 422);
            }

            $res = Reservation::create([
                'user_id' => Auth::id(),
                'item_type' => $itemClass,
                'item_id' => $validated['item_id'],
                'date_debut' => $start,
                'date_fin' => $end,
                'statut' => 'pending',
                'motif' => $validated['motif'] ?? null,
            ]);

            return response()->json(['ok' => true, 'reservation' => $res], 201);
        });
    });

    // Étudiant: données (salles, matériels) + demande de réservation
    Route::middleware('role:Étudiant')->group(function () {
        Route::get('/student/salles', function () {
            $busyIds = Reservation::where('item_type', Salle::class)
                ->where('statut', 'approved')
                ->where('date_debut', '<=', DB::raw('CURRENT_TIMESTAMP'))
                ->where('date_fin', '>=', DB::raw('CURRENT_TIMESTAMP'))
                ->pluck('item_id')
                ->all();
            return Salle::orderBy('nom_salle')->get()->map(function ($s) use ($busyIds) {
                $s->setAttribute('disponible', !in_array($s->id, $busyIds));
                return $s;
            });
        });

        Route::get('/student/materiels', function () {
            $busyIds = Reservation::where('item_type', Materiel::class)
                ->where('statut', 'approved')
                ->where('date_debut', '<=', DB::raw('CURRENT_TIMESTAMP'))
                ->where('date_fin', '>=', DB::raw('CURRENT_TIMESTAMP'))
                ->pluck('item_id')
                ->all();
            return Materiel::orderBy('nom_materiel')->get()->map(function ($m) use ($busyIds) {
                $m->setAttribute('disponible', !in_array($m->id, $busyIds));
                return $m;
            });
        });

        Route::post('/student/reservations', function (Request $request) {
            $validated = $request->validate([
                'item_type' => 'required|in:salle,materiel',
                'item_id' => 'required|integer',
                'date_debut' => 'required|date',
                'date_fin' => 'required|date|after:date_debut',
                'motif' => 'nullable|string|max:255',
            ]);

            $start = Carbon::parse($validated['date_debut']);
            $end = Carbon::parse($validated['date_fin']);
            $itemClass = $validated['item_type'] === 'salle' ? Salle::class : Materiel::class;

            $overlap = Reservation::where('item_type', $itemClass)
                ->where('item_id', $validated['item_id'])
                ->where(function ($q) use ($start, $end) {
                    $q->whereBetween('date_debut', [$start, $end])
                      ->orWhereBetween('date_fin', [$start, $end])
                      ->orWhere(function ($q2) use ($start, $end) {
                          $q2->where('date_debut', '<=', $start)
                             ->where('date_fin', '>=', $end);
                      });
                })
                ->exists();

            if ($overlap) {
                return response()->json(['ok' => false, 'error' => 'Créneau déjà réservé'], 422);
            }

            $res = Reservation::create([
                'user_id' => Auth::id(),
                'item_type' => $itemClass,
                'item_id' => $validated['item_id'],
                'date_debut' => $start,
                'date_fin' => $end,
                'statut' => 'pending',
                'motif' => $validated['motif'] ?? null,
            ]);

            return response()->json(['ok' => true, 'reservation' => $res], 201);
        });
    });
});
