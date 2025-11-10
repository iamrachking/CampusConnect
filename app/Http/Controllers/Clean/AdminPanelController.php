<?php

namespace App\Http\Controllers\Clean;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;

class AdminPanelController extends Controller
{
    public function index()
    {
        $user = request()->user();
        if (!$user || !$user->role || $user->role->name !== 'Administrateur') {
            abort(403, 'Accès interdit');
        }

        $pending = Reservation::where('statut', 'pending')->orderBy('date_debut')->paginate(15);
        return view('clean.admin.reservations', compact('pending'));
    }

    public function approve(Reservation $reservation)
    {
        $user = request()->user();
        if (!$user || !$user->role || $user->role->name !== 'Administrateur') {
            abort(403, 'Accès interdit');
        }

        $reservation->update(['statut' => 'approved']);
        return back()->with('status', 'Réservation validée');
    }

    public function reject(Reservation $reservation)
    {
        $user = request()->user();
        if (!$user || !$user->role || $user->role->name !== 'Administrateur') {
            abort(403, 'Accès interdit');
        }

        $reservation->update(['statut' => 'rejected']);
        return back()->with('status', 'Réservation rejetée');
    }
}