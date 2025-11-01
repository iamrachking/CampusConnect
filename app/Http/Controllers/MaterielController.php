<?php

namespace App\Http\Controllers;

use App\Models\Materiel;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class MaterielController extends Controller
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
                // Les administrateurs voient tous les matériels avec leurs réservations
                $query = Materiel::with(['reservations' => function($query) {
                    $query->where('statut', 'approved')
                          ->orderBy('date_debut', 'asc');
                }]);
                break;

            case 'Enseignant':
                // Les enseignants voient tous les matériels disponibles pour réserver
                $query = Materiel::where('disponible', true)
                    ->with(['reservations' => function($query) {
                        $query->where('statut', 'approved')
                              ->orderBy('date_debut', 'asc');
                    }]);
                break;

            case 'Étudiant':
                // Les étudiants voient les matériels disponibles pour consulter la disponibilité
                $query = Materiel::where('disponible', true)
                    ->with(['reservations' => function($query) {
                        $query->where('statut', 'approved')
                              ->where('date_fin', '>=', now())
                              ->orderBy('date_debut', 'asc');
                    }]);
                break;

            default:
                $query = Materiel::query();
                break;
        }
        
        // Application des filtres
        if ($filterDisponible !== null) {
            if ($filterDisponible === 'disponible') {
                $query->where('disponible', true);
            } elseif ($filterDisponible === 'indisponible') {
                $query->where('disponible', false);
            }
        }
        
        if ($search) {
            $query->where('nom_materiel', 'like', '%' . $search . '%');
        }
        
        $materiels = $query->paginate(15);

        // Si c'est une requête AJAX, retourner du JSON
        if ($request->get('ajax') == '1') {
            return response()->json($materiels->items());
        }

        return view('materiels.index', compact('materiels'));
    }

    /**
     * Show the form for creating a new resource.
     * 
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Seuls les administrateurs peuvent créer des matériels
        if (Auth::user()->role->name !== 'Administrateur') {
            return redirect()->route('materiels.index')
                ->with('error', 'Seuls les administrateurs peuvent créer des matériels.');
        }

        return view('materiels.create');
    }

    /**
     * Store a newly created resource in storage.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Seuls les administrateurs peuvent créer des matériels
        if (Auth::user()->role->name !== 'Administrateur') {
            return redirect()->route('materiels.index')
                ->with('error', 'Seuls les administrateurs peuvent créer des matériels.');
        }

        // Validation des données
        $validator = Validator::make($request->all(), [
            'nom_materiel' => 'required|string|max:255|unique:materiels,nom_materiel',
            'disponible' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Créer le matériel
        $materiel = Materiel::create([
            'nom_materiel' => $request->nom_materiel,
            'disponible' => $request->has('disponible') ? true : false,
        ]);

        return redirect()->route('materiels.show', $materiel)
            ->with('success', 'Matériel créé avec succès.');
    }

    /**
     * Display the specified resource.
     * 
     * @param  \App\Models\Materiel  $materiel
     * @return \Illuminate\Http\Response
     */
    public function show(Materiel $materiel)
    {
        $user = Auth::user();
        
        // Charger les réservations selon le rôle
        $reservations = collect();
        
        switch ($user->role->name) {
            case 'Administrateur':
                // Les administrateurs voient toutes les réservations du matériel
                $reservations = Reservation::where('item_type', 'materiel')
                    ->where('item_id', $materiel->id)
                    ->with('user')
                    ->orderBy('date_debut', 'desc')
                    ->paginate(10);
                break;

            case 'Enseignant':
                // Les enseignants voient leurs propres réservations + les approuvées
                $reservations = Reservation::where('item_type', 'materiel')
                    ->where('item_id', $materiel->id)
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
                $reservations = Reservation::where('item_type', 'materiel')
                    ->where('item_id', $materiel->id)
                    ->where('statut', 'approved')
                    ->where('date_fin', '>=', now())
                    ->with('user')
                    ->orderBy('date_debut', 'asc')
                    ->paginate(10);
                break;
        }

        return view('materiels.show', compact('materiel', 'reservations'));
    }

    /**
     * Show the form for editing the specified resource.
     * 
     * @param  \App\Models\Materiel  $materiel
     * @return \Illuminate\Http\Response
     */
    public function edit(Materiel $materiel)
    {
        // Seuls les administrateurs peuvent modifier les matériels
        if (Auth::user()->role->name !== 'Administrateur') {
            return redirect()->route('materiels.index')
                ->with('error', 'Seuls les administrateurs peuvent modifier les matériels.');
        }

        return view('materiels.edit', compact('materiel'));
    }

    /**
     * Update the specified resource in storage.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Materiel  $materiel
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Materiel $materiel)
    {
        // Seuls les administrateurs peuvent modifier les matériels
        if (Auth::user()->role->name !== 'Administrateur') {
            return redirect()->route('materiels.index')
                ->with('error', 'Seuls les administrateurs peuvent modifier les matériels.');
        }

        // Validation des données
        $validator = Validator::make($request->all(), [
            'nom_materiel' => 'required|string|max:255|unique:materiels,nom_materiel,' . $materiel->id,
            'disponible' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Mettre à jour le matériel
        $materiel->update([
            'nom_materiel' => $request->nom_materiel,
            'disponible' => $request->has('disponible') ? true : false,
        ]);

        return redirect()->route('materiels.show', $materiel)
            ->with('success', 'Matériel mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     * 
     * @param  \App\Models\Materiel  $materiel
     * @return \Illuminate\Http\Response
     */
    public function destroy(Materiel $materiel)
    {
        // Seuls les administrateurs peuvent supprimer les matériels
        if (Auth::user()->role->name !== 'Administrateur') {
            return redirect()->route('materiels.index')
                ->with('error', 'Seuls les administrateurs peuvent supprimer les matériels.');
        }

        // Vérifier s'il y a des réservations actives
        $hasActiveReservations = Reservation::where('item_type', \App\Models\Materiel::class)
            ->where('item_id', $materiel->id)
            ->where('statut', 'approved')
            ->where('date_fin', '>=', now())
            ->exists();

        if ($hasActiveReservations) {
            return redirect()->route('materiels.show', $materiel)
                ->with('error', 'Impossible de supprimer ce matériel car il a des réservations actives.');
        }

        $materiel->delete();

        return redirect()->route('materiels.index')
            ->with('success', 'Matériel supprimé avec succès.');
    }

    /**
     * Vérifier la disponibilité d'un matériel pour une période donnée
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Materiel  $materiel
     * @return \Illuminate\Http\Response
     */
    public function availability(Request $request, Materiel $materiel)
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
        $conflict = Reservation::where('item_type', \App\Models\Materiel::class)
            ->where('item_id', $materiel->id)
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

        $available = !$conflict && $materiel->disponible;

        return response()->json([
            'success' => true,
            'available' => $available,
            'materiel' => [
                'id' => $materiel->id,
                'nom_materiel' => $materiel->nom_materiel,
                'disponible' => $materiel->disponible
            ],
            'message' => $available ? 'Matériel disponible pour cette période' : 'Matériel non disponible pour cette période'
        ]);
    }

    /**
     * Obtenir le calendrier des réservations d'un matériel
     * 
     * @param  \App\Models\Materiel  $materiel
     * @return \Illuminate\Http\Response
     */
    public function calendar(Materiel $materiel)
    {
        $user = Auth::user();
        
        // Récupérer les réservations selon le rôle
        $query = Reservation::where('item_type', \App\Models\Materiel::class)
            ->where('item_id', $materiel->id);

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
            'materiel' => [
                'id' => $materiel->id,
                'nom_materiel' => $materiel->nom_materiel,
                'disponible' => $materiel->disponible
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
