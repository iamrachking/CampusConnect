# Outil local: resumé_commandes

Ce fichier récapitule les commandes pour:
- Démarrer rapidement le backend (Laravel) et le frontend (Vite)
- Préparer l’environnement local si nécessaire
- Pousser le projet sur la branche `Joseph_branch` avec les bons filtres

## Démarrer les serveurs
- Backend (Laravel):
  - `cd "d:\Personnal Folder\projets_persos\class project\CampusConnect"`
  - `.\tools\php83\php.exe .\artisan serve --ansi`
  - URL: `http://127.0.0.1:8000/`
- Frontend (Vite):
  - `cmd /c npm run dev`
  - URL: `http://localhost:5173/`

## Préparer l’environnement local (si première ouverture ou après un pull)
- Installer dépendances PHP:
  - `.\tools\php83\php.exe .\.tools\composer.phar install`
- Générer la clé d’application:
  - `.\tools\php83\php.exe .\artisan key:generate --ansi`
- Migrations + seeders (base SQLite):
  - `.\tools\php83\php.exe .\artisan migrate --seed --ansi`

## Pousser sur GitHub (branche Joseph_branch)
- Basculer/Créer la branche:
  - `git checkout Joseph_branch`  (ou `git checkout -b Joseph_branch` si elle n’existe pas)
- (Optionnel) Définir l’auteur local pour ce repo:
  - `git config user.name "Joseph"`
  - `git config user.email "joseph@example.com"`
- Définir la remote si nécessaire:
  - `git remote add origin https://github.com/<votre_compte>/<votre_repo>.git`
- Vérifier que les outils locaux sont ignorés (déjà configuré):
  - `.tools/`
  - `tools/php83/`
- Commit + push:
  - `git add .`
  - `git commit -m "Update: travail en cours / corrections / features"`
  - `git push -u origin Joseph_branch`

## Raccourcis utiles
- Nettoyer la config Laravel:
  - `.\tools\php83\php.exe .\artisan config:clear`
- Vérifier version PHP portable:
  - `.\tools\php83\php.exe -v`
- Vérifier les extensions chargées (HTTPS, SQLite, chaînes):
  - `.\tools\php83\php.exe -m | findstr /i "openssl curl pdo_sqlite sqlite3 mbstring"`

## Notes
 - Base de données: `.env` utilise `DB_CONNECTION=sqlite` et `DB_DATABASE=database/database.sqlite`.
 - Le warning `ctype` au démarrage est bénin pour le dev local; migrations/serveur fonctionnent.
 - Pour exposer Vite sur le réseau local: `npm run dev -- --host`.

## Accès à l’interface et URL

- URL locale de l’application: `http://127.0.0.1:8000/`
- Page de connexion: `http://127.0.0.1:8000/login`

### Administration (Administrateur)
- Salles: `http://127.0.0.1:8000/admin/salles`
- Matériels: `http://127.0.0.1:8000/admin/materiels`
- Réservations en attente: `http://127.0.0.1:8000/admin/reservations`

#### Connexion Administrateur (identifiants de test)
- Email: `admin@example.com`
- Mot de passe: `password`
- Prérequis: exécuter les migrations et seeders (`php artisan migrate --force && php artisan db:seed --force`). Ces comptes sont créés par `database/seeders/DatabaseSeeder.php` via la factory (mot de passe par défaut: `password`).

### Enseignant
- Mes Réservations: `http://127.0.0.1:8000/reservations`
- Nouvelle réservation: `http://127.0.0.1:8000/reservations/create`

#### Connexion Enseignant (identifiants de test)
- Email: `teacher@example.com`
- Mot de passe: `password`

### Étudiant
- Disponibilité des salles: `http://127.0.0.1:8000/availability`

## Assets frontend (Vite)

- En développement, démarrer Vite: `npm run dev` (dans le dossier `CampusConnect`)
- En production ou sans Vite en mode dev, construire les assets: `npm run build` (génère `public/build/manifest.json`)
- Si l’erreur « Vite manifest not found » apparaît, lancez `npm run dev` ou `npm run build`.

## Base de données

- Config par défaut `.env`: MySQL (`campusconnect`) ou SQLite selon votre setup.
- Pour MySQL: réglez `DB_CONNECTION=mysql` et adaptez `DB_*`, puis `php artisan migrate --force && php artisan db:seed --force`.
- Pour SQLite: assurez `database\database.sqlite` existe, et `php artisan migrate --seed --force`.