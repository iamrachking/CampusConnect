<?php

namespace App\Http\Controllers;

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
            $stats['total_etudiants'] = User::whereHas('role', function($q) { $q->where('name', 'Étudiant'); })->count();
            $stats['total_enseignants'] = User::whereHas('role', function($q) { $q->where('name', 'Enseignant'); })->count();
        } elseif ($user->role->name === 'Enseignant') {
            // Statistiques réelles pour l'enseignant
            $stats['mes_reservations'] = Reservation::where('user_id', $user->id)->count();
            $stats['projets_encadres'] = Projet::where('encadrant_id', $user->id)->count();
            
            // Calculer les heures réelles de réservations cette semaine
            $debutSemaine = now()->startOfWeek();
            $finSemaine = now()->endOfWeek();
            $reservationsCetteSemaine = Reservation::where('user_id', $user->id)
                ->whereBetween('date_debut', [$debutSemaine, $finSemaine])
                ->get();
            
            // Calculer le total d'heures
            $heuresTotal = 0;
            foreach($reservationsCetteSemaine as $reservation) {
                $heuresTotal += $reservation->date_debut->diffInHours($reservation->date_fin);
            }
            $stats['heures_semaine'] = $heuresTotal;
            $stats['cours_semaine'] = $reservationsCetteSemaine->count();
            
            // Trouver le jour le plus chargé et le moins chargé
            $joursCharges = [];
            $traductionJours = [
                'Monday' => 'Lundi',
                'Tuesday' => 'Mardi',
                'Wednesday' => 'Mercredi',
                'Thursday' => 'Jeudi',
                'Friday' => 'Vendredi',
                'Saturday' => 'Samedi',
                'Sunday' => 'Dimanche'
            ];
            
            foreach($reservationsCetteSemaine as $reservation) {
                $jourEn = $reservation->date_debut->format('l');
                $jourFr = $traductionJours[$jourEn] ?? $jourEn;
                $joursCharges[$jourFr] = ($joursCharges[$jourFr] ?? 0) + 1;
            }
            arsort($joursCharges);
            $stats['jour_plus_charge'] = !empty($joursCharges) ? array_key_first($joursCharges) : 'Aucun';
            $stats['jour_moins_charge'] = !empty($joursCharges) ? array_key_last($joursCharges) : 'Aucun';
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
        
        // Récupérer les projets récents avec les équipes et utilisateurs
        $recentProjets = Projet::with(['equipes.user', 'encadrant'])
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
            // Trouver le créateur (chef de projet)
            $createur = $projet->equipes->where('role_membre', 'Chef de projet')->first();
            $nomCreateur = $createur && $createur->user ? $createur->user->getFullNameAttribute() : 'Un étudiant';
            
            $recentActivities->push([
                'type' => 'projet',
                'title' => 'Nouveau projet',
                'description' => '"' . $projet->titre . '" créé par ' . $nomCreateur . 
                    ($projet->encadrant ? ' et encadré par ' . $projet->encadrant->getFullNameAttribute() : ''),
                'date' => $projet->created_at,
                'status' => 'active',
                'icon' => '📚'
            ]);
        }
        
        $recentActivities = $recentActivities->sortByDesc('date')->take(5);

        // Pour l'admin : récupérer les réservations en attente
        $pendingReservations = collect();
        if ($user->role->name === 'Administrateur') {
            $pendingReservations = Reservation::where('statut', 'pending')
                ->with(['user', 'item'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        }

        return view('dashboard', compact('stats', 'recentActivities', 'pendingReservations'));
    }
}