<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MaterielSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $materiels = [
            ['nom_materiel' => 'Vidéoprojecteur portable', 'disponible' => true],
            ['nom_materiel' => 'Tableau blanc interactif', 'disponible' => true],
            ['nom_materiel' => 'Ordinateur portable', 'disponible' => true],
            ['nom_materiel' => 'Microphone sans fil', 'disponible' => true],
            ['nom_materiel' => 'Caméra de visioconférence', 'disponible' => true],
            ['nom_materiel' => 'Tablette graphique', 'disponible' => true],
            ['nom_materiel' => 'Enceintes portables', 'disponible' => true],
            ['nom_materiel' => 'Écran de projection', 'disponible' => true],
        ];

        foreach ($materiels as $materiel) {
            \App\Models\Materiel::create($materiel);
        }
    }
}
