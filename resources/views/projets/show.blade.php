<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Détails du Projet') }}
            </h2>
            <div class="flex space-x-2">
                @if(Auth::user()->role->name === 'Étudiant')
                    @php
                        $isMember = $projet->equipes->where('user_id', Auth::id())->count() > 0;
                        $isChefProjet = $projet->equipes->where('user_id', Auth::id())->where('role_membre', 'Chef de projet')->count() > 0;
                    @endphp
                    
                    @if(!$isMember)
                        <form method="POST" action="{{ route('projets.join', $projet) }}" class="inline">
                            @csrf
                            <button type="submit" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded">
                                Rejoindre le projet
                            </button>
                        </form>
                    @elseif(!$isChefProjet)
                        <form method="POST" action="{{ route('projets.leave', $projet) }}" class="inline" id="leave-projet-show-{{ $projet->id }}">
                            @csrf
                            <button type="button" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded" 
                                    onclick="confirmLeave('leave-projet-show-{{ $projet->id }}')">
                                Quitter le projet
                            </button>
                        </form>
                    @endif
                    
                    @if($isChefProjet)
                        <a href="{{ route('projets.edit', $projet) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded">
                            Modifier
                        </a>
                    @endif
                @endif
                <a href="{{ route('projets.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                    Retour
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

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Informations du projet -->
                <div class="lg:col-span-2">
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
                        <div class="p-6">
                            <h3 class="text-2xl font-semibold text-gray-900 mb-4">{{ $projet->titre }}</h3>
                            <p class="text-gray-600 mb-6">{{ $projet->description }}</p>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="font-medium text-gray-700">Encadrant :</span>
                                    <span class="ml-2">{{ $projet->encadrant ? $projet->encadrant->nom . ' ' . $projet->encadrant->prenom : 'Non assigné' }}</span>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-700">Créé le :</span>
                                    <span class="ml-2">{{ $projet->created_at->format('d/m/Y') }}</span>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-700">Membres :</span>
                                    <span class="ml-2">{{ $membresEquipe->count() }}</span>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-700">Livrables :</span>
                                    <span class="ml-2">{{ $livrables->count() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Équipe -->
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
                        <div class="p-6">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">Équipe</h3>
                                @if(Auth::user()->role->name === 'Étudiant')
                                    @php
                                        $isChefProjet = $projet->equipes->where('user_id', Auth::id())->where('role_membre', 'Chef de projet')->count() > 0;
                                    @endphp
                                    @if($isChefProjet)
                                        <a href="{{ route('projets.equipes.create', $projet) }}" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-1 px-3 rounded text-sm">
                                            Ajouter un membre
                                        </a>
                                    @endif
                                @endif
                            </div>

                            @if($membresEquipe->count() > 0)
                                <div class="space-y-3">
                                    @foreach($membresEquipe as $membre)
                                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                            <div>
                                                <p class="font-medium text-gray-900">{{ $membre->user->getFullNameAttribute() }}</p>
                                                <p class="text-sm text-gray-600">{{ $membre->user->email }}</p>
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                    {{ $membre->role_membre === 'Chef de projet' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                                    {{ $membre->role_membre }}
                                                </span>
                                                
                                                @if(Auth::user()->role->name === 'Étudiant')
                                                    @php
                                                        $isChefProjet = $projet->equipes->where('user_id', Auth::id())->where('role_membre', 'Chef de projet')->count() > 0;
                                                    @endphp
                                                    @if($isChefProjet && $membre->role_membre !== 'Chef de projet')
                                                        <form method="POST" action="{{ route('equipes.destroy', $membre) }}" class="inline" id="remove-equipe-{{ $membre->id }}">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" class="text-red-600 hover:text-red-900 text-sm" 
                                                                    onclick="confirmDelete('remove-equipe-{{ $membre->id }}', 'Ce membre')">
                                                                Retirer
                                                            </button>
                                                        </form>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-500 text-center py-4">Aucun membre dans l'équipe.</p>
                            @endif
                        </div>
                    </div>

                    <!-- Livrables -->
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">Livrables</h3>
                                @if(Auth::user()->role->name === 'Étudiant')
                                    @php
                                        $isMember = $projet->equipes->where('user_id', Auth::id())->count() > 0;
                                    @endphp
                                    @if($isMember)
                                        <a href="{{ route('projets.livrables.create', $projet) }}" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-1 px-3 rounded text-sm">
                                            Déposer un livrable
                                        </a>
                                    @endif
                                @endif
                            </div>

                            @if($livrables->count() > 0)
                                <div class="space-y-3">
                                    @foreach($livrables as $livrable)
                                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                            <div>
                                                <p class="font-medium text-gray-900">{{ $livrable->nom_livrable }}</p>
                                                <p class="text-sm text-gray-600">Déposé par {{ $livrable->user->getFullNameAttribute() }} le {{ $livrable->created_at->format('d/m/Y à H:i') }}</p>
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    {{ $livrable->type_livrable }}
                                                </span>
                                                
                                                <a href="{{ route('livrables.download', $livrable) }}" class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                                    Télécharger
                                                </a>
                                                
                                                @if(Auth::user()->role->name === 'Étudiant' && $livrable->user_id === Auth::id())
                                                    <a href="{{ route('livrables.edit', $livrable) }}" class="text-yellow-600 hover:text-yellow-900 text-sm font-medium">
                                                        Modifier
                                                    </a>
                                                    <form method="POST" action="{{ route('livrables.destroy', $livrable) }}" class="inline" id="delete-livrable-{{ $livrable->id }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="text-red-600 hover:text-red-900 text-sm font-medium" 
                                                                onclick="confirmDelete('delete-livrable-{{ $livrable->id }}', 'Ce livrable')">
                                                            Supprimer
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-500 text-center py-4">Aucun livrable déposé.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1">
                    <!-- Statistiques -->
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistiques</h3>
                            <div class="space-y-3 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Membres</span>
                                    <span class="font-medium">{{ $membresEquipe->count() }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Livrables</span>
                                    <span class="font-medium">{{ $livrables->count() }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Dernier livrable</span>
                                    <span class="font-medium">
                                        @if($livrables->count() > 0)
                                            {{ $livrables->first()->created_at->diffForHumans() }}
                                        @else
                                            -
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions rapides -->
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions rapides</h3>
                            <div class="space-y-2">
                                <a href="{{ route('projets.livrables.index', $projet) }}" class="block w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded text-center text-sm">
                                    Voir tous les livrables
                                </a>
                                <a href="{{ route('projets.stats', $projet) }}" class="block w-full bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded text-center text-sm">
                                    Statistiques détaillées
                                </a>
                            </div>
                        </div>
                    </div>
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
