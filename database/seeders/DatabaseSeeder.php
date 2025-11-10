<?php

namespace Database\Seeders;

use App\Models\User;
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
        // 1) Rôles d'abord (FK sur users.role_id)
        $this->call([
            RoleSeeder::class,
        ]);

        // 2) Créer / assurer les utilisateurs de test
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'nom' => 'Admin',
                'prenom' => 'Test',
                'password' => User::factory()->make()->password ?? \Illuminate\Support\Facades\Hash::make('password'),
                'role_id' => 1, // Administrateur
            ]
        );

        User::firstOrCreate(
            ['email' => 'teacher@example.com'],
            [
                'nom' => 'Teacher',
                'prenom' => 'Test',
                'password' => User::factory()->make()->password ?? \Illuminate\Support\Facades\Hash::make('password'),
                'role_id' => 2, // Enseignant
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
