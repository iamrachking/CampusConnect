<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Salle;
use App\Models\Materiel;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    public function __construct()
    {
        // Protéger création aux enseignants
        $this->middleware(function ($request, $next) {
            if (in_array($request->route()->getName(), ['reservations.create', 'reservations.store', 'reservations.index'])) {
                $user = $request->user();
                if (!$user || !$user->role || $user->role->name !== 'Enseignant') {
                    abort(403, 'Accès interdit');
                }
            }
            return $next($request);
        });
    }

    public function index()
    {
        $reservations = Reservation::where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->paginate(10);
        return view('reservations.index', compact('reservations'));
    }

    public function create()
    {
        $salles = Salle::orderBy('nom_salle')->get();
        $materiels = Materiel::orderBy('nom_materiel')->get();
        return view('reservations.create', compact('salles', 'materiels'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_type' => 'required|in:App\\Models\\Salle,App\\Models\\Materiel',
            'item_id' => 'required|integer',
            'date_debut' => 'required|date_format:Y-m-d\\TH:i',
            'date_fin' => 'required|date_format:Y-m-d\\TH:i|after:date_debut',
            'motif' => 'required|string',
        ]);

        // Validation d'existence selon le type choisi
        if ($validated['item_type'] === Salle::class) {
            if (!Salle::whereKey($validated['item_id'])->exists()) {
                return back()->withErrors(['item_id' => 'Salle introuvable'])->withInput();
            }
        } else {
            if (!Materiel::whereKey($validated['item_id'])->exists()) {
                return back()->withErrors(['item_id' => 'Matériel introuvable'])->withInput();
            }
        }

        // Parser les dates du format datetime-local
        $start = Carbon::createFromFormat('Y-m-d\\TH:i', $validated['date_debut']);
        $end = Carbon::createFromFormat('Y-m-d\\TH:i', $validated['date_fin']);

        // Vérifier disponibilité simple: pas de chevauchement
        $overlap = Reservation::where('item_type', $validated['item_type'])
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
            'item_type' => $validated['item_type'],
            'item_id' => $validated['item_id'],
            'date_debut' => $start,
            'date_fin' => $end,
            'statut' => 'pending',
            'motif' => $validated['motif'],
        ]);

        return redirect()->route('reservations.index')->with('status', 'Réservation créée et en attente de validation');
    }

    // Disponibilité pour les étudiants: vue et filtre
    public function availability(Request $request)
    {
        $dateDebut = $request->input('date_debut');
        $dateFin = $request->input('date_fin');

        $salles = Salle::orderBy('nom_salle')->get();

        $reservations = collect();
        if ($dateDebut && $dateFin) {
            $reservations = Reservation::where('item_type', Salle::class)
                ->where(function ($q) use ($dateDebut, $dateFin) {
                    $q->whereBetween('date_debut', [$dateDebut, $dateFin])
                      ->orWhereBetween('date_fin', [$dateDebut, $dateFin])
                      ->orWhere(function ($q2) use ($dateDebut, $dateFin) {
                          $q2->where('date_debut', '<=', $dateDebut)
                             ->where('date_fin', '>=', $dateFin);
                      });
                })
                ->get()
                ->groupBy('item_id');
        }

        return view('availability.index', [
            'salles' => $salles,
            'reservations' => $reservations,
            'date_debut' => $dateDebut,
            'date_fin' => $dateFin,
        ]);
    }

    // Étudiants: formulaire de demande de réservation
    public function studentCreate()
    {
        $salles = Salle::orderBy('nom_salle')->get();
        $materiels = Materiel::orderBy('nom_materiel')->get();
        return view('student.reservations.create', compact('salles', 'materiels'));
    }

    // Étudiants: soumission de la demande de réservation
    public function studentStore(Request $request)
    {
        $validated = $request->validate([
            'item_type' => 'required|in:App\\Models\\Salle,App\\Models\\Materiel',
            'item_id' => 'required|integer',
            'date_debut' => 'required|date_format:Y-m-d\\TH:i',
            'date_fin' => 'required|date_format:Y-m-d\\TH:i|after:date_debut',
            'motif' => 'required|string',
        ]);

        if ($validated['item_type'] === Salle::class) {
            if (!Salle::whereKey($validated['item_id'])->exists()) {
                return back()->withErrors(['item_id' => 'Salle introuvable'])->withInput();
            }
        } else {
            if (!Materiel::whereKey($validated['item_id'])->exists()) {
                return back()->withErrors(['item_id' => 'Matériel introuvable'])->withInput();
            }
        }

        $start = Carbon::createFromFormat('Y-m-d\\TH:i', $validated['date_debut']);
        $end = Carbon::createFromFormat('Y-m-d\\TH:i', $validated['date_fin']);

        $overlap = Reservation::where('item_type', $validated['item_type'])
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
            'item_type' => $validated['item_type'],
            'item_id' => $validated['item_id'],
            'date_debut' => $start,
            'date_fin' => $end,
            'statut' => 'pending',
            'motif' => $validated['motif'],
        ]);

        return redirect()->route('availability.index')->with('status', 'Demande de réservation envoyée');
    }
}