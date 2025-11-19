# CampusConnect 🎓

## Description du Projet
CampusConnect est un portail interne universitaire conçu pour faciliter les échanges entre les étudiants, enseignants et services administratifs. 

## 🚀 Démarrage Rapide

### Prérequis
- PHP 8.3+
- Composer
- Node.js 18+
- MySQL 

### Installation

1. **Cloner le projet**
```bash
git clone https://github.com/iamrachking/CampusConnect.git

cd CampusConnect
```

2. **Installation**
```bash
# Dépendances PHP
composer install

# Dépendances Node.js
npm install

# Configuration
cp .env.example .env
php artisan key:generate

# NB:Base de données (créer la DB 'campusconnect' dans phpMyAdmin)
php artisan migrate

php artisan db:seed

# Vous pouvez faire directement la commande suivante pour lancer les migrations et les seeders en un click
php artisan migrate --seed
```

4. **Démarrer le serveur de développement**
```bash
# Commande unique qui lance tout
composer run dev

# Ou manuellement
php artisan serve
npm run dev
```
### Compte admin de test par defaut

```
email : test@example.com
mot de passe : password
```

## 📁 Structure du Projet

```
CampusConnect/
├── app/
│   ├── Http/Controllers/     # Contrôleurs
│   ├── Models/              # Modèles Eloquent
│   ├── Services/            # Logique métier
│   └── Traits/              # Traits réutilisables
├── database/
│   ├── migrations/          # Migrations de base de données
│   ├── seeders/            # Seeders pour les données de test
│   └── factories/          # Factories pour les tests
├── resources/
│   ├── views/              # Vues Blade
│   ├── css/               # Styles CSS
│   └── js/                # JavaScript
└── routes/
    ├── web.php            # Routes web
    └── api.php            # Routes API
```

## 🛠️ Technologies Utilisées

- **Backend**: Laravel 12
- **Frontend**: Blade
- **Base de données**: MySQL

## 👥 Équipe de Développement

- **Développeurs**: 
  - AKOUTEY Joseph Godbless
  - LAWINGNI Abdoul Rachard
  - ODJO Segnon Ariand
  - SOULEYMANE Hosny


## 📝 Workflow Git

### Branches
- **`main`** : Branche principale (PERSONNE n'écrit directement dessus)
- **`prenom`** : Chaque membre crée sa propre branche avec son prénom

### 🚨 Règles Importantes

- ✅ **Toujours créer une branche en son nom au début**
- ✅ **Vérifier qu'on est bien sur sa propre branche avant de commiter**
- ✅ **Tester avant de pousser et assurer qu'il n'y a pas d'erreur**
- ✅ **Messages de commit clairs**
- ❌ **Ne jamais commiter directement sur main**
- ❌ **Ne jamais commiter sur la branche d'autrui**

## 📞 Support

Pour toute question, contactez un membre du groupe .