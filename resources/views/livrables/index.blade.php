<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Livrables du Projet') }}
            </h2>
            @if(Auth::user()->role->name === 'Étudiant')
                @php
                    $isMember = $projet->equipes->where('user_id', Auth::id())->count() > 0;
                @endphp
                @if($isMember)
                    <a href="{{ route('projets.livrables.create', $projet) }}" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                        Déposer un livrable
                    </a>
                @endif
            @endif
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

            <!-- Informations du projet -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $projet->titre }}</h3>
                    <p class="text-gray-600">{{ Str::limit($projet->description, 200) }}</p>
                    <div class="mt-4 flex space-x-4 text-sm text-gray-500">
                        <span>Encadrant : {{ $projet->encadrant ? ($projet->encadrant->nom . ' ' . $projet->encadrant->prenom) : 'Non assigné' }}</span>
                        <span>Livrables : {{ $livrables->total() }}</span>
                    </div>
                </div>
            </div>

            <!-- Filtres -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Filtres</h3>
                    <form method="GET" action="{{ route('projets.livrables.index', $projet) }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <select name="type" class="border-gray-300 rounded-md shadow-sm">
                            <option value="">Tous les types</option>
                            <option value="Rapport" {{ request('type') === 'Rapport' ? 'selected' : '' }}>Rapport</option>
                            <option value="Présentation" {{ request('type') === 'Présentation' ? 'selected' : '' }}>Présentation</option>
                            <option value="Code source" {{ request('type') === 'Code source' ? 'selected' : '' }}>Code source</option>
                            <option value="Documentation" {{ request('type') === 'Documentation' ? 'selected' : '' }}>Documentation</option>
                            <option value="Autre" {{ request('type') === 'Autre' ? 'selected' : '' }}>Autre</option>
                        </select>
                        <input type="text" name="search" value="{{ request('search') }}" class="border-gray-300 rounded-md shadow-sm" placeholder="Rechercher par nom ou auteur...">
                        <div class="flex space-x-2">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">Filtrer</button>
                            <a href="{{ route('projets.livrables.index', $projet) }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded text-center">Effacer</a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Liste des livrables -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Livrables</h3>
                    
                    @if($livrables->count() > 0)
                        <div class="space-y-4">
                            @foreach($livrables as $livrable)
                                <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <h4 class="text-lg font-medium text-gray-900">{{ $livrable->nom_livrable }}</h4>
                                            <p class="text-sm text-gray-600 mt-1">{{ $livrable->description }}</p>
                                            
                                            <div class="flex items-center space-x-4 mt-3 text-sm text-gray-500">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    {{ ucfirst($livrable->type_livrable) }}
                                                </span>
                                                <span>Déposé par {{ $livrable->user->nom }} {{ $livrable->user->prenom }}</span>
                                                <span>{{ $livrable->created_at->format('d/m/Y à H:i') }}</span>
                                                
                                            </div>
                                        </div>
                                        
                                        <div class="flex items-center space-x-2 ml-4">
                                            <a href="{{ route('livrables.download', $livrable) }}" 
                                               class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-1 px-3 rounded text-sm">
                                                Télécharger
                                            </a>
                                            
                                            @if(Auth::user()->role->name === 'Étudiant' && $livrable->user_id === Auth::id())
                                                <a href="{{ route('livrables.edit', $livrable) }}" 
                                                   class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-1 px-3 rounded text-sm">
                                                    Modifier
                                                </a>
                                                <form method="POST" action="{{ route('livrables.destroy', $livrable) }}" class="inline" id="delete-livrable-{{ $livrable->id }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" 
                                                            class="bg-red-500 hover:bg-red-600 text-white font-bold py-1 px-3 rounded text-sm"
                                                            onclick="confirmDelete('delete-livrable-{{ $livrable->id }}', 'Ce livrable')">
                                                        Supprimer
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <!-- Pagination -->
                        <div class="mt-6">
                            {{ $livrables->links() }}
                        </div>
                    @else
                        <div class="text-center py-8">
                            <p class="text-gray-500 mb-4">Aucun livrable déposé pour ce projet.</p>
                            @if(Auth::user()->role->name === 'Étudiant')
                                @php
                                    $isMember = $projet->equipes->where('user_id', Auth::id())->count() > 0;
                                @endphp
                                @if($isMember)
                                    <a href="{{ route('projets.livrables.create', $projet) }}" 
                                       class="inline-block bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                                        Déposer le premier livrable
                                    </a>
                                @endif
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete(formId, itemName) {
            Swal.fire({
                title: 'Êtes-vous sûr ?',
                text: 'Vous êtes sur le point de supprimer ' + itemName,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Oui, supprimer',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(formId).submit();
                }
            });
        }
    </script>
</x-app-layout>
