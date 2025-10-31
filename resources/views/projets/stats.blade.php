<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Statistiques détaillées') }} - {{ $projet->titre }}
            </h2>
            <a href="{{ route('projets.show', $projet) }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                Retour
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Messages -->
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

            <!-- Informations générales du projet -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Informations du projet</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="font-medium text-gray-700">Titre :</span>
                            <span class="ml-2 text-gray-900">{{ $projet->titre }}</span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700">Encadrant :</span>
                            <span class="ml-2 text-gray-900">
                                {{ $projet->encadrant ? $projet->encadrant->getFullNameAttribute() : 'Non assigné' }}
                            </span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700">Date de création :</span>
                            <span class="ml-2 text-gray-900">{{ $projet->created_at->format('d/m/Y à H:i') }}</span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700">Description :</span>
                            <span class="ml-2 text-gray-900">{{ $projet->description }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                <!-- Statistiques des membres -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">📊 Statistiques des membres</h3>
                        <div class="space-y-4">
                            <div class="bg-blue-50 p-4 rounded-lg">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-700 font-medium">Total des membres</span>
                                    <span class="text-2xl font-bold text-blue-600">{{ $totalMembres }}</span>
                                </div>
                            </div>

                            @if($membresParRole->count() > 0)
                                <div>
                                    <h4 class="text-sm font-medium text-gray-700 mb-2">Répartition par rôle :</h4>
                                    <div class="space-y-2">
                                        @foreach($membresParRole as $role => $count)
                                            <div class="flex justify-between items-center p-2 bg-gray-50 rounded">
                                                <span class="text-gray-600">{{ $role }}</span>
                                                <span class="font-semibold text-gray-900">{{ $count }} {{ $count > 1 ? 'membres' : 'membre' }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <p class="text-gray-500 text-center py-4">Aucun membre dans l'équipe</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Statistiques des livrables -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">📦 Statistiques des livrables</h3>
                        <div class="space-y-4">
                            <div class="bg-green-50 p-4 rounded-lg">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-700 font-medium">Total des livrables</span>
                                    <span class="text-2xl font-bold text-green-600">{{ $totalLivrables }}</span>
                                </div>
                            </div>

                            @if($dernierLivrable)
                                <div class="bg-yellow-50 p-4 rounded-lg">
                                    <div class="text-sm">
                                        <span class="text-gray-700 font-medium">Dernier livrable :</span>
                                        <div class="mt-1">
                                            <p class="font-medium text-gray-900">{{ $dernierLivrable->nom_livrable }}</p>
                                            <p class="text-gray-600">Déposé par {{ $dernierLivrable->user->getFullNameAttribute() }}</p>
                                            <p class="text-gray-500 text-xs">{{ $dernierLivrable->created_at->format('d/m/Y à H:i') }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if($livrablesParType->count() > 0)
                                <div>
                                    <h4 class="text-sm font-medium text-gray-700 mb-2">Répartition par type :</h4>
                                    <div class="space-y-2">
                                        @foreach($livrablesParType as $type => $count)
                                            <div class="flex justify-between items-center p-2 bg-gray-50 rounded">
                                                <span class="text-gray-600">{{ $type }}</span>
                                                <span class="font-semibold text-gray-900">{{ $count }} {{ $count > 1 ? 'livrables' : 'livrable' }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if($livrablesParEtudiant->count() > 0)
                                <div>
                                    <h4 class="text-sm font-medium text-gray-700 mb-2">Livrables par étudiant :</h4>
                                    <div class="space-y-2">
                                        @foreach($livrablesParEtudiant as $item)
                                            <div class="flex justify-between items-center p-2 bg-gray-50 rounded">
                                                <span class="text-gray-600">{{ $item['nom'] }}</span>
                                                <span class="font-semibold text-gray-900">{{ $item['count'] }} {{ $item['count'] > 1 ? 'livrables' : 'livrable' }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>

