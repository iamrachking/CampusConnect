<?php

namespace App\Http\Controllers;

use App\Models\Projet;
use App\Models\User;
use App\Models\Equipe;
use App\Models\Livrable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProjetController extends Controller
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
        $filterEncadrant = $request->get('encadrant');
        $search = $request->get('search');
        
        $projets = collect();

        // Logique selon le rôle de l'utilisateur
        switch ($user->role->name) {
            case 'Administrateur':
                // Les administrateurs voient tous les projets
                $query = Projet::with(['encadrant', 'equipes.user', 'livrables']);
                break;

            case 'Enseignant':
                // Les enseignants voient les projets qu'ils encadrent
                $query = Projet::with(['encadrant', 'equipes.user', 'livrables'])
                    ->where('encadrant_id', $user->id);
                break;

            case 'Étudiant':
                // Les étudiants voient les projets auxquels ils participent
                $query = Projet::with(['encadrant', 'equipes.user', 'livrables'])
                    ->whereHas('equipes', function($query) use ($user) {
                        $query->where('user_id', $user->id);
                    });
                break;
        }
        
        // Appliquer les filtres
        if ($filterEncadrant) {
            $query->where('encadrant_id', $filterEncadrant);
        }
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('titre', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }
        
        $projets = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('projets.index', compact('projets'));
    }

    /**
     * Show the form for creating a new resource.
     * 
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Seuls les étudiants peuvent créer des projets
        if (Auth::user()->role->name !== 'Étudiant') {
            return redirect()->route('projets.index')
                ->with('error', 'Seuls les étudiants peuvent créer des projets.');
        }

        // Récupérer les enseignants disponibles pour l'encadrement du projets creer 
        $enseignants = User::whereHas('role', function($query) {
            $query->where('name', 'Enseignant');
        })->get();

        return view('projets.create', compact('enseignants'));
    }

    /**
     * Store a newly created resource in storage.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Seuls les étudiants peuvent créer des projets
        if (Auth::user()->role->name !== 'Étudiant') {
            return redirect()->route('projets.index')
                ->with('error', 'Seuls les étudiants peuvent créer des projets.');
        }

        // Validation des données
        $validator = Validator::make($request->all(), [
            'titre' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'encadrant' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Vérifier que l'encadrant est bien un enseignant
        $encadrant = User::find($request->encadrant);
        if (!$encadrant || $encadrant->role->name !== 'Enseignant') {
            return redirect()->back()
                ->with('error', 'L\'encadrant sélectionné n\'est pas un enseignant.')
                ->withInput();
        }

        // Créer le projet
        $projet = Projet::create([
            'titre' => $request->titre,
            'description' => $request->description,
            'encadrant_id' => $request->encadrant,
        ]);

        // Ajouter le créateur comme membre de l'équipe avec le rôle "Chef de projet"
        Equipe::create([
            'projet_id' => $projet->id,
            'user_id' => Auth::id(),
            'role_membre' => 'Chef de projet',
        ]);

        return redirect()->route('projets.show', $projet)
            ->with('success', 'Projet créé avec succès. Vous êtes automatiquement ajouté comme chef de projet.');
    }

    /**
     * Display the specified resource.
     * 
     * @param  \App\Models\Projet  $projet
     * @return \Illuminate\Http\Response
     */
    public function show(Projet $projet)
    {
        $user = Auth::user();

        // Vérifier les permissions d'accès
        $canAccess = false;

        switch ($user->role->name) {
            case 'Administrateur':
                $canAccess = true;
                break;

            case 'Enseignant':
                // L'enseignant peut voir les projets qu'il encadre
                $canAccess = ($projet->encadrant_id === $user->id);
                break;

            case 'Étudiant':
                // L'étudiant peut voir les projets auxquels il participe
                $canAccess = $projet->equipes()->where('user_id', $user->id)->exists();
                break;
        }

        if (!$canAccess) {
            return redirect()->route('projets.index')
                ->with('error', 'Vous n\'avez pas accès à ce projet.');
        }

        // Charger les relations
        $projet->load(['encadrant', 'equipes.user', 'livrables.user']);

        // Récupérer les membres de l'équipe
        $membresEquipe = $projet->equipes()->with('user')->get();

        // Récupérer les livrables
        $livrables = $projet->livrables()->with('user')->orderBy('created_at', 'desc')->get();

        return view('projets.show', compact('projet', 'membresEquipe', 'livrables'));
    }

    /**
     * Show the form for editing the specified resource.
     * 
     * @param  \App\Models\Projet  $projet
     * @return \Illuminate\Http\Response
     */
    public function edit(Projet $projet)
    {
        $user = Auth::user();

        // Seuls les étudiants peuvent modifier les projets, et seulement s'ils sont chef de projet
        if ($user->role->name !== 'Étudiant') {
            return redirect()->route('projets.index')
                ->with('error', 'Seuls les étudiants peuvent modifier les projets.');
        }

        // Vérifier que l'utilisateur est chef de projet
        $isChefProjet = $projet->equipes()
            ->where('user_id', $user->id)
            ->where('role_membre', 'Chef de projet')
            ->exists();

        if (!$isChefProjet) {
            return redirect()->route('projets.show', $projet)
                ->with('error', 'Seul le chef de projet peut modifier les informations du projet.');
        }

        // Récupérer les enseignants disponibles pour l'encadrement
        $enseignants = User::whereHas('role', function($query) {
            $query->where('name', 'Enseignant');
        })->get();

        return view('projets.edit', compact('projet', 'enseignants'));
    }

    /**
     * Update the specified resource in storage.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Projet  $projet
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Projet $projet)
    {
        $user = Auth::user();

        // Seuls les étudiants peuvent modifier les projets, et seulement s'ils sont chef de projet
        if ($user->role->name !== 'Étudiant') {
            return redirect()->route('projets.index')
                ->with('error', 'Seuls les étudiants peuvent modifier les projets.');
        }

        // Vérifier que l'utilisateur est chef de projet
        $isChefProjet = $projet->equipes()
            ->where('user_id', $user->id)
            ->where('role_membre', 'Chef de projet')
            ->exists();

        if (!$isChefProjet) {
            return redirect()->route('projets.show', $projet)
                ->with('error', 'Seul le chef de projet peut modifier les informations du projet.');
        }

        // Validation des données
        $validator = Validator::make($request->all(), [
            'titre' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'encadrant' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Vérifier que l'encadrant est bien un enseignant
        $encadrant = User::find($request->encadrant);
        if (!$encadrant || $encadrant->role->name !== 'Enseignant') {
            return redirect()->back()
                ->with('error', 'L\'encadrant sélectionné n\'est pas un enseignant.')
                ->withInput();
        }

        // Mettre à jour le projet
        $projet->update([
            'titre' => $request->titre,
            'description' => $request->description,
            'encadrant_id' => $request->encadrant,
        ]);

        return redirect()->route('projets.show', $projet)
            ->with('success', 'Projet mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     * 
     * @param  \App\Models\Projet  $projet
     * @return \Illuminate\Http\Response
     */
    public function destroy(Projet $projet)
    {
        $user = Auth::user();

        // Seuls les étudiants peuvent supprimer les projets, et seulement s'ils sont chef de projet
        if ($user->role->name !== 'Étudiant') {
            return redirect()->route('projets.index')
                ->with('error', 'Seuls les étudiants peuvent supprimer les projets.');
        }

        // Vérifier que l'utilisateur est chef de projet
        $isChefProjet = $projet->equipes()
            ->where('user_id', $user->id)
            ->where('role_membre', 'Chef de projet')
            ->exists();

        if (!$isChefProjet) {
            return redirect()->route('projets.show', $projet)
                ->with('error', 'Seul le chef de projet peut supprimer le projet.');
        }

        // Vérifier s'il y a des livrables
        $hasLivrables = $projet->livrables()->exists();

        if ($hasLivrables) {
            return redirect()->route('projets.show', $projet)
                ->with('error', 'Impossible de supprimer ce projet car il contient des livrables.');
        }

        // Supprimer le projet (les équipes seront supprimées automatiquement par la contrainte de clé étrangère)
        $projet->delete();

        return redirect()->route('projets.index')
            ->with('success', 'Projet supprimé avec succès.');
    }

    /**
     * Rejoindre un projet (pour les étudiants)
     * 
     * @param  \App\Models\Projet  $projet
     * @return \Illuminate\Http\Response
     */
    public function join(Projet $projet)
    {
        $user = Auth::user();

        // Seuls les étudiants peuvent rejoindre des projets
        if ($user->role->name !== 'Étudiant') {
            return redirect()->route('projets.index')
                ->with('error', 'Seuls les étudiants peuvent rejoindre des projets.');
        }

        // Vérifier que l'utilisateur n'est pas déjà membre
        $alreadyMember = $projet->equipes()->where('user_id', $user->id)->exists();

        if ($alreadyMember) {
            return redirect()->route('projets.show', $projet)
                ->with('error', 'Vous êtes déjà membre de ce projet.');
        }

        // Ajouter l'utilisateur à l'équipe avec le rôle "Membre"
        Equipe::create([
            'projet_id' => $projet->id,
            'user_id' => $user->id,
            'role_membre' => 'Membre',
        ]);

        return redirect()->route('projets.show', $projet)
            ->with('success', 'Vous avez rejoint le projet avec succès.');
    }

    /**
     * Quitter un projet (pour les étudiants)
     * 
     * @param  \App\Models\Projet  $projet
     * @return \Illuminate\Http\Response
     */
    public function leave(Projet $projet)
    {
        $user = Auth::user();

        // Seuls les étudiants peuvent quitter des projets
        if ($user->role->name !== 'Étudiant') {
            return redirect()->route('projets.index')
                ->with('error', 'Seuls les étudiants peuvent quitter des projets.');
        }

        // Vérifier que l'utilisateur est membre
        $equipe = $projet->equipes()->where('user_id', $user->id)->first();

        if (!$equipe) {
            return redirect()->route('projets.show', $projet)
                ->with('error', 'Vous n\'êtes pas membre de ce projet.');
        }

        // Ne pas permettre au chef de projet de quitter
        if ($equipe->role_membre === 'Chef de projet') {
            return redirect()->route('projets.show', $projet)
                ->with('error', 'Le chef de projet ne peut pas quitter le projet. Transférez d\'abord le rôle à un autre membre.');
        }

        // Supprimer l'utilisateur de l'équipe
        $equipe->delete();

        return redirect()->route('projets.index')
            ->with('success', 'Vous avez quitté le projet avec succès.');
    }

    /**
     * Obtenir les statistiques d'un projet
     * 
     * @param  \App\Models\Projet  $projet
     * @return \Illuminate\Http\Response
     */
    public function stats(Projet $projet)
    {
        $user = Auth::user();

        // Vérifier les permissions d'accès
        $canAccess = false;

        switch ($user->role->name) {
            case 'Administrateur':
                $canAccess = true;
                break;

            case 'Enseignant':
                $canAccess = ($projet->encadrant_id === $user->id);
                break;

            case 'Étudiant':
                $canAccess = $projet->equipes()->where('user_id', $user->id)->exists();
                break;
        }

        if (!$canAccess) {
            return redirect()->route('projets.show', $projet)
                ->with('error', 'Accès non autorisé');
        }

        // Charger les relations nécessaires
        $projet->load(['encadrant', 'equipes.user', 'livrables.user']);

        // Calculer les statistiques
        $totalMembres = $projet->equipes()->count();
        $totalLivrables = $projet->livrables()->count();
        $dernierLivrable = $projet->livrables()->latest()->first();
        
        // Membres par rôle
        $membresParRole = $projet->equipes()
            ->selectRaw('role_membre, COUNT(*) as count')
            ->groupBy('role_membre')
            ->get()
            ->pluck('count', 'role_membre');

        // Livrables par type
        $livrablesParType = $projet->livrables()
            ->selectRaw('type_livrable, COUNT(*) as count')
            ->groupBy('type_livrable')
            ->get()
            ->pluck('count', 'type_livrable');

        // Livrables par étudiant
        $livrablesParEtudiant = $projet->livrables()
            ->join('users', 'livrables.user_id', '=', 'users.id')
            ->selectRaw('users.prenom, users.nom, COUNT(*) as count')
            ->groupBy('users.id', 'users.prenom', 'users.nom')
            ->get()
            ->map(function ($item) {
                return [
                    'nom' => $item->prenom . ' ' . $item->nom,
                    'count' => $item->count
                ];
            });

        return view('projets.stats', compact(
            'projet',
            'totalMembres',
            'totalLivrables',
            'dernierLivrable',
            'membresParRole',
            'livrablesParType',
            'livrablesParEtudiant'
        ));
    }
}
