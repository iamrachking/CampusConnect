<x-mail::message>
# Bienvenue sur CampusConnect

Bonjour {{ $user->name }},

Un compte a été créé pour vous sur CampusConnect avec les identifiants suivants :

## Vos identifiants de connexion

**Email :** {{ $user->email }}  
**Mot de passe temporaire :** `{{ $temporaryPassword }}`

**Rôle :** {{ $user->role->name }}

## Première connexion

Pour vous connecter :
1. Allez sur la page de connexion
2. Utilisez votre email et le mot de passe temporaire ci-dessus
3. Vous serez invité à changer votre mot de passe à la première connexion

## Important

⚠️ **Le mot de passe ci-dessus est temporaire. Nous vous recommandons fortement de le changer dès votre première connexion pour des raisons de sécurité.**

<x-mail::button :url="route('login')">
Se connecter
</x-mail::button>

## Besoin d'aide ?

Si vous rencontrez des difficultés, n'hésitez pas à contacter l'administration.

Cordialement,<br>
{{ config('app.name') }}
</x-mail::message>
