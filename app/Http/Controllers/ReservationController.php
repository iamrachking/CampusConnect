<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Salle;
use App\Models\Materiel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ReservationController extends Controller
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
        $filterStatut = $request->get('statut');
        $filterType = $request->get('type');
        $search = $request->get('search');
        
        $reservations = collect();

        // Logique selon le rôle de l'utilisateur
        switch ($user->role->name) {
            case 'Administrateur':
                // Les administrateurs voient toutes les réservations
                $query = Reservation::with(['user', 'item']);
                break;

            case 'Enseignant':
                // Les enseignants voient leurs propres réservations
                $query = Reservation::with(['user', 'item'])
                    ->where('user_id', $user->id);
                break;

            case 'Étudiant':
                // Les étudiants voient seulement les réservations approuvées (pour consulter la disponibilité)
                $query = Reservation::with(['user', 'item'])
                    ->where('statut', 'approved');
                break;
        }
        
        // Appliquer les filtres
        if ($filterStatut) {
            $query->where('statut', $filterStatut);
        }
        
        if ($filterType) {
            $query->where('item_type', $filterType === 'salle' ? Salle::class : Materiel::class);
        }
        
        if ($search) {
            $query->where('motif', 'like', '%' . $search . '%');
        }
        
        // Ordre selon le rôle
        if ($user->role->name === 'Étudiant') {
            $query->orderBy('date_debut', 'asc');
        } else {
            $query->orderBy('created_at', 'desc');
        }
        
        $reservations = $query->paginate(15);

        return view('reservations.index', compact('reservations'));
    }

    /**
     * Show the form for creating a new resource.
     * 
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Seuls les enseignants peuvent créer des réservations
        if (Auth::user()->role->name !== 'Enseignant') {
            return redirect()->route('reservations.index')
                ->with('error', 'Seuls les enseignants peuvent créer des réservations.');
        }

        $salles = Salle::where('disponible', true)->get();
        $materiels = Materiel::where('disponible', true)->get();

        return view('reservations.create', compact('salles', 'materiels'));
    }

    /**
     * Store a newly created resource in storage.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Seuls les enseignants peuvent créer des réservations
        if (Auth::user()->role->name !== 'Enseignant') {
            return redirect()->route('reservations.index')
                ->with('error', 'Seuls les enseignants peuvent créer des réservations.');
        }

        // Validation des données
        $validator = Validator::make($request->all(), [
            'item_type' => 'required|in:salle,materiel',
            'item_id' => 'required|integer',
            'date_debut' => 'required|date|after:now',
            'date_fin' => 'required|date|after:date_debut',
            'motif' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Vérifier que l'item existe et est disponible
        if ($request->item_type === 'salle') {
            $item = Salle::find($request->item_id);
        } else {
            $item = Materiel::find($request->item_id);
        }

        if (!$item || !$item->disponible) {
            return redirect()->back()
                ->with('error', "L'élément sélectionné n'est pas disponible.")
                ->withInput();
        }

        // Vérifier les conflits de réservation
        $conflict = Reservation::where('item_type', $request->item_type === 'salle' ? Salle::class : Materiel::class)
            ->where('item_id', $request->item_id)
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

        if ($conflict) {
            return redirect()->back()
                ->with('error', 'Une réservation existe déjà pour cette période.')
                ->withInput();
        }

        // Créer la réservation
        $reservation = Reservation::create([
            'user_id' => Auth::id(),
            'item_type' => $request->item_type === 'salle' ? Salle::class : Materiel::class,
            'item_id' => $request->item_id,
            'date_debut' => $request->date_debut,
            'date_fin' => $request->date_fin,
            'statut' => 'pending',
            'motif' => $request->motif,
        ]);

        return redirect()->route('reservations.show', $reservation)
            ->with('success', 'Réservation créée avec succès. En attente de validation.');
    }

    /**
     * Display the specified resource.
     * 
     * @param  \App\Models\Reservation  $reservation
     * @return \Illuminate\Http\Response
     */
    public function show(Reservation $reservation)
    {
        $user = Auth::user();

        // Vérifier les permissions d'accès
        if ($user->role->name === 'Enseignant' && $reservation->user_id !== $user->id) {
            return redirect()->route('reservations.index')
                ->with('error', 'Vous ne pouvez voir que vos propres réservations.');
        }

        if ($user->role->name === 'Étudiant' && $reservation->statut !== 'approved') {
            return redirect()->route('reservations.index')
                ->with('error', 'Vous ne pouvez voir que les réservations approuvées.');
        }

        $reservation->load(['user', 'item']);

        return view('reservations.show', compact('reservation'));
    }

    /**
     * Show the form for editing the specified resource.
     * 
     * @param  \App\Models\Reservation  $reservation
     * @return \Illuminate\Http\Response
     */
    public function edit(Reservation $reservation)
    {
        // Seuls les enseignants peuvent modifier leurs propres réservations
        if (Auth::user()->role->name !== 'Enseignant' || $reservation->user_id !== Auth::id()) {
            return redirect()->route('reservations.index')
                ->with('error', 'Vous ne pouvez modifier que vos propres réservations.');
        }

        // Ne pas permettre la modification si déjà approuvée ou rejetée
        if ($reservation->statut !== 'pending') {
            return redirect()->route('reservations.show', $reservation)
                ->with('error', 'Cette réservation ne peut plus être modifiée.');
        }

        $salles = Salle::where('disponible', true)->get();
        $materiels = Materiel::where('disponible', true)->get();

        return view('reservations.edit', compact('reservation', 'salles', 'materiels'));
    }

    /**
     * Update the specified resource in storage.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Reservation  $reservation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Reservation $reservation)
    {
        // Seuls les enseignants peuvent modifier leurs propres réservations
        if (Auth::user()->role->name !== 'Enseignant' || $reservation->user_id !== Auth::id()) {
            return redirect()->route('reservations.index')
                ->with('error', 'Vous ne pouvez modifier que vos propres réservations.');
        }

        // Ne pas permettre la modification si déjà approuvée ou rejetée
        if ($reservation->statut !== 'pending') {
            return redirect()->route('reservations.show', $reservation)
                ->with('error', 'Cette réservation ne peut plus être modifiée.');
        }

        // Validation des données
        $validator = Validator::make($request->all(), [
            'item_type' => 'required|in:salle,materiel',
            'item_id' => 'required|integer',
            'date_debut' => 'required|date|after:now',
            'date_fin' => 'required|date|after:date_debut',
            'motif' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Vérifier que l'item existe et est disponible
        if ($request->item_type === 'salle') {
            $item = Salle::find($request->item_id);
        } else {
            $item = Materiel::find($request->item_id);
        }

        if (!$item || !$item->disponible) {
            return redirect()->back()
                ->with('error', 'L\'élément sélectionné n\'est pas disponible.')
                ->withInput();
        }

        // Vérifier les conflits de réservation (en excluant la réservation actuelle)
        $conflict = Reservation::where('item_type', $request->item_type === 'salle' ? Salle::class : Materiel::class)
            ->where('item_id', $request->item_id)
            ->where('id', '!=', $reservation->id)
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

        if ($conflict) {
            return redirect()->back()
                ->with('error', 'Une réservation existe déjà pour cette période.')
                ->withInput();
        }

        // Mettre à jour la réservation
        $reservation->update([
            'item_type' => $request->item_type === 'salle' ? Salle::class : Materiel::class,
            'item_id' => $request->item_id,
            'date_debut' => $request->date_debut,
            'date_fin' => $request->date_fin,
            'motif' => $request->motif,
        ]);

        return redirect()->route('reservations.show', $reservation)
            ->with('success', 'Réservation mise à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     * 
     * @param  \App\Models\Reservation  $reservation
     * @return \Illuminate\Http\Response
     */
    public function destroy(Reservation $reservation)
    {
        // Seuls les enseignants peuvent supprimer leurs propres réservations
        if (Auth::user()->role->name !== 'Enseignant' || $reservation->user_id !== Auth::id()) {
            return redirect()->route('reservations.index')
                ->with('error', 'Vous ne pouvez supprimer que vos propres réservations.');
        }

        // Ne pas permettre la suppression si déjà approuvée
        if ($reservation->statut === 'approved') {
            return redirect()->route('reservations.show', $reservation)
                ->with('error', 'Cette réservation ne peut plus être supprimée car elle est approuvée.');
        }

        $reservation->delete();

        return redirect()->route('reservations.index')
            ->with('success', 'Réservation supprimée avec succès.');
    }

    /**
     * Approuver une réservation (Administrateur uniquement)
     * 
     * @param  \App\Models\Reservation  $reservation
     * @return \Illuminate\Http\Response
     */
    public function approve(Reservation $reservation)
    {
        if (Auth::user()->role->name !== 'Administrateur') {
            return redirect()->route('reservations.index')
                ->with('error', 'Seuls les administrateurs peuvent approuver les réservations.');
        }

        if ($reservation->statut !== 'pending') {
            return redirect()->route('reservations.show', $reservation)
                ->with('error', 'Cette réservation a déjà été traitée.');
        }

        $reservation->update(['statut' => 'approved']);

        return redirect()->route('reservations.show', $reservation)
            ->with('success', 'Réservation approuvée avec succès.');
    }

    /**
     * Rejeter une réservation (Administrateur uniquement)
     * 
     * @param  \App\Models\Reservation  $reservation
     * @return \Illuminate\Http\Response
     */
    public function reject(Reservation $reservation)
    {
        if (Auth::user()->role->name !== 'Administrateur') {
            return redirect()->route('reservations.index')
                ->with('error', 'Seuls les administrateurs peuvent rejeter les réservations.');
        }

        if ($reservation->statut !== 'pending') {
            return redirect()->route('reservations.show', $reservation)
                ->with('error', 'Cette réservation a déjà été traitée.');
        }

        $reservation->update(['statut' => 'rejected']);

        return redirect()->route('reservations.show', $reservation)
            ->with('success', 'Réservation rejetée.');
    }
}
