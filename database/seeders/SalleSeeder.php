<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SalleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $salles = [
            [
                'nom_salle' => 'Amphithéâtre A',
                'capacite' => 200,
                'disponible' => true,
                'localisation' => 'Bâtiment Principal - 1er étage'
            ],
            [
                'nom_salle' => 'Salle de cours B1',
                'capacite' => 30,
                'disponible' => true,
                'localisation' => 'Bâtiment B - Rez-de-chaussée'
            ],
            [
                'nom_salle' => 'Salle de cours B2',
                'capacite' => 30,
                'disponible' => true,
                'localisation' => 'Bâtiment B - Rez-de-chaussée'
            ],
            [
                'nom_salle' => 'Laboratoire Informatique',
                'capacite' => 25,
                'disponible' => true,
                'localisation' => 'Bâtiment C - 2ème étage'
            ],
            [
                'nom_salle' => 'Salle de réunion',
                'capacite' => 15,
                'disponible' => true,
                'localisation' => 'Bâtiment Principal - 3ème étage'
            ],
        ];

        foreach ($salles as $salle) {
            \App\Models\Salle::updateOrCreate(
                ['nom_salle' => $salle['nom_salle']],
                [
                    'capacite' => $salle['capacite'],
                    'disponible' => $salle['disponible'],
                    'localisation' => $salle['localisation']
                ]
            );
        }
    }
}
