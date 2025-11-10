<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Salle;
use App\Models\Materiel;
use App\Models\Reservation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Purge des données avant seeds pour éviter les doublons
        // On nettoie les tables dépendantes en premier
        Reservation::query()->delete();
        Materiel::query()->delete();
        Salle::query()->delete();

        // 1) Rôles d'abord (FK sur users.role_id)
        $this->call([
            RoleSeeder::class,
        ]);

        // 2) Créer / mettre à jour les utilisateurs de test (mot de passe connu)
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'nom' => 'Admin',
                'prenom' => 'Test',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role_id' => 1,
            ]
        );

        User::updateOrCreate(
            ['email' => 'teacher@example.com'],
            [
                'nom' => 'Teacher',
                'prenom' => 'Test',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role_id' => 2,
            ]
        );

        User::updateOrCreate(
            ['email' => 'student@example.com'],
            [
                'nom' => 'Student',
                'prenom' => 'Test',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role_id' => 3,
            ]
        );

        // 3) Seeders dépendants
        $this->call([
            SalleSeeder::class,
            MaterielSeeder::class,
            ReservationSeeder::class,
        ]);
    }
}
