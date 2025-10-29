<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Gestion des Projets') }}
            </h2>
            <div class="flex space-x-2">
                @if(Auth::user()->role->name === 'Étudiant')
                    <a href="{{ route('projets.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                        Nouveau Projet
                    </a>
                @endif
                <a href="{{ route('dashboard') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                    Retour Dashboard
                </a>
            </div>
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

            <!-- Filtres -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Filtres</h3>
                    <form method="GET" action="{{ route('projets.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @if(Auth::user()->role->name === 'Administrateur')
                            <select name="encadrant" class="border-gray-300 rounded-md shadow-sm">
                                <option value="">Tous les encadrants</option>
                                @foreach(\App\Models\User::whereHas('role', function($q) { $q->where('name', 'Enseignant'); })->get() as $enseignant)
                                    <option value="{{ $enseignant->id }}" {{ request('encadrant') == $enseignant->id ? 'selected' : '' }}>
                                        {{ $enseignant->getFullNameAttribute() }}
                                    </option>
                                @endforeach
                            </select>
                        @endif
                        <input type="text" name="search" value="{{ request('search') }}" class="border-gray-300 rounded-md shadow-sm" placeholder="Rechercher par titre ou description...">
                        <div class="flex space-x-2">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                                Filtrer
                            </button>
                            <a href="{{ route('projets.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded text-center">
                                Effacer
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Liste des projets -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($projets as $projet)
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">{{ $projet->titre }}</h3>
                                <span class="text-xs text-gray-500">{{ $projet->created_at->format('d/m/Y') }}</span>
                            </div>
                            
                            <p class="text-gray-600 text-sm mb-4 line-clamp-3">{{ Str::limit($projet->description, 100) }}</p>
                            
                            <div class="space-y-2 text-sm text-gray-600 mb-4">
                                <p><span class="font-medium">Encadrant :</span> {{ $projet->encadrant ? $projet->encadrant->nom . ' ' . $projet->encadrant->prenom : 'Non assigné' }}</p>
                                <p><span class="font-medium">Membres :</span> {{ $projet->equipes->count() }}</p>
                                <p><span class="font-medium">Livrables :</span> {{ $projet->livrables->count() }}</p>
                            </div>

                            <div class="flex justify-between items-center">
                                <a href="{{ route('projets.show', $projet) }}" class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                    Voir détails
                                </a>
                                
                                <div class="flex space-x-2">
                                    @if(Auth::user()->role->name === 'Étudiant')
                                        @php
                                            $isMember = $projet->equipes->where('user_id', Auth::id())->count() > 0;
                                            $isChefProjet = $projet->equipes->where('user_id', Auth::id())->where('role_membre', 'Chef de projet')->count() > 0;
                                        @endphp
                                        
                                        @if(!$isMember)
                                            <form method="POST" action="{{ route('projets.join', $projet) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="text-green-600 hover:text-green-900 text-sm font-medium">
                                                    Rejoindre
                                                </button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('projets.leave', $projet) }}" class="inline" id="leave-projet-{{ $projet->id }}">
                                                @csrf
                                                <button type="button" class="text-red-600 hover:text-red-900 text-sm font-medium" 
                                                        onclick="confirmLeave('leave-projet-{{ $projet->id }}')">
                                                    Quitter
                                                </button>
                                            </form>
                                        @endif
                                        
                                        @if($isChefProjet)
                                            <a href="{{ route('projets.edit', $projet) }}" class="text-yellow-600 hover:text-yellow-900 text-sm font-medium">
                                                Modifier
                                            </a>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($projets->count() === 0)
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6 text-center">
                        <p class="text-gray-500">Aucun projet trouvé.</p>
                        @if(Auth::user()->role->name === 'Étudiant')
                            <a href="{{ route('projets.create') }}" class="mt-4 inline-block bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                                Créer votre premier projet
                            </a>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Pagination -->
            @if($projets->hasPages())
                <div class="mt-6">
                    {{ $projets->links() }}
                </div>
            @endif
        </div>
    </div>

    <script>
        function confirmLeave(formId) {
            Swal.fire({
                title: 'Êtes-vous sûr ?',
                text: 'Vous êtes sur le point de quitter ce projet',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Oui, quitter',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(formId).submit();
                }
            });
        }
    </script>
</x-app-layout>
