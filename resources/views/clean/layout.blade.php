<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CampusConnect</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @keyframes gradientShift { 0% { background-position: 0% 50%; } 50% { background-position: 100% 50%; } 100% { background-position: 0% 50%; } }
        .animated-bg { background: linear-gradient(135deg, #7c3aed, #2563eb, #06b6d4, #22c55e); background-size: 300% 300%; animation: gradientShift 12s ease infinite; }
    </style>
</head>
<body class="animated-bg min-h-screen">
    <header class="bg-white/80 backdrop-blur border-b border-white/40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center justify-center h-8 w-8 rounded-lg bg-indigo-600 text-white">CC</span>
                <span class="text-lg font-semibold text-gray-900">CampusConnect</span>
            </div>
            <nav class="flex items-center gap-4 text-sm">
                @php($roleName = optional(Auth::user()->role)->name)
                @if($roleName === 'Administrateur')
                    <a href="{{ route('clean.admin.reservations') }}" class="text-gray-700 hover:text-indigo-600">Réservations</a>
                @elseif($roleName === 'Enseignant')
                    <a href="{{ route('clean.teacher.index') }}" class="text-gray-700 hover:text-indigo-600">Mes réservations</a>
                    <a href="{{ route('clean.teacher.create') }}" class="text-gray-700 hover:text-indigo-600">Nouvelle demande</a>
                @elseif($roleName === 'Étudiant')
                    <a href="{{ route('clean.student.availability') }}" class="text-gray-700 hover:text-indigo-600">Disponibilité</a>
                    <a href="{{ route('clean.student.create') }}" class="text-gray-700 hover:text-indigo-600">Demande de réservation</a>
                @endif

                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button class="px-3 py-1.5 rounded bg-gray-900 text-white hover:bg-gray-800">Se déconnecter</button>
                </form>
            </nav>
        </div>
    </header>

    <main class="min-h-[calc(100vh-64px)]">
        <div class="max-w-7xl mx-auto p-6">
            {{ $slot }}
        </div>
    </main>
</body>
</html>