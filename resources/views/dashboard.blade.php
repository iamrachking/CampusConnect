<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Messages de succès/erreur -->
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            @if(Auth::user()->role->name === 'Administrateur')
                <!-- DASHBOARD ADMINISTRATEUR -->
                <div class="space-y-6">
                    <!-- Cartes de statistiques -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <!-- Carte Étudiants -->
                        <div class="bg-white rounded-sm shadow-sm p-8 border-l-4" style="">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600 mb-1">Étudiants</p>
                                    <p class="text-3xl font-bold" style="color: #19a6ec;">{{ $stats['total_etudiants'] }}</p>
                                </div>
                                <div class="w-24 h-20  rounded-sm flex items-center justify-center" style="background-color: #E8F5FE;">
                                    <img src="{{ asset('images/dash-icon-01.svg') }}" alt="Étudiant" class="w-16 h-16">
                                </div>
                            </div>
                        </div>

                        <!-- Carte Professeurs -->
                        <div class="bg-white rounded-sm shadow-sm p-8 border-l-4" style="">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600 mb-1">Professeurs</p>
                                    <p class="text-3xl font-bold" style="color: #3d5ee1;">{{ $stats['total_enseignants'] }}</p>
                                </div>
                                <div class="w-24 h-20  rounded-sm flex items-center justify-center" style="background-color: #E8EDFF;">
                                    <img src="{{ asset('images/teacher.svg') }}" alt="Professeur" class="w-16 h-16">
                                </div>
                            </div>
                        </div>

                        <!-- Carte Cours/Projets -->
                        <div class="bg-white rounded-sm shadow-sm p-8 border-l-4" style="">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600 mb-1">Projets</p>
                                    <p class="text-3xl font-bold" style="color: #19a6ec;">{{ $stats['projets_en_cours'] }}</p>
                                </div>
                                <div class="w-24 h-20  rounded-sm flex items-center justify-center" style="background-color: #E8F5FE;">
                                    <img src="{{ asset('images/books.svg') }}" alt="Projets" class="w-16 h-16">
                                    
                                </div>
                            </div>
                        </div>

                        <!-- Carte Salles -->
                        <div class="bg-white rounded-sm shadow-sm p-8 border-l-4" style="">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600 mb-1">Salles</p>
                                    <p class="text-3xl font-bold" style="color: #3d5ee1;">{{ $stats['total_salles'] }}</p>
                                </div>
                                <div class="w-24 h-20  rounded-sm flex items-center justify-center" style="background-color: #E8EDFF;">
                                    <img src="{{ asset('images/dash-icon-03.svg') }}" alt="Salles" class="w-16 h-16">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Graphique et Actions -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Graphique Statistiques -->
                        <div class="lg:col-span-2 bg-white rounded-lg shadow-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistiques</h3>
                            <div style="height: 300px; position: relative;">
                                <canvas id="admin-chart"></canvas>
                            </div>
                        </div>

                        <!-- Actions rapides -->
                        <div class="bg-white rounded-lg shadow-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions Rapides</h3>
                            <div class="space-y-3">
                                <a href="{{ route('reservations.index') }}" class="block w-full text-white font-bold py-3 px-4 rounded text-center fd-bg-secondary hover:opacity-90 transition">
                                    Voir réservations
                                </a>
                                <a href="{{ route('salles.index') }}" class="block w-full text-white font-bold py-3 px-4 rounded text-center fd-bg hover:opacity-90 transition">
                                    Gérer salles
                                </a>
                                <a href="{{ route('materiels.index') }}" class="block w-full text-white font-bold py-3 px-4 rounded text-center fd-bg-secondary hover:opacity-90 transition">
                                    Gérer matériel
                                </a>
                                <a href="{{ route('projets.index') }}" class="block w-full text-white font-bold py-3 px-4 rounded text-center fd-bg hover:opacity-90 transition">
                                    Voir projets
                                </a>
                                <a href="{{ route('equipes.index') }}" class="block w-full text-white font-bold py-3 px-4 rounded text-center fd-bg-secondary hover:opacity-90 transition">
                                    Gérer les équipes
                                </a>
                                <a href="{{ route('users.index') }}" class="block w-full text-white font-bold py-3 px-4 rounded text-center fd-bg hover:opacity-90 transition">
                                    Gérer utilisateurs
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Réservations en attente -->
                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Réservations en attente ({{ $stats['reservations_en_attente'] }})</h3>
                        <div class="overflow-x-auto">
                            @if($pendingReservations->count() > 0)
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Demandeur</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Élément</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date début</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date fin</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($pendingReservations as $reservation)
                                            <tr>
                                                <td class="px-4 py-3 text-sm text-gray-900">{{ $reservation->user->getFullNameAttribute() }}</td>
                                                <td class="px-4 py-3 text-sm text-gray-900">
                                                    {{ $reservation->item_type === 'App\\Models\\Salle' ? $reservation->item->nom_salle : $reservation->item->nom_materiel }}
                                                </td>
                                                <td class="px-4 py-3 text-sm text-gray-900">{{ $reservation->date_debut->format('d/m/Y H:i') }}</td>
                                                <td class="px-4 py-3 text-sm text-gray-900">{{ $reservation->date_fin->format('d/m/Y H:i') }}</td>
                                                <td class="px-4 py-3 text-sm">
                                                    <a href="{{ route('reservations.show', $reservation) }}" class="text-blue-600 hover:text-blue-900">Voir</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p class="text-gray-500 text-center py-4">Aucune réservation en attente</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Script pour le graphique admin -->
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const ctx = document.getElementById('admin-chart');
                        if (ctx) {
                            new Chart(ctx, {
                                type: 'bar',
                                data: {
                                    labels: ['Étudiants', 'Professeurs', 'Projets', 'Salles', 'Matériels'],
                                    datasets: [{
                                        label: 'Nombre total',
                                        data: [
                                            {{ $stats['total_etudiants'] }},
                                            {{ $stats['total_enseignants'] }},
                                            {{ $stats['projets_en_cours'] }},
                                            {{ $stats['total_salles'] }},
                                            {{ $stats['total_materiels'] }}
                                        ],
                                        backgroundColor: '#19a6ec',
                                        borderColor: '#3d5ee1',
                                        borderWidth: 1
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: true,
                                    aspectRatio: 2.5,
                                    plugins: {
                                        legend: {
                                            display: true,
                                            position: 'top'
                                        }
                                    },
                                    scales: {
                                        y: {
                                            beginAtZero: true,
                                            ticks: {
                                                precision: 0
                                            }
                                        }
                                    }
                                }
                            });
                        }
                    });
                </script>

            @elseif(Auth::user()->role->name === 'Enseignant')
                <!-- DASHBOARD ENSEIGNANT -->
                <div class="space-y-6">
                    <!-- Cartes de statistiques -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
                        <div class="bg-white rounded-lg shadow-lg p-6 border-l-4" style="border-left-color: #3d5ee1;">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600 mb-1">Heures cette semaine</p>
                                    <p class="text-3xl font-bold" style="color: #3d5ee1;">{{ $stats['heures_semaine'] ?? 0 }}h</p>
                                </div>
                                <div class="w-16 h-16 rounded-full flex items-center justify-center" style="background-color: #E8EDFF;">
                                    <svg class="w-10 h-10" style="color: #3d5ee1;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg shadow-lg p-6 border-l-4" style="border-left-color: #19a6ec;">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600 mb-1">Mes réservations</p>
                                    <p class="text-3xl font-bold" style="color: #19a6ec;">{{ $stats['mes_reservations'] }}</p>
                                </div>
                                <div class="w-16 h-16 rounded-full flex items-center justify-center" style="background-color: #E8F5FE;">
                                    <svg class="w-10 h-10" style="color: #19a6ec;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg shadow-lg p-6 border-l-4" style="border-left-color: #3d5ee1;">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600 mb-1">Projets encadrés</p>
                                    <p class="text-3xl font-bold" style="color: #3d5ee1;">{{ $stats['projets_encadres'] }}</p>
                                </div>
                                <div class="w-16 h-16 rounded-full flex items-center justify-center" style="background-color: #E8EDFF;">
                                    <svg class="w-10 h-10" style="color: #3d5ee1;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg shadow-lg p-6 border-l-4" style="border-left-color: #19a6ec;">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600 mb-1">Réservations actives</p>
                                    <p class="text-3xl font-bold" style="color: #19a6ec;">{{ $stats['reservations_actives'] }}</p>
                                </div>
                                <div class="w-16 h-16 rounded-full flex items-center justify-center" style="background-color: #E8F5FE;">
                                    <svg class="w-10 h-10" style="color: #19a6ec;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg shadow-lg p-6 border-l-4" style="border-left-color: #3d5ee1;">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600 mb-1">Salles disponibles</p>
                                    <p class="text-3xl font-bold" style="color: #3d5ee1;">{{ $stats['salles_disponibles'] }}</p>
                                </div>
                                <div class="w-16 h-16 rounded-full flex items-center justify-center" style="background-color: #E8EDFF;">
                                    <svg class="w-10 h-10" style="color: #3d5ee1;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions rapides -->
                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions Rapides</h3>
                        <div class="space-y-3">
                            <a href="{{ route('reservations.index') }}" class="block w-full text-white font-bold py-3 px-4 rounded text-center fd-bg-secondary hover:opacity-90 transition">
                                Mes réservations
                            </a>
                            <a href="{{ route('reservations.create') }}" class="block w-full text-white font-bold py-3 px-4 rounded text-center fd-bg hover:opacity-90 transition">
                                Nouvelle réservation
                            </a>
                            <a href="{{ route('salles.index') }}" class="block w-full text-white font-bold py-3 px-4 rounded text-center fd-bg-secondary hover:opacity-90 transition">
                                Voir les salles
                            </a>
                            <a href="{{ route('materiels.index') }}" class="block w-full text-white font-bold py-3 px-4 rounded text-center fd-bg hover:opacity-90 transition">
                                Voir le matériel
                            </a>
                            <a href="{{ route('projets.index') }}" class="block w-full text-white font-bold py-3 px-4 rounded text-center fd-bg-secondary hover:opacity-90 transition">
                                Projets encadrés
                            </a>
                        </div>
                    </div>
                </div>

            @else
                <!-- DASHBOARD ÉTUDIANT -->
                <div class="space-y-6">
                    <!-- Cartes de statistiques -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div class="bg-white rounded-lg shadow-lg p-6 border-l-4" style="border-left-color: #3d5ee1;">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600 mb-1">Mes projets</p>
                                    <p class="text-3xl font-bold" style="color: #3d5ee1;">{{ $stats['mes_projets'] }}</p>
                                </div>
                                <div class="w-16 h-16 rounded-full flex items-center justify-center" style="background-color: #E8EDFF;">
                                    <svg class="w-10 h-10" style="color: #3d5ee1;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg shadow-lg p-6 border-l-4" style="border-left-color: #19a6ec;">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600 mb-1">Réservations actives</p>
                                    <p class="text-3xl font-bold" style="color: #19a6ec;">{{ $stats['reservations_actives'] }}</p>
                                </div>
                                <div class="w-16 h-16 rounded-full flex items-center justify-center" style="background-color: #E8F5FE;">
                                    <svg class="w-10 h-10" style="color: #19a6ec;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg shadow-lg p-6 border-l-4" style="border-left-color: #3d5ee1;">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600 mb-1">Salles disponibles</p>
                                    <p class="text-3xl font-bold" style="color: #3d5ee1;">{{ $stats['salles_disponibles'] }}</p>
                                </div>
                                <div class="w-16 h-16 rounded-full flex items-center justify-center" style="background-color: #E8EDFF;">
                                    <svg class="w-10 h-10" style="color: #3d5ee1;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions rapides -->
                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions Rapides</h3>
                        <div class="space-y-3">
                            <a href="{{ route('reservations.index') }}" class="block w-full text-white font-bold py-3 px-4 rounded text-center fd-bg-secondary hover:opacity-90 transition">
                                Consulter les réservations
                            </a>
                            <a href="{{ route('salles.index') }}" class="block w-full text-white font-bold py-3 px-4 rounded text-center fd-bg hover:opacity-90 transition">
                                Consulter les salles
                            </a>
                            <a href="{{ route('materiels.index') }}" class="block w-full text-white font-bold py-3 px-4 rounded text-center fd-bg-secondary hover:opacity-90 transition">
                                Consulter le matériel
                            </a>
                            <a href="{{ route('projets.index') }}" class="block w-full text-white font-bold py-3 px-4 rounded text-center fd-bg hover:opacity-90 transition">
                                Mes projets
                            </a>
                            <a href="{{ route('projets.create') }}" class="block w-full text-white font-bold py-3 px-4 rounded text-center fd-bg-secondary hover:opacity-90 transition">
                                Créer un projet
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Section activité récente (pour tous) -->
            <div class="mt-8 bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Activité Récente</h3>
                    @if($recentActivities->count() > 0)
                        <div class="space-y-3">
                            @foreach($recentActivities as $activity)
                                <div class="flex items-start space-x-3 p-3 bg-gray-50 rounded-lg">
                                    <div class="text-lg">{{ $activity['icon'] }}</div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900">{{ $activity['title'] }}</p>
                                        <p class="text-sm text-gray-600">{{ $activity['description'] }}</p>
                                        <p class="text-xs text-gray-500 mt-1">{{ $activity['date']->format('d/m/Y à H:i') }}</p>
                                    </div>
                                    @if($activity['type'] === 'reservation')
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                            {{ $activity['status'] === 'approved' ? 'bg-green-100 text-green-800' : 
                                               ($activity['status'] === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                            {{ ucfirst($activity['status']) }}
                                        </span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-gray-500 text-center py-8">
                            <p>Aucune activité récente</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>