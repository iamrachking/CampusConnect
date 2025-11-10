<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Se connecter — CampusConnect</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @keyframes gradientShift { 0% { background-position: 0% 50%; } 50% { background-position: 100% 50%; } 100% { background-position: 0% 50%; } }
        .animated-bg { background: linear-gradient(135deg, #7c3aed, #2563eb, #06b6d4, #22c55e); background-size: 300% 300%; animation: gradientShift 12s ease infinite; }
        .soft-in { animation: softIn .9s ease both; }
        @keyframes softIn { from { opacity: 0; transform: translateY(12px) scale(.98);} to { opacity: 1; transform: translateY(0) scale(1);} }
    </style>
</head>
<body class="animated-bg min-h-screen flex items-center justify-center">
    <div class="relative p-8 sm:p-10 w-[92%] sm:w-[480px] rounded-2xl bg-white/80 backdrop-blur shadow-2xl soft-in border border-white/40">
        <div class="flex items-center gap-3 mb-6">
            <svg class="h-8 w-8 text-indigo-600" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 2a1 1 0 0 1 1 1v7h7a1 1 0 1 1 0 2h-7v7a1 1 0 1 1-2 0v-7H4a1 1 0 1 1 0-2h7V3a1 1 0 0 1 1-1Z"/>
            </svg>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Se connecter</h1>
        </div>

        @if ($errors->any())
            <div class="mb-4 rounded-md border border-red-300 bg-red-50 text-red-700 p-3">
                <ul class="list-disc ms-4">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input id="email" name="email" type="email" autocomplete="username" required autofocus value="{{ old('email') }}" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white/70 px-3 py-2 text-gray-900 shadow-sm placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Mot de passe</label>
                <input id="password" name="password" type="password" autocomplete="current-password" required class="mt-1 block w-full rounded-lg border border-gray-300 bg-white/70 px-3 py-2 text-gray-900 shadow-sm placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
            </div>

            <button type="submit" class="inline-flex items-center justify-center w-full px-5 py-3 rounded-lg bg-indigo-600 text-white font-semibold shadow-lg hover:bg-indigo-500 hover:scale-[1.02] hover:shadow-xl active:scale-100 transition-all">
                Se connecter
                <svg class="ms-2 h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
            </button>
        </form>

        <p class="mt-6 text-xs text-gray-500">Astuce: Utilise les comptes de test fournis (admin / teacher / student) ou ceux existants dans la base.</p>
    </div>
</body>
</html>
