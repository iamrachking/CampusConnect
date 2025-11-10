# Résumé complet du projet et commandes (Windows)

Ce document présente CampusConnect, ses fonctionnalités clés, sa structure, les endpoints utiles et fournit un guide de démarrage avec un résumé des commandes pour lancer et bien faire tourner le projet.

## Aperçu du projet
- Application Laravel pour la gestion des réservations de salles et de matériels.
- Rôles: Administrateur, Enseignant, Étudiant.
- Authentification: Jetstream/Fortify, Sanctum (API et session web).
- Frontend: Vite + Blade, assets dans `public/` et `resources/`.
- Disponibilité côté Étudiant: mise à jour auto 5s et bouton “Actualiser”.
- Admin: interface épurée (pas d’affichage de disponibilité), actions d’approbation/rejet.

## Fonctionnalités principales
- Réservation: création, approbation (admin), rejet, affichage des demandes.
- Items: Salles et Matériels, listés pour Student/Teacher, visibles en Admin.
- Disponibilité en temps réel: calcul serveur via `CURRENT_TIMESTAMP` pour éviter les décalages de fuseau.
- Dropdowns Student/Teacher: affichent tous les éléments, ajoutent “(Occupé)” aux indisponibles.

## Structure de répertoires
```
CampusConnect/
├── app/Http/Controllers/           # Contrôleurs web et API
├── app/Models/                      # Modèles (Reservation, Salle, Materiel)
├── config/                          # Config Laravel (sanctum, app, etc.)
├── database/                        # Migrations, seeders
├── public/                          # app.js, app.css, front controller
├── resources/views/                 # Vues Blade (admin, student, spa)
├── routes/                          # web.php, api.php
├── tools/php83/                     # PHP portable (Windows)
└── vite.config.js                   # Config Vite
```

## Endpoints utiles (API)
- Auth:
  - `POST /api/login` → renvoie `{ token: <plainTextToken> }` (Sanctum)
  - `GET /api/user` (protégé `auth:sanctum`)
- Étudiant:
  - `GET /api/student/salles` → objets avec `disponible: true|false`
  - `GET /api/student/materiels` → objets avec `disponible: true|false`
- Items (général):
  - `GET /api/items/salles` → disponibilité calculée côté serveur
  - `GET /api/items/materiels` → disponibilité calculée côté serveur
- Admin:
  - `GET /api/admin/items/salles` | `GET /api/admin/items/materiels`
  - `POST /api/admin/items/salles` | `DELETE /api/admin/items/salles/{id}`
  - `POST /api/admin/items/materiels` | `DELETE /api/admin/items/materiels/{id}`
  - Réservations: approbation/rejet via routes web (voir vues Admin)

## Interfaces et URLs
- Connexion: `http://127.0.0.1:8000/login`
- Tableau de bord: `http://127.0.0.1:8000/dashboard`
- Étudiant (SPA épurée): `http://127.0.0.1:8000/student/availability` et `.../create`
- Enseignant (SPA épurée): `http://127.0.0.1:8000/teacher` et `.../create`
- Admin: `http://127.0.0.1:8000/admin/reservations`, listes items

## Prérequis
- Windows 10/11, Git (optionnel), Node.js 18+
- Option A: PHP portable inclus `tools/php83/php.exe` + Composer PHAR `tools/composer.phar`
- Option B: PHP + Composer installés globalement

## Mise en place pas-à-pas
1) Ouvrir le dossier projet
- `cd "d:\Personnal Folder\projets_persos\class project\CampusConnect"`

2) Installer dépendances
- Node: `npm install`
- PHP portable: `./tools/php83/php.exe ./tools/composer.phar install`
- PHP global: `composer install`

3) Configurer `.env`
- Copier: `copy .env.example .env`
- Générer clé:
  - Portable: `./tools/php83/php.exe ./artisan key:generate --ansi`
  - Global: `php artisan key:generate --ansi`
- Base SQLite recommandée:
  - Créer fichier: `ni database/database.sqlite -ItemType File`
  - `.env`: `DB_CONNECTION=sqlite`, `DB_DATABASE=database/database.sqlite`

4) Migrations et seeders
- Portable: `./tools/php83/php.exe ./artisan migrate --seed --ansi`
- Global: `php artisan migrate --seed --ansi`

5) Assets (Vite)
- Dév: `cmd /c npm run dev`
- Prod: `cmd /c npm run build`

6) Démarrer serveur Laravel
- Portable: `./tools/php83/php.exe ./artisan serve --ansi`
- Global: `php artisan serve --ansi`
- URL: `http://127.0.0.1:8000/`

## Comptes de test
- Admin: `admin@example.com` / `password`
- Enseignant: `teacher@example.com` / `password`
- Étudiant: `student@example.com` / `password`

## Astuces et dépannage
- Si PowerShell bloque `npm run ...` → utilisez `cmd /c npm run dev|build`.
- Si “Vite manifest not found” → lancez `npm run dev` ou `npm run build`.
- Si DB SQLite manquante → créez `database\database.sqlite` et refaites migrate/seed.
- Sans websockets, l’auto-refresh côté Étudiant est de 5s; utilisez le bouton “Actualiser” pour forcer la mise à jour.

---

## Résumé des commandes (copier/coller)
- Dossier projet:
  - `cd "d:\Personnal Folder\projets_persos\class project\CampusConnect"`
- Installer dépendances:
  - `npm install`
  - `./tools/php83/php.exe ./tools/composer.phar install` (ou `composer install`)
- Initialiser l’app:
  - `copy .env.example .env`
  - `./tools/php83/php.exe ./artisan key:generate --ansi` (ou `php artisan key:generate --ansi`)
  - `ni database/database.sqlite -ItemType File`
  - `./tools/php83/php.exe ./artisan migrate --seed --ansi` (ou `php artisan migrate --seed --ansi`)
- Démarrer:
  - `cmd /c npm run dev` (dev) ou `cmd /c npm run build` (prod)
  - `./tools/php83/php.exe ./artisan serve --ansi` (ou `php artisan serve --ansi`)
- Tests (optionnel):
  - `php artisan test --ansi`