<?php

namespace App\Http\Controllers;

use App\Models\Salle;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SalleController extends Controller
{
    /**
     * Display a listing of the resource.
     * 
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Paramètres de filtrage
        $filterDisponible = $request->get('disponible');
        $search = $request->get('search');
        
        // Logique selon le rôle de l'utilisateur
        switch ($user->role->name) {
            case 'Administrateur':
                // Les administrateurs voient toutes les salles avec leurs réservations
                $query = Salle::with(['reservations' => function($query) {
                    $query->where('statut', 'approved')
                          ->orderBy('date_debut', 'asc');
                }]);
                break;

            case 'Enseignant':
                // Les enseignants voient toutes les salles disponibles pour réserver
                $query = Salle::where('disponible', true)
                    ->with(['reservations' => function($query) {
                        $query->where('statut', 'approved')
                              ->orderBy('date_debut', 'asc');
                    }]);
                break;

            case 'Étudiant':
                // Les étudiants voient les salles disponibles pour consulter la disponibilité
                $query = Salle::where('disponible', true)
                    ->with(['reservations' => function($query) {
                        $query->where('statut', 'approved')
                              ->where('date_fin', '>=', now())
                              ->orderBy('date_debut', 'asc');
                    }]);
                break;

            default:
                $query = Salle::query();
                break;
        }
        
        // Appliquer les filtres
        if ($filterDisponible !== null) {
            if ($filterDisponible === 'disponible') {
                $query->where('disponible', true);
            } elseif ($filterDisponible === 'indisponible') {
                $query->where('disponible', false);
            }
        }
        
        if ($search) {
            $query->where('nom_salle', 'like', '%' . $search . '%');
        }
        
        $salles = $query->paginate(15);

        // Si c'est une requête AJAX, retourner du JSON
        if ($request->get('ajax') == '1') {
            return response()->json($salles->items());
        }

        return view('salles.index', compact('salles'));
    }

    /**
     * Show the form for creating a new resource.
     * 
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Seuls les administrateurs peuvent créer des salles
        if (Auth::user()->role->name !== 'Administrateur') {
            return redirect()->route('salles.index')
                ->with('error', 'Seuls les administrateurs peuvent créer des salles.');
        }

        return view('salles.create');
    }

    /**
     * Store a newly created resource in storage.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Seuls les administrateurs peuvent créer des salles
        if (Auth::user()->role->name !== 'Administrateur') {
            return redirect()->route('salles.index')
                ->with('error', 'Seuls les administrateurs peuvent créer des salles.');
        }

        // Validation des données
        $validator = Validator::make($request->all(), [
            'nom_salle' => 'required|string|max:255|unique:salles,nom_salle',
            'capacite' => 'required|integer|min:1|max:1000',
            'localisation' => 'required|string|max:500',
            'disponible' => 'boolean',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Créer la salle
        $salle = Salle::create([
            'nom_salle' => $request->nom_salle,
            'capacite' => $request->capacite,
            'localisation' => $request->localisation,
            'disponible' => $request->has('disponible') ? true : false,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        return redirect()->route('salles.show', $salle)
            ->with('success', 'Salle créée avec succès.');
    }

    /**
     * Display the specified resource.
     * 
     * @param  \App\Models\Salle  $salle
     * @return \Illuminate\Http\Response
     */
    public function show(Salle $salle)
    {
        $user = Auth::user();
        
        // Charger les réservations selon le rôle
        $reservations = collect();
        
        switch ($user->role->name) {
            case 'Administrateur':
                // Les administrateurs voient toutes les réservations de la salle
                $reservations = Reservation::where('item_type', 'salle')
                    ->where('item_id', $salle->id)
                    ->with('user')
                    ->orderBy('date_debut', 'desc')
                    ->paginate(10);
                break;

            case 'Enseignant':
                // Les enseignants voient leurs propres réservations + les approuvées
                $reservations = Reservation::where('item_type', 'salle')
                    ->where('item_id', $salle->id)
                    ->where(function($query) use ($user) {
                        $query->where('user_id', $user->id)
                              ->orWhere('statut', 'approved');
                    })
                    ->with('user')
                    ->orderBy('date_debut', 'desc')
                    ->paginate(10);
                break;

            case 'Étudiant':
                // Les étudiants voient seulement les réservations approuvées
                $reservations = Reservation::where('item_type', 'salle')
                    ->where('item_id', $salle->id)
                    ->where('statut', 'approved')
                    ->where('date_fin', '>=', now())
                    ->with('user')
                    ->orderBy('date_debut', 'asc')
                    ->paginate(10);
                break;
        }

        return view('salles.show', compact('salle', 'reservations'));
    }

    /**
     * Show the form for editing the specified resource.
     * 
     * @param  \App\Models\Salle  $salle
     * @return \Illuminate\Http\Response
     */
    public function edit(Salle $salle)
    {
        // Seuls les administrateurs peuvent modifier les salles
        if (Auth::user()->role->name !== 'Administrateur') {
            return redirect()->route('salles.index')
                ->with('error', 'Seuls les administrateurs peuvent modifier les salles.');
        }

        return view('salles.edit', compact('salle'));
    }

    /**
     * Update the specified resource in storage.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Salle  $salle
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Salle $salle)
    {
        // Seuls les administrateurs peuvent modifier les salles
        if (Auth::user()->role->name !== 'Administrateur') {
            return redirect()->route('salles.index')
                ->with('error', 'Seuls les administrateurs peuvent modifier les salles.');
        }

        // Validation des données
        $validator = Validator::make($request->all(), [
            'nom_salle' => 'required|string|max:255|unique:salles,nom_salle,' . $salle->id,
            'capacite' => 'required|integer|min:1|max:1000',
            'localisation' => 'required|string|max:500',
            'disponible' => 'boolean',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Mettre à jour la salle
        $salle->update([
            'nom_salle' => $request->nom_salle,
            'capacite' => $request->capacite,
            'localisation' => $request->localisation,
            'disponible' => $request->has('disponible') ? true : false,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        return redirect()->route('salles.show', $salle)
            ->with('success', 'Salle mise à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     * 
     * @param  \App\Models\Salle  $salle
     * @return \Illuminate\Http\Response
     */
    public function destroy(Salle $salle)
    {
        // Seuls les administrateurs peuvent supprimer les salles
        if (Auth::user()->role->name !== 'Administrateur') {
            return redirect()->route('salles.index')
                ->with('error', 'Seuls les administrateurs peuvent supprimer les salles.');
        }

        // Vérifier s'il y a des réservations actives
        $hasActiveReservations = Reservation::where('item_type', \App\Models\Salle::class)
            ->where('item_id', $salle->id)
            ->where('statut', 'approved')
            ->where('date_fin', '>=', now())
            ->exists();

        if ($hasActiveReservations) {
            return redirect()->route('salles.show', $salle)
                ->with('error', 'Impossible de supprimer cette salle car elle a des réservations actives.');
        }

        $salle->delete();

        return redirect()->route('salles.index')
            ->with('success', 'Salle supprimée avec succès.');
    }

    /**
     * Vérifier la disponibilité d'une salle pour une période donnée
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Salle  $salle
     * @return \Illuminate\Http\Response
     */
    public function availability(Request $request, Salle $salle)
    {
        $validator = Validator::make($request->all(), [
            'date_debut' => 'required|date|after:now',
            'date_fin' => 'required|date|after:date_debut',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dates invalides',
                'errors' => $validator->errors()
            ], 400);
        }

        // Vérifier les conflits de réservation
        $conflict = Reservation::where('item_type', \App\Models\Salle::class)
            ->where('item_id', $salle->id)
            ->where('statut', 'approved')
            ->where(function ($query) use ($request) {
                $query->whereBetween('date_debut', [$request->date_debut, $request->date_fin])
                    ->orWhereBetween('date_fin', [$request->date_debut, $request->date_fin])
                    ->orWhere(function ($q) use ($request) {
                        $q->where('date_debut', '<=', $request->date_debut)
                          ->where('date_fin', '>=', $request->date_fin);
                    });
            })
            ->exists();

        $available = !$conflict && $salle->disponible;

        return response()->json([
            'success' => true,
            'available' => $available,
            'salle' => [
                'id' => $salle->id,
                'nom_salle' => $salle->nom_salle,
                'capacite' => $salle->capacite,
                'localisation' => $salle->localisation,
                'disponible' => $salle->disponible
            ],
            'message' => $available ? 'Salle disponible pour cette période' : 'Salle non disponible pour cette période'
        ]);
    }

    /**
     * Obtenir le calendrier des réservations d'une salle
     * 
     * @param  \App\Models\Salle  $salle
     * @return \Illuminate\Http\Response
     */
    public function calendar(Salle $salle)
    {
        $user = Auth::user();
        
        // Récupérer les réservations selon le rôle
        $query = Reservation::where('item_type', \App\Models\Salle::class)
            ->where('item_id', $salle->id);

        switch ($user->role->name) {
            case 'Administrateur':
                // Les administrateurs voient toutes les réservations
                break;

            case 'Enseignant':
                // Les enseignants voient leurs propres réservations + les approuvées
                $query->where(function($q) use ($user) {
                    $q->where('user_id', $user->id)
                      ->orWhere('statut', 'approved');
                });
                break;

            case 'Étudiant':
                // Les étudiants voient seulement les réservations approuvées
                $query->where('statut', 'approved');
                break;
        }

        $reservations = $query->with('user')
            ->orderBy('date_debut', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'salle' => [
                'id' => $salle->id,
                'nom_salle' => $salle->nom_salle,
                'capacite' => $salle->capacite,
                'localisation' => $salle->localisation
            ],
            'reservations' => $reservations->map(function($reservation) {
                return [
                    'id' => $reservation->id,
                    'title' => $reservation->motif,
                    'start' => $reservation->date_debut->format('Y-m-d H:i:s'),
                    'end' => $reservation->date_fin->format('Y-m-d H:i:s'),
                    'status' => $reservation->statut,
                    'user' => $reservation->user->name,
                    'color' => $reservation->statut === 'approved' ? '#28a745' : 
                              ($reservation->statut === 'rejected' ? '#dc3545' : '#ffc107')
                ];
            })
        ]);
    }
}
