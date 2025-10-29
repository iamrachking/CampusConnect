<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Reservation;
use App\Models\Salle;
use App\Models\Materiel;
use App\Models\Projet;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Statistiques générales
        $stats = [
            'reservations_actives' => Reservation::where('statut', 'approved')
                ->where('date_fin', '>=', now())
                ->count(),
            'salles_disponibles' => Salle::where('disponible', true)->count(),
            'materiel_disponible' => Materiel::where('disponible', true)->count(),
            'projets_en_cours' => Projet::count(),
        ];

        // Statistiques selon le rôle
        if ($user->role->name === 'Administrateur') {
            $stats['total_reservations'] = Reservation::count();
            $stats['reservations_en_attente'] = Reservation::where('statut', 'pending')->count();
            $stats['total_utilisateurs'] = User::count();
            $stats['total_salles'] = Salle::count();
            $stats['total_materiels'] = Materiel::count();
        } elseif ($user->role->name === 'Enseignant') {
            $stats['mes_reservations'] = Reservation::where('user_id', $user->id)->count();
            $stats['projets_encadres'] = Projet::where('encadrant_id', $user->id)->count();
        } elseif ($user->role->name === 'Étudiant') {
            $stats['mes_projets'] = Projet::whereHas('equipes', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })->count();
        }

        // Activité récente
        $recentActivities = collect();
        
        // Récupérer les réservations récentes
        $recentReservations = Reservation::with(['user', 'item'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Récupérer les projets récents
        $recentProjets = Projet::with(['encadrant'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Combiner et trier par date
        foreach($recentReservations as $reservation) {
            $recentActivities->push([
                'type' => 'reservation',
                'title' => 'Nouvelle réservation',
                'description' => $reservation->user->getFullNameAttribute() . ' a réservé ' . 
                    ($reservation->item_type === 'App\\Models\\Salle' ? $reservation->item->nom_salle : $reservation->item->nom_materiel),
                'date' => $reservation->created_at,
                'status' => $reservation->statut,
                'icon' => '📅'
            ]);
        }
        
        foreach($recentProjets as $projet) {
            $recentActivities->push([
                'type' => 'projet',
                'title' => 'Nouveau projet',
                'description' => '"' . $projet->titre . '" créé' . 
                    ($projet->encadrant ? ' par ' . $projet->encadrant->getFullNameAttribute() : ''),
                'date' => $projet->created_at,
                'status' => 'active',
                'icon' => '📚'
            ]);
        }
        
        $recentActivities = $recentActivities->sortByDesc('date')->take(5);

        return view('dashboard', compact('stats', 'recentActivities'));
    }
}