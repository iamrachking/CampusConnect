<?php

namespace App\Http\Controllers;

use App\Models\Reservation;

class AdminReservationController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = $request->user();
            if (!$user || !$user->role || $user->role->name !== 'Administrateur') {
                abort(403, 'Accès interdit');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $pending = Reservation::where('statut', 'pending')->orderBy('date_debut')->paginate(15);
        return view('admin.reservations.index', compact('pending'));
    }

    public function approve(Reservation $reservation)
    {
        $reservation->update(['statut' => 'approved']);
        return back()->with('status', 'Réservation validée');
    }

    public function reject(Reservation $reservation)
    {
        $reservation->update(['statut' => 'rejected']);
        return back()->with('status', 'Réservation rejetée');
    }
}