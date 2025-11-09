<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Projet;
use App\Models\Invitation;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    public function create()
    {
        $encadrants = User::whereHas('role', function($q) {
            $q->where('name', 'Enseignant');
        })->get();

        $etudiants = User::whereHas('role', function($q) {
            $q->where('name', 'Etudiant');
        })->get();

        return view('project.create', compact('encadrants', 'etudiants'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'required|string',
            'encadrant_id' => 'required|exists:users,id',
            'invitations' => 'nullable|array',
            'invitations.*' => 'exists:users,id',
        ]);

        
        $projet = Projet::create([
            'titre' => $request->titre,
            'description' => $request->description,
            'encadrant_id' => $request->encadrant_id,
            'creator_id' => Auth::id(),
        ]);

        
        $projet->equipes()->create([
            'user_id' => Auth::id(),
            'role_membre' => 'chef',
        ]);


        if($request->has('invitations')) {
            foreach($request->invitations as $destinataire_id) {

                if($destinataire_id == Auth::id()) 
                {
                    continue;
                }
                Invitation::create([
                    'projet_id' => $projet->id,
                    'expediteur_id' => Auth::id(),
                    'destinataire_id' => $destinataire_id,
                    'statut' => 'pending',
                    'is_read' => false,
                ]);
            }
        }

       return redirect()->back()->with('success', 'Projet créé avec succès !');
            
    }

    public function destroy(Projet $projet)
    {
        if(Auth::id() !== $projet->creator_id) {
            abort(403, 'Action non autorisée');
        }

        $projet->delete();

        return redirect()->back()->with('success', 'Projet créé avec succès !');
    }
}
