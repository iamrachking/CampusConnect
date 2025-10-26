# Diagramme de la Base de Données CampusConnect

```mermaid
erDiagram
    ROLES {
        int id PK
        string name
        timestamp created_at
        timestamp updated_at
    }
    
    USERS {
        int id PK
        string name
        string nom
        string prenom
        string email
        string password
        int role_id FK
        timestamp email_verified_at
        string remember_token
        int current_team_id
        string profile_photo_path
        timestamp created_at
        timestamp updated_at
    }
    
    SALLES {
        int id PK
        string nom_salle
        int capacite
        boolean disponible
        text localisation
        timestamp created_at
        timestamp updated_at
    }
    
    MATERIELS {
        int id PK
        string nom_materiel
        boolean disponible
        timestamp created_at
        timestamp updated_at
    }
    
    RESERVATIONS {
        int id PK
        int user_id FK
        enum item_type
        int item_id
        datetime date_debut
        datetime date_fin
        enum statut
        text motif
        timestamp created_at
        timestamp updated_at
    }
    
    PROJETS {
        int id PK
        string titre
        text description
        int encadrant FK
        timestamp created_at
        timestamp updated_at
    }
    
    EQUIPES {
        int id PK
        int projet_id FK
        int user_id FK
        string role_membre
        timestamp created_at
        timestamp updated_at
    }
    
    LIVRABLES {
        int id PK
        int projet_id FK
        int user_id FK
        string nom_livrable
        string url_livrable
        string type_livrable
        timestamp created_at
        timestamp updated_at
    }
    
    ROLES ||--o{ USERS : "a"
    USERS ||--o{ RESERVATIONS : "fait"
    USERS ||--o{ PROJETS : "encadre"
    USERS ||--o{ EQUIPES : "participe"
    USERS ||--o{ LIVRABLES : "dépose"
    PROJETS ||--o{ EQUIPES : "contient"
    PROJETS ||--o{ LIVRABLES : "a"
    SALLES ||--o{ RESERVATIONS : "réservée"
    MATERIELS ||--o{ RESERVATIONS : "réservé"
```

## Légende des Relations

- **ROLES → USERS** : Un rôle peut avoir plusieurs utilisateurs
- **USERS → RESERVATIONS** : Un utilisateur peut faire plusieurs réservations
- **USERS → PROJETS** : Un utilisateur peut encadrer plusieurs projets
- **USERS → EQUIPES** : Un utilisateur peut participer à plusieurs équipes
- **USERS → LIVRABLES** : Un utilisateur peut déposer plusieurs livrables
- **PROJETS → EQUIPES** : Un projet peut avoir plusieurs équipes
- **PROJETS → LIVRABLES** : Un projet peut avoir plusieurs livrables
- **SALLES → RESERVATIONS** : Une salle peut être réservée plusieurs fois
- **MATERIELS → RESERVATIONS** : Un matériel peut être réservé plusieurs fois

## Types de Relations

1. **One-to-Many** : La plupart des relations sont de type 1:N
2. **Polymorphe** : Les réservations peuvent concerner soit des salles soit des matériels
3. **Many-to-Many** : Les utilisateurs et projets sont liés via la table équipes
