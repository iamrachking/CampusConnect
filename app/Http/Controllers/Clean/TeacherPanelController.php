<?php

namespace App\Http\Controllers\Clean;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Salle;
use App\Models\Materiel;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class TeacherPanelController extends Controller
{
    public function index()
    {
        $user = request()->user();
        if (!$user || !$user->role || $user->role->name !== 'Enseignant') {
            abort(403, 'Accès interdit');
        }

        $reservations = Reservation::where('user_id', $user->id)->orderByDesc('date_debut')->paginate(15);
        return view('clean.teacher.index', compact('reservations'));
    }

    public function create()
    {
        $user = request()->user();
        if (!$user || !$user->role || $user->role->name !== 'Enseignant') {
            abort(403, 'Accès interdit');
        }

        $salles = Salle::orderBy('nom_salle')->get();
        $materiels = Materiel::orderBy('nom_materiel')->get();
        return view('clean.teacher.create', compact('salles', 'materiels'));
    }

    public function store(Request $request)
    {
        $user = $request->user();
        if (!$user || !$user->role || $user->role->name !== 'Enseignant') {
            abort(403, 'Accès interdit');
        }

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
            return back()->withErrors(['date_debut' => 'Créneau déjà réservé'])->withInput();
        }

        Reservation::create([
            'user_id' => Auth::id(),
            'item_type' => $itemClass,
            'item_id' => $validated['item_id'],
            'date_debut' => $start,
            'date_fin' => $end,
            'statut' => 'pending',
            'motif' => $validated['motif'] ?? null,
        ]);

        return redirect()->route('clean.teacher.index')->with('status', 'Demande de réservation envoyée');
    }
}