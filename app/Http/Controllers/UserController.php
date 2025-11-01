<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewUserCreatedMail;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Display a listing of users (admin only).
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Seuls les administrateurs peuvent voir la liste des utilisateurs
        if (Auth::user()->role->name !== 'Administrateur') {
            return redirect()->route('dashboard')
                ->with('error', 'Accès non autorisé.');
        }

        $users = User::with('role')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user (admin only).
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Seuls les administrateurs peuvent créer des utilisateurs
        if (Auth::user()->role->name !== 'Administrateur') {
            return redirect()->route('dashboard')
                ->with('error', 'Accès non autorisé.');
        }

        $roles = Role::whereIn('name', ['Administrateur', 'Enseignant'])->get();

        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created user in storage (admin only).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Seuls les administrateurs peuvent créer des utilisateurs
        if (Auth::user()->role->name !== 'Administrateur') {
            return redirect()->route('dashboard')
                ->with('error', 'Accès non autorisé.');
        }

        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'role_id' => 'required|exists:roles,id',
        ]);

        if ($validator->fails()) {
            return redirect()->route('users.create')
                ->withErrors($validator)
                ->withInput();
        }

        // Génération d'un mot de passe aléatoire
        $temporaryPassword = Str::random(12);

        // Création de l'utilisateur
        $user = User::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'password' => Hash::make($temporaryPassword),
            'role_id' => $request->role_id,
        ]);

        // Envoyer l'email avec les identifiants
        try {
            Mail::to($user->email)->send(new NewUserCreatedMail($user, $temporaryPassword));
            return redirect()->route('users.index')
                ->with('success', "L'utilisateur a été créé avec succès. Un email avec les identifiants a été envoyé à " . $user->email . ".");
        } catch (\Exception $e) {
            // Si l'envoi d'email échoue, normalement on affiche quand même le mot de passe pour que sa soit envoyer manuellement mais j'ai decider de ne pas afficher le mot de passe , je me dis que l'envoie de mail ne va pas echouer 
            return redirect()->route('users.index')
                ->with('success', "L'utilisateur a été créé avec succès. Erreur lors de l'envoi de l'email. Email : " . $user->email . ".")
                ->with('warning', "Attention : L'envoi de l'email a échoué. Veuillez communiquer les identifiants manuellement.");
        }
    }

    /**
     * Display the specified user (admin only).
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        // Seuls les administrateurs peuvent voir les détails des utilisateurs
        if (Auth::user()->role->name !== 'Administrateur') {
            return redirect()->route('dashboard')
                ->with('error', 'Accès non autorisé.');
        }

        $user->load('role');
        

        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user (admin only).
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        // Seuls les administrateurs peuvent modifier les utilisateurs
        if (Auth::user()->role->name !== 'Administrateur') {
            return redirect()->route('dashboard')
                ->with('error', 'Accès non autorisé.');
        }

        $roles = Role::whereIn('name', ['Administrateur', 'Enseignant'])->get();

        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user in storage (admin only).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        // Seuls les administrateurs peuvent modifier les utilisateurs
        if (Auth::user()->role->name !== 'Administrateur') {
            return redirect()->route('dashboard')
                ->with('error', 'Accès non autorisé.');
        }

        $validator = Validator::make($request->all(), [
            // 'nom' => 'required|string|max:255',
            // 'prenom' => 'required|string|max:255',
            // 'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role_id' => 'required|exists:roles,id',
        ]);

        if ($validator->fails()) {
            return redirect()->route('users.edit', $user)
                ->withErrors($validator)
                ->withInput();
        }
        /* ici je me suis dit que la mise a jour d'un user cote admin , sa serait uniquement son role , car pour les autre information il peut le faire directement dans son compte a lui meme */
        $user->update([
            // 'nom' => $request->nom,
            // 'prenom' => $request->prenom,
            // 'email' => $request->email,
            'role_id' => $request->role_id,
        ]);

        return redirect()->route('users.index')
            ->with('success', "Le rôle de l'utilisateur a été modifié avec succès.");
    }

    /**
     * Remove the specified user from storage (admin only).
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        // Seuls les administrateurs peuvent supprimer des utilisateurs
        if (Auth::user()->role->name !== 'Administrateur') {
            return redirect()->route('dashboard')
                ->with('error', 'Accès non autorisé.');
        }

        // Empêcher la suppression de l'utilisateur actuellement connecté
        if ($user->id === Auth::id()) {
            return redirect()->route('users.index')
            ->with('error', "Vous ne pouvez pas supprimer votre propre compte.");
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', "L'utilisateur a été supprimé avec succès.");
    }
}

