<?php

namespace App\Http\Controllers;

use App\Models\Livrable;
use App\Models\Projet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class LivrableController extends Controller
{
    /**
     * Display a listing of the resource.
     * 
     * @param  \App\Models\Projet  $projet
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Projet $projet)
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
            return redirect()->route('projets.index')
                ->with('error', "Vous n'avez pas accès aux livrables de ce projet.");
        }

        // Filtres
        $filterType = $request->get('type');
        $search = $request->get('search');

        $query = $projet->livrables()->with('user');

        if ($filterType) {
            $query->where('type_livrable', $filterType);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nom_livrable', 'like', '%' . $search . '%')
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('nom', 'like', '%' . $search . '%')
                         ->orWhere('prenom', 'like', '%' . $search . '%')
                         ->orWhere('email', 'like', '%' . $search . '%');
                  });
            });
        }

        $livrables = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('livrables.index', compact('projet', 'livrables'));
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

        // Seuls les étudiants membres du projet peuvent déposer des livrables
        if ($user->role->name !== 'Étudiant') {
            return redirect()->route('projets.show', $projet)
                ->with('error', 'Seuls les étudiants peuvent déposer des livrables.');
        }

        // Vérifier que l'utilisateur est membre du projet
        $isMember = $projet->equipes()->where('user_id', $user->id)->exists();

        if (!$isMember) {
            return redirect()->route('projets.show', $projet)
                ->with('error', 'Vous devez être membre du projet pour déposer des livrables.');
        }

        return view('livrables.create', compact('projet'));
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

        // Seuls les étudiants membres du projet peuvent déposer des livrables
        if ($user->role->name !== 'Étudiant') {
            return redirect()->route('projets.show', $projet)
                ->with('error', 'Seuls les étudiants peuvent déposer des livrables.');
        }

        // Vérifier que l'utilisateur est membre du projet
        $isMember = $projet->equipes()->where('user_id', $user->id)->exists();

        if (!$isMember) {
            return redirect()->route('projets.show', $projet)
                ->with('error', 'Vous devez être membre du projet pour déposer des livrables.');
        }

        // Validation des données
        $validator = Validator::make($request->all(), [
            'nom_livrable' => 'required|string|max:255',
            'type_livrable' => 'required|string|in:Rapport,Présentation,Code source,Documentation,Autre',
            'fichier' => 'required|file|mimetypes:application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-powerpoint,application/vnd.openxmlformats-officedocument.presentationml.presentation,text/plain,application/zip,application/x-rar-compressed,application/x-zip-compressed,application/octet-stream|mimes:pdf,doc,docx,ppt,pptx,txt,zip,rar|max:20480',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Traitement du fichier
        if ($request->hasFile('fichier')) {
            $file = $request->file('fichier');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('livrables', $filename, 'public');
        } else {
            return redirect()->back()
                ->with('error', 'Aucun fichier n\'a été téléchargé.')
                ->withInput();
        }

        // Créer le livrable
        $livrable = Livrable::create([
            'projet_id' => $projet->id,
            'user_id' => $user->id,
            'nom_livrable' => $request->nom_livrable,
            'url_livrable' => $path,
            'type_livrable' => $request->type_livrable,
        ]);

        return redirect()->route('projets.show', $projet)
            ->with('success', 'Livrable déposé avec succès.');
    }

    /**
     * Display the specified resource.
     * 
     * @param  \App\Models\Livrable  $livrable
     * @return \Illuminate\Http\Response
     */
    public function show(Livrable $livrable)
    {
        $user = Auth::user();

        // Vérifier les permissions d'accès
        $canAccess = false;

        switch ($user->role->name) {
            case 'Administrateur':
                $canAccess = true;
                break;

            case 'Enseignant':
                $canAccess = ($livrable->projet->encadrant_id === $user->id);
                break;

            case 'Étudiant':
                $canAccess = $livrable->projet->equipes()->where('user_id', $user->id)->exists();
                break;
        }

        if (!$canAccess) {
            return redirect()->route('projets.show', $livrable->projet)
                ->with('error', 'Vous n\'avez pas accès à ce livrable.');
        }

        $livrable->load(['projet', 'user']);

        return view('livrables.show', compact('livrable'));
    }

    /**
     * Show the form for editing the specified resource.
     * 
     * @param  \App\Models\Livrable  $livrable
     * @return \Illuminate\Http\Response
     */
    public function edit(Livrable $livrable)
    {
        $user = Auth::user();

        // Seuls les étudiants peuvent modifier leurs propres livrables
        if ($user->role->name !== 'Étudiant' || $livrable->user_id !== $user->id) {
            return redirect()->route('projets.show', $livrable->projet)
                ->with('error', 'Vous ne pouvez modifier que vos propres livrables.');
        }

        return view('livrables.edit', compact('livrable'));
    }

    /**
     * Update the specified resource in storage.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Livrable  $livrable
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Livrable $livrable)
    {
        $user = Auth::user();

        // Seuls les étudiants peuvent modifier leurs propres livrables
        if ($user->role->name !== 'Étudiant' || $livrable->user_id !== $user->id) {
            return redirect()->route('projets.show', $livrable->projet)
                ->with('error', 'Vous ne pouvez modifier que vos propres livrables.');
        }

        // Validation des données
        $validator = Validator::make($request->all(), [
            'nom_livrable' => 'required|string|max:255',
            'type_livrable' => 'required|string|in:Rapport,Présentation,Code source,Documentation,Autre',
            'fichier' => 'nullable|file|mimetypes:application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-powerpoint,application/vnd.openxmlformats-officedocument.presentationml.presentation,text/plain,application/zip,application/x-rar-compressed,application/x-zip-compressed,application/octet-stream|mimes:pdf,doc,docx,ppt,pptx,txt,zip,rar|max:20480',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = [
            'nom_livrable' => $request->nom_livrable,
            'type_livrable' => $request->type_livrable,
        ];

        // Traitement du nouveau fichier si fourni pour ne pas trainer avec des fichier unitile
        if ($request->hasFile('fichier')) {
            // Supprimer l'ancien fichier
            if ($livrable->url_livrable && Storage::disk('public')->exists($livrable->url_livrable)) {
                Storage::disk('public')->delete($livrable->url_livrable);
            }

            // Sauvegarder le nouveau fichier
            $file = $request->file('fichier');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('livrables', $filename, 'public');
            $data['url_livrable'] = $path;
        }

        // Mettre à jour le livrable
        $livrable->update($data);

        return redirect()->route('projets.show', $livrable->projet)
            ->with('success', 'Livrable mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     * 
     * @param  \App\Models\Livrable  $livrable
     * @return \Illuminate\Http\Response
     */
    public function destroy(Livrable $livrable)
    {
        $user = Auth::user();

        // Seuls les étudiants peuvent supprimer leurs propres livrables
        if ($user->role->name !== 'Étudiant' || $livrable->user_id !== $user->id) {
            return redirect()->route('projets.show', $livrable->projet)
                ->with('error', 'Vous ne pouvez supprimer que vos propres livrables.');
        }

        // Supprimer le fichier du stockage
        if ($livrable->url_livrable && Storage::disk('public')->exists($livrable->url_livrable)) {
            Storage::disk('public')->delete($livrable->url_livrable);
        }

        $projet = $livrable->projet;
        $livrable->delete();

        return redirect()->route('projets.show', $projet)
            ->with('success', 'Livrable supprimé avec succès.');
    }

    /**
     * Télécharger un livrable
     * 
     * @param  \App\Models\Livrable  $livrable
     * @return \Illuminate\Http\Response
     */
    public function download(Livrable $livrable)
    {
        $user = Auth::user();

        // Vérifier les permissions d'accès
        $canAccess = false;

        switch ($user->role->name) {
            case 'Administrateur':
                $canAccess = true;
                break;

            case 'Enseignant':
                $canAccess = ($livrable->projet->encadrant === $user->id);
                break;

            case 'Étudiant':
                $canAccess = $livrable->projet->equipes()->where('user_id', $user->id)->exists();
                break;
        }

        if (!$canAccess) {
            return redirect()->route('projets.show', $livrable->projet)
                ->with('error', 'Vous n\'avez pas accès à ce livrable.');
        }

        // Vérifier que le fichier existe
        if (!Storage::disk('public')->exists($livrable->url_livrable)) {
            return redirect()->route('projets.show', $livrable->projet)
                ->with('error', 'Le fichier n\'existe plus sur le serveur.');
        }

        // Obtenir le nom original du fichier
        $filename = basename($livrable->url_livrable);
        $originalName = substr($filename, strpos($filename, '_') + 1); // Enlever le timestamp

        return response()->download(storage_path('app/public/' . $livrable->url_livrable), $originalName);
    }

    /**
     * Obtenir les statistiques des livrables d'un projet
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
            return response()->json([
                'success' => false,
                'message' => 'Accès non autorisé'
            ], 403);
        }

        $stats = [
            'total_livrables' => $projet->livrables()->count(),
            'livrables_par_type' => $projet->livrables()
                ->selectRaw('type_livrable, COUNT(*) as count')
                ->groupBy('type_livrable')
                ->pluck('count', 'type_livrable'),
            'livrables_par_etudiant' => $projet->livrables()
                ->join('users', 'livrables.user_id', '=', 'users.id')
                ->selectRaw('users.name, COUNT(*) as count')
                ->groupBy('users.id', 'users.name')
                ->pluck('count', 'name'),
            'dernier_livrable' => $projet->livrables()
                ->with('user')
                ->latest()
                ->first(),
        ];

        return response()->json([
            'success' => true,
            'projet' => [
                'id' => $projet->id,
                'titre' => $projet->titre,
            ],
            'stats' => $stats
        ]);
    }
}
