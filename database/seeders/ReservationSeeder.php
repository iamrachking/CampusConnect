<?php

namespace Database\Seeders;

use App\Models\Reservation;
use App\Models\Salle;
use App\Models\Materiel;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class ReservationSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $teacher = User::where('email', 'teacher@example.com')->first();
        if (!$teacher) {
            $teacher = User::factory()->create([
                'nom' => 'Teacher',
                'prenom' => 'Seed',
                'email' => 'teacher@example.com',
                'role_id' => 2,
            ]);
        }

        $salle = Salle::first();
        $materiel = Materiel::first();

        if ($salle) {
            Reservation::create([
                'user_id' => $teacher->id,
                'item_type' => Salle::class,
                'item_id' => $salle->id,
                'date_debut' => Carbon::now()->addDay()->setTime(9, 0),
                'date_fin' => Carbon::now()->addDay()->setTime(11, 0),
                'statut' => 'pending',
                'motif' => 'Cours de mathématiques',
            ]);
        }

        if ($materiel) {
            Reservation::create([
                'user_id' => $teacher->id,
                'item_type' => Materiel::class,
                'item_id' => $materiel->id,
                'date_debut' => Carbon::now()->addDays(2)->setTime(14, 0),
                'date_fin' => Carbon::now()->addDays(2)->setTime(16, 0),
                'statut' => 'pending',
                'motif' => 'Présentation projet',
            ]);
        }
    }
}