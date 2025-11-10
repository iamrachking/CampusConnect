<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CampusConnect — Bienvenue</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .animated-bg {
            background: linear-gradient(135deg, #7c3aed, #2563eb, #06b6d4, #22c55e);
            background-size: 300% 300%;
            animation: gradientShift 12s ease infinite;
        }
        .soft-in {
            animation: softIn .9s ease both;
        }
        @keyframes softIn {
            from { opacity: 0; transform: translateY(12px) scale(.98); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
    </style>
</head>
<body class="animated-bg min-h-screen flex items-center justify-center">
    <div class="relative p-8 sm:p-10 w-[92%] sm:w-[560px] rounded-2xl bg-white/80 backdrop-blur shadow-2xl soft-in border border-white/40">
        <div class="flex items-center gap-3 mb-6">
            <svg class="h-8 w-8 text-indigo-600" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 2a1 1 0 0 1 1 1v7h7a1 1 0 1 1 0 2h-7v7a1 1 0 1 1-2 0v-7H4a1 1 0 1 1 0-2h7V3a1 1 0 0 1 1-1Z"/>
            </svg>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">CampusConnect</h1>
        </div>

        <p class="text-gray-600 mb-8">Plateforme moderne de gestion des réservations du campus. Connectez-vous pour accéder à votre espace dédié (Étudiant, Enseignant ou Administrateur).</p>

        @auth
            <a href="{{ url('/dashboard') }}" class="inline-flex items-center justify-center w-full sm:w-auto px-5 py-3 rounded-lg bg-indigo-600 text-white font-semibold shadow-lg hover:bg-indigo-500 hover:scale-[1.02] hover:shadow-xl active:scale-100 transition-all">
                Accéder à mon espace
                <svg class="ms-2 h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
            </a>
        @else
            <a href="{{ route('login') }}" class="group inline-flex items-center justify-center w-full sm:w-auto px-5 py-3 rounded-lg bg-indigo-600 text-white font-semibold shadow-lg hover:bg-indigo-500 hover:scale-[1.02] hover:shadow-xl active:scale-100 transition-all">
                Se connecter
                <span class="inline-flex items-center justify-center ms-2 h-5 w-5 rounded-full bg-white/20 group-hover:bg-white/30 transition-colors">
                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                </span>
            </a>
            <p class="mt-4 text-xs text-gray-500">L’inscription est désactivée — utilisez un compte existant.</p>
        @endauth

        <div class="mt-8 grid grid-cols-1 sm:grid-cols-3 gap-3 text-sm">
            <div class="p-3 rounded-lg bg-gray-50 border border-gray-200/60">Étudiants: disponibilité et demandes</div>
            <div class="p-3 rounded-lg bg-gray-50 border border-gray-200/60">Enseignants: gérer vos réservations</div>
            <div class="p-3 rounded-lg bg-gray-50 border border-gray-200/60">Admin: salles, matériels, demandes</div>
        </div>
    </div>
</body>
</html>