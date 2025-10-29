<?php

namespace App\Http\Controllers;

use App\Models\Equipe;
use App\Models\Projet;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class EquipeController extends Controller
{
    /**
     * Display a listing of the resource.
     * 
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        $equipes = collect();

        // Logique selon le rôle de l'utilisateur
        switch ($user->role->name) {
            case 'Administrateur':
                // Les administrateurs voient toutes les équipes
                $equipes = Equipe::with(['projet', 'user'])
                    ->orderBy('created_at', 'desc')
                    ->paginate(15);
                break;

            case 'Enseignant':
                // Les enseignants voient les équipes des projets qu'ils encadrent
                $equipes = Equipe::with(['projet', 'user'])
                    ->whereHas('projet', function($query) use ($user) {
                        $query->where('encadrant_id', $user->id);
                    })
                    ->orderBy('created_at', 'desc')
                    ->paginate(15);
                break;

            case 'Étudiant':
                // Les étudiants voient les équipes auxquelles ils participent
                $equipes = Equipe::with(['projet', 'user'])
                    ->where('user_id', $user->id)
                    ->orderBy('created_at', 'desc')
                    ->paginate(15);
                break;
        }

        return view('equipes.index', compact('equipes'));
    }

    /**
     * Show the form for creating a new resource.
     * 
     * @param  \App\Models\Projet  $projet
     * @return \Illuminate\Http\Response
     */
    public function create(Projet $projet)
    {
        $user = Auth::user();

        // Vérifier les permissions
        $canAddMember = false;

        switch ($user->role->name) {
            case 'Administrateur':
                $canAddMember = true;
                break;

            case 'Enseignant':
                // L'enseignant peut ajouter des membres aux projets qu'il encadre
                $canAddMember = ($projet->encadrant_id === $user->id);
                break;

            case 'Étudiant':
                // L'étudiant peut ajouter des membres s'il est chef de projet
                $canAddMember = $projet->equipes()
                    ->where('user_id', $user->id)
                    ->where('role_membre', 'Chef de projet')
                    ->exists();
                break;
        }

        if (!$canAddMember) {
            return redirect()->route('projets.show', $projet)
                ->with('error', 'Vous n\'avez pas l\'autorisation d\'ajouter des membres à ce projet.');
        }

        // Récupérer les étudiants disponibles (non membres du projet)
        $membresExistants = $projet->equipes()->pluck('user_id')->toArray();
        $etudiantsDisponibles = User::whereHas('role', function($query) {
            $query->where('name', 'Étudiant');
        })->whereNotIn('id', $membresExistants)->get();

        return view('equipes.create', compact('projet', 'etudiantsDisponibles'));
    }

    /**
     * Store a newly created resource in storage.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Projet  $projet
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Projet $projet)
    {
        $user = Auth::user();

        // Vérifier les permissions
        $canAddMember = false;

        switch ($user->role->name) {
            case 'Administrateur':
                $canAddMember = true;
                break;

            case 'Enseignant':
                $canAddMember = ($projet->encadrant_id === $user->id);
                break;

            case 'Étudiant':
                $canAddMember = $projet->equipes()
                    ->where('user_id', $user->id)
                    ->where('role_membre', 'Chef de projet')
                    ->exists();
                break;
        }

        if (!$canAddMember) {
            return redirect()->route('projets.show', $projet)
                ->with('error', 'Vous n\'avez pas l\'autorisation d\'ajouter des membres à ce projet.');
        }

        // Validation des données
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'role_membre' => 'required|string|in:Chef de projet,Membre,Développeur,Designer,Testeur',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Vérifier que l'utilisateur est un étudiant
        $etudiant = User::find($request->user_id);
        if (!$etudiant || $etudiant->role->name !== 'Étudiant') {
            return redirect()->back()
                ->with('error', 'Seuls les étudiants peuvent être ajoutés à une équipe.')
                ->withInput();
        }

        // Vérifier que l'utilisateur n'est pas déjà membre
        $alreadyMember = $projet->equipes()->where('user_id', $request->user_id)->exists();

        if ($alreadyMember) {
            return redirect()->back()
                ->with('error', 'Cet étudiant est déjà membre de ce projet.')
                ->withInput();
        }

        // Vérifier qu'il n'y a qu'un seul chef de projet
        if ($request->role_membre === 'Chef de projet') {
            $hasChefProjet = $projet->equipes()->where('role_membre', 'Chef de projet')->exists();
            
            if ($hasChefProjet) {
                return redirect()->back()
                    ->with('error', 'Il ne peut y avoir qu\'un seul chef de projet par projet.')
                    ->withInput();
            }
        }

        // Créer le membre d'équipe
        Equipe::create([
            'projet_id' => $projet->id,
            'user_id' => $request->user_id,
            'role_membre' => $request->role_membre,
        ]);

        return redirect()->route('projets.show', $projet)
            ->with('success', 'Membre ajouté à l\'équipe avec succès.');
    }

    /**
     * Display the specified resource.
     * 
     * @param  \App\Models\Equipe  $equipe
     * @return \Illuminate\Http\Response
     */
    public function show(Equipe $equipe)
    {
        $user = Auth::user();

        // Vérifier les permissions d'accès
        $canAccess = false;

        switch ($user->role->name) {
            case 'Administrateur':
                $canAccess = true;
                break;

            case 'Enseignant':
                $canAccess = ($equipe->projet->encadrant_id === $user->id);
                break;

            case 'Étudiant':
                $canAccess = ($equipe->user_id === $user->id || 
                             $equipe->projet->equipes()->where('user_id', $user->id)->exists());
                break;
        }

        if (!$canAccess) {
            return redirect()->route('equipes.index')
                ->with('error', 'Vous n\'avez pas accès à cette équipe.');
        }

        $equipe->load(['projet', 'user']);

        return view('equipes.show', compact('equipe'));
    }

    /**
     * Show the form for editing the specified resource.
     * 
     * @param  \App\Models\Equipe  $equipe
     * @return \Illuminate\Http\Response
     */
    public function edit(Equipe $equipe)
    {
        $user = Auth::user();

        // Vérifier les permissions
        $canEdit = false;

        switch ($user->role->name) {
            case 'Administrateur':
                $canEdit = true;
                break;

            case 'Enseignant':
                $canEdit = ($equipe->projet->encadrant_id === $user->id);
                break;

            case 'Étudiant':
                // L'étudiant peut modifier son propre rôle ou si il est chef de projet
                $canEdit = ($equipe->user_id === $user->id || 
                           $equipe->projet->equipes()
                               ->where('user_id', $user->id)
                               ->where('role_membre', 'Chef de projet')
                               ->exists());
                break;
        }

        if (!$canEdit) {
            return redirect()->route('projets.show', $equipe->projet)
                ->with('error', 'Vous n\'avez pas l\'autorisation de modifier ce membre.');
        }

        return view('equipes.edit', compact('equipe'));
    }

    /**
     * Update the specified resource in storage.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Equipe  $equipe
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Equipe $equipe)
    {
        $user = Auth::user();

        // Vérifier les permissions
        $canEdit = false;

        switch ($user->role->name) {
            case 'Administrateur':
                $canEdit = true;
                break;

            case 'Enseignant':
                $canEdit = ($equipe->projet->encadrant_id === $user->id);
                break;

            case 'Étudiant':
                $canEdit = ($equipe->user_id === $user->id || 
                           $equipe->projet->equipes()
                               ->where('user_id', $user->id)
                               ->where('role_membre', 'Chef de projet')
                               ->exists());
                break;
        }

        if (!$canEdit) {
            return redirect()->route('projets.show', $equipe->projet)
                ->with('error', 'Vous n\'avez pas l\'autorisation de modifier ce membre.');
        }

        // Validation des données
        $validator = Validator::make($request->all(), [
            'role_membre' => 'required|string|in:Chef de projet,Membre,Développeur,Designer,Testeur',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Vérifier qu'il n'y a qu'un seul chef de projet
        if ($request->role_membre === 'Chef de projet' && $equipe->role_membre !== 'Chef de projet') {
            $hasChefProjet = $equipe->projet->equipes()
                ->where('id', '!=', $equipe->id)
                ->where('role_membre', 'Chef de projet')
                ->exists();
            
            if ($hasChefProjet) {
                return redirect()->back()
                    ->with('error', 'Il ne peut y avoir qu\'un seul chef de projet par projet.')
                    ->withInput();
            }
        }

        // Mettre à jour le rôle
        $equipe->update([
            'role_membre' => $request->role_membre,
        ]);

        return redirect()->route('projets.show', $equipe->projet)
            ->with('success', 'Rôle du membre mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     * 
     * @param  \App\Models\Equipe  $equipe
     * @return \Illuminate\Http\Response
     */
    public function destroy(Equipe $equipe)
    {
        $user = Auth::user();

        // Vérifier les permissions
        $canRemove = false;

        switch ($user->role->name) {
            case 'Administrateur':
                $canRemove = true;
                break;

            case 'Enseignant':
                $canRemove = ($equipe->projet->encadrant_id === $user->id);
                break;

            case 'Étudiant':
                // L'étudiant peut se retirer lui-même ou si il est chef de projet
                $canRemove = ($equipe->user_id === $user->id || 
                             $equipe->projet->equipes()
                                 ->where('user_id', $user->id)
                                 ->where('role_membre', 'Chef de projet')
                                 ->exists());
                break;
        }

        if (!$canRemove) {
            return redirect()->route('projets.show', $equipe->projet)
                ->with('error', 'Vous n\'avez pas l\'autorisation de retirer ce membre.');
        }

        // Ne pas permettre au chef de projet de se retirer
        if ($equipe->role_membre === 'Chef de projet') {
            return redirect()->route('projets.show', $equipe->projet)
                ->with('error', 'Le chef de projet ne peut pas être retiré. Transférez d\'abord le rôle à un autre membre.');
        }

        $projet = $equipe->projet;
        $equipe->delete();

        return redirect()->route('projets.show', $projet)
            ->with('success', 'Membre retiré de l\'équipe avec succès.');
    }

    /**
     * Transférer le rôle de chef de projet
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Equipe  $equipe
     * @return \Illuminate\Http\Response
     */
    public function transferLeadership(Request $request, Equipe $equipe)
    {
        $user = Auth::user();

        // Seuls les chefs de projet peuvent transférer leur rôle
        if ($equipe->role_membre !== 'Chef de projet' || $equipe->user_id !== $user->id) {
            return redirect()->route('projets.show', $equipe->projet)
                ->with('error', 'Seul le chef de projet peut transférer son rôle.');
        }

        // Validation des données
        $validator = Validator::make($request->all(), [
            'new_chef_id' => 'required|exists:equipes,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $nouveauChef = Equipe::find($request->new_chef_id);

        // Vérifier que le nouveau chef est dans la même équipe
        if ($nouveauChef->projet_id !== $equipe->projet_id) {
            return redirect()->back()
                ->with('error', 'Le nouveau chef doit être membre de la même équipe.')
                ->withInput();
        }

        // Transférer les rôles
        $equipe->update(['role_membre' => 'Membre']);
        $nouveauChef->update(['role_membre' => 'Chef de projet']);

        return redirect()->route('projets.show', $equipe->projet)
            ->with('success', 'Rôle de chef de projet transféré avec succès.');
    }
}
