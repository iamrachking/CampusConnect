<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tableau de Bord - CampusConnect') }}
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

            <!-- Informations utilisateur -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
                <div class="p-6 bg-gradient-to-r from-blue-500 to-purple-600 text-white">
                    <h3 class="text-lg font-semibold mb-2">Bienvenue, {{ Auth::user()->getFullNameAttribute() }} !</h3>
                    <p class="text-blue-100">Rôle : {{ Auth::user()->role->name }}</p>
                </div>
            </div>

            <!-- Cartes d'actions selon le rôle -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                
                @if(Auth::user()->role->name === 'Administrateur')
                    <!-- Actions Administrateur -->
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Gestion des Réservations</h3>
                            <div class="space-y-3">
                                <a href="{{ route('reservations.index') }}" class="block w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded text-center">
                                    Voir toutes les réservations
                                </a>
                                <a href="{{ route('salles.index') }}" class="block w-full bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded text-center">
                                    Gérer les salles
                                </a>
                                <a href="{{ route('materiels.index') }}" class="block w-full bg-purple-500 hover:bg-purple-600 text-white font-bold py-2 px-4 rounded text-center">
                                    Gérer le matériel
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Gestion des Projets</h3>
                            <div class="space-y-3">
                                <a href="{{ route('projets.index') }}" class="block w-full bg-indigo-500 hover:bg-indigo-600 text-white font-bold py-2 px-4 rounded text-center">
                                    Voir tous les projets
                                </a>
                                <a href="{{ route('equipes.index') }}" class="block w-full bg-pink-500 hover:bg-pink-600 text-white font-bold py-2 px-4 rounded text-center">
                                    Gérer les équipes
                                </a>
                                <a href="{{ route('users.index') }}" class="block w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-4 rounded text-center">
                                    Gérer les utilisateurs
                                </a>
                            </div>
                        </div>
                    </div>

                @elseif(Auth::user()->role->name === 'Enseignant')
                    <!-- Actions Enseignant -->
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Mes Réservations</h3>
                            <div class="space-y-3">
                                <a href="{{ route('reservations.index') }}" class="block w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded text-center">
                                    Mes réservations
                                </a>
                                <a href="{{ route('reservations.create') }}" class="block w-full bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded text-center">
                                    Nouvelle réservation
                                </a>
                                <a href="{{ route('salles.index') }}" class="block w-full bg-purple-500 hover:bg-purple-600 text-white font-bold py-2 px-4 rounded text-center">
                                    Voir les salles
                                </a>
                                <a href="{{ route('materiels.index') }}" class="block w-full bg-purple-500 hover:bg-purple-600 text-white font-bold py-2 px-4 rounded text-center">
                                    Voir le matériel
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Mes Projets</h3>
                            <div class="space-y-3">
                                <a href="{{ route('projets.index') }}" class="block w-full bg-indigo-500 hover:bg-indigo-600 text-white font-bold py-2 px-4 rounded text-center">
                                    Projets encadrés
                                </a>
                            </div>
                        </div>
                    </div>

                @elseif(Auth::user()->role->name === 'Étudiant')
                    <!-- Actions Étudiant -->
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Disponibilité</h3>
                            <div class="space-y-3">
                                <a href="{{ route('salles.index') }}" class="block w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded text-center">
                                    Consulter les salles
                                </a>
                                <a href="{{ route('materiels.index') }}" class="block w-full bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded text-center">
                                    Consulter le matériel
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Mes Projets</h3>
                            <div class="space-y-3">
                                <a href="{{ route('projets.index') }}" class="block w-full bg-indigo-500 hover:bg-indigo-600 text-white font-bold py-2 px-4 rounded text-center">
                                    Mes projets
                                </a>
                                <a href="{{ route('projets.create') }}" class="block w-full bg-purple-500 hover:bg-purple-600 text-white font-bold py-2 px-4 rounded text-center">
                                    Créer un projet
                                </a>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Statistiques rapides -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistiques</h3>
                        <div class="space-y-2 text-sm text-gray-600">
                            <p>📅 Réservations actives : <span class="font-semibold text-green-600">{{ $stats['reservations_actives'] }}</span></p>
                            <p>🏢 Salles disponibles : <span class="font-semibold text-blue-600">{{ $stats['salles_disponibles'] }}</span></p>
                            <p>📦 Matériel disponible : <span class="font-semibold text-purple-600">{{ $stats['materiel_disponible'] }}</span></p>
                            <p>📚 Projets en cours : <span class="font-semibold text-indigo-600">{{ $stats['projets_en_cours'] }}</span></p>
                            
                            @if(Auth::user()->role->name === 'Administrateur')
                                <hr class="my-3">
                                <p class="text-xs text-gray-500">Statistiques détaillées :</p>
                                <p>📊 Total réservations : <span class="font-semibold">{{ $stats['total_reservations'] }}</span></p>
                                <p>⏳ En attente : <span class="font-semibold text-yellow-600">{{ $stats['reservations_en_attente'] }}</span></p>
                                <p>👥 Utilisateurs : <span class="font-semibold">{{ $stats['total_utilisateurs'] }}</span></p>
                            @elseif(Auth::user()->role->name === 'Enseignant')
                                <hr class="my-3">
                                <p class="text-xs text-gray-500">Mes statistiques :</p>
                                <p>📋 Mes réservations : <span class="font-semibold">{{ $stats['mes_reservations'] }}</span></p>
                                <p>🎓 Projets encadrés : <span class="font-semibold">{{ $stats['projets_encadres'] }}</span></p>
                            @elseif(Auth::user()->role->name === 'Étudiant')
                                <hr class="my-3">
                                <p class="text-xs text-gray-500">Mes statistiques :</p>
                                <p>🎯 Mes projets : <span class="font-semibold">{{ $stats['mes_projets'] }}</span></p>
                            @endif
                        </div>
                    </div>
                </div>

            </div>

            <!-- Section récente activité -->
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
