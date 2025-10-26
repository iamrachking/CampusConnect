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
        // Seeders pour les données de base
        $this->call([
            RoleSeeder::class,
            SalleSeeder::class,
            MaterielSeeder::class,
        ]);

        // Créer un utilisateur de test
        User::factory()->create([
            'nom' => 'John',
            'prenom' => 'Doe',
            'email' => 'test@example.com',
            'role_id' => 1, // Administrateur
        ]);
    }
}
