<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Détails de l\'Équipe') }}
            </h2>
            <a href="{{ route('equipes.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                Retour
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
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

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Informations du Membre</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Informations du projet -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="text-md font-semibold text-gray-900 mb-3">Projet</h4>
                            <div class="space-y-2">
                                <p><span class="font-medium">Titre :</span> 
                                    <a href="{{ route('projets.show', $equipe->projet) }}" class="text-blue-600 hover:text-blue-900">
                                        {{ $equipe->projet->titre }}
                                    </a>
                                </p>
                                <p><span class="font-medium">Description :</span> {{ Str::limit($equipe->projet->description, 100) }}</p>
                                <p><span class="font-medium">Encadrant :</span> 
                                    {{ $equipe->projet->encadrant ? $equipe->projet->encadrant->getFullNameAttribute() : 'Non assigné' }}
                                </p>
                                <p><span class="font-medium">Créé le :</span> {{ $equipe->projet->created_at->format('d/m/Y à H:i') }}</p>
                            </div>
                        </div>

                        <!-- Informations du membre -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="text-md font-semibold text-gray-900 mb-3">Membre</h4>
                            <div class="space-y-2">
                                <p><span class="font-medium">Nom :</span> {{ $equipe->user->getFullNameAttribute() }}</p>
                                <p><span class="font-medium">Email :</span> {{ $equipe->user->email }}</p>
                                <p><span class="font-medium">Rôle :</span> 
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $equipe->role_membre === 'Chef de projet' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $equipe->role_membre }}
                                    </span>
                                </p>
                                <p><span class="font-medium">Ajouté le :</span> {{ $equipe->created_at->format('d/m/Y à H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="mt-6 flex justify-end space-x-4">
                        @if(Auth::user()->role->name === 'Administrateur' || 
                            (Auth::user()->role->name === 'Enseignant' && $equipe->projet->encadrant_id === Auth::id()) ||
                            (Auth::user()->role->name === 'Étudiant' && $equipe->user_id === Auth::id()))
                            <a href="{{ route('equipes.edit', $equipe) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded">
                                Modifier le rôle
                            </a>
                        @endif
                        
                        @if(Auth::user()->role->name === 'Administrateur' || 
                            (Auth::user()->role->name === 'Enseignant' && $equipe->projet->encadrant_id === Auth::id()) ||
                            (Auth::user()->role->name === 'Étudiant' && $equipe->user_id === Auth::id()))
                            <form method="POST" action="{{ route('equipes.destroy', $equipe) }}" class="inline" id="delete-equipe-{{ $equipe->id }}">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded" onclick="confirmDelete('delete-equipe-{{ $equipe->id }}', 'ce membre')">
                                    Retirer de l'équipe
                                </button>
                            </form>
                        @endif
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
    </script>
</x-app-layout>
