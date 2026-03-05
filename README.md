# CampusConnect — Portail universitaire

[![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=flat-square&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat-square&logo=php&logoColor=white)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-5.7+-4479A1?style=flat-square&logo=mysql&logoColor=white)](https://mysql.com)
[![Vite](https://img.shields.io/badge/Vite-646CFF?style=flat-square&logo=vite&logoColor=white)](https://vitejs.dev)

Portail interne universitaire **CampusConnect** : réservation de salles et de matériel, gestion de projets étudiants, équipes et livrables. Interface par rôle (Administrateur, Enseignant, Étudiant).

---

## Accès de test (back-office)

Après `php artisan db:seed`, un compte administrateur est créé pour accéder au tableau de bord :

| | Valeur |
|---|--------|
| **URL connexion** | http://localhost:8000/login |
| **Email** | `test@example.com` |
| **Mot de passe** | `password` |

![Connexion](docs/images/login.png)  
*Écran de connexion au portail.*

![Dashboard admin](docs/images/admin_dash.png)  
*Tableau de bord administrateur.*

![Dashboard enseignant](docs/images/teacher_dash.png)  
*Tableau de bord enseignant.*

![Dashboard étudiant](docs/images/student_dash.png)  
*Tableau de bord étudiant.*

---

## Stack

- **Backend** : Laravel 12
- **Base de données** : MySQL
- **Auth** : Laravel Jetstream (Livewire) + Sanctum
- **Frontend** : Blade, Livewire, Tailwind CSS, Vite

---

## Fonctionnalités principales

- **Réservation** : salles et matériel (créneaux, approbation/rejet par l’admin)
- **Projets étudiants** : création, équipes, rejoindre/quitter, statistiques
- **Livrables** : dépôt et gestion par projet
- **Rôles** : Administrateur (gestion complète), Enseignant, Étudiant (tableaux de bord et actions adaptés)

---

## Installation locale

**Prérequis** : PHP >= 8.2, Composer, MySQL >= 5.7, Node.js >= 18

```bash
git clone https://github.com/iamrachking/CampusConnect.git
cd CampusConnect
composer install
npm install
cp .env.example .env
php artisan key:generate
```

Configurer la base dans `.env` (par ex. `DB_DATABASE=campusconnect`, `DB_USERNAME`, `DB_PASSWORD`), puis :

```bash
php artisan migrate
php artisan db:seed
npm run build
php artisan serve
```

- **Portail** : http://localhost:8000 (connexion avec le compte admin ci-dessus)

Pour le développement avec rechargement à chaud (serveur + queue + Vite) :

```bash
composer run dev
```

---

## Structure des rôles

| Rôle | Droits principaux |
|------|-------------------|
| **Administrateur** | Salles/matériel (CRUD), approbation réservations, gestion globale |
| **Enseignant** | Projets (encadrement), réservations, tableau de bord enseignant |
| **Étudiant** | Réservations, projets (rejoindre/équipes), livrables, tableau de bord étudiant |

