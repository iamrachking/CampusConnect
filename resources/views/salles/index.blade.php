<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Gestion des Salles') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('dashboard') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                    Retour Dashboard
                </a>
                @if(Auth::user()->role->name === 'Administrateur')
                    <a href="{{ route('salles.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                        Nouvelle Salle
                    </a>
                @endif
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
                    <form method="GET" action="{{ route('salles.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <select name="disponible" class="border-gray-300 rounded-md shadow-sm">
                            <option value="">Toutes les salles</option>
                            <option value="disponible" {{ request('disponible') === 'disponible' ? 'selected' : '' }}>Disponibles</option>
                            <option value="indisponible" {{ request('disponible') === 'indisponible' ? 'selected' : '' }}>Indisponibles</option>
                        </select>
                        <input type="text" name="search" value="{{ request('search') }}" class="border-gray-300 rounded-md shadow-sm" placeholder="Rechercher par nom...">
                        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                            Filtrer
                        </button>
                    </form>
                    @if(request('disponible') || request('search'))
                        <div class="mt-4">
                            <a href="{{ route('salles.index') }}" class="text-blue-600 hover:text-blue-800 text-sm">
                                Effacer les filtres
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Liste des salles -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($salles as $salle)
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">{{ $salle->nom_salle }}</h3>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $salle->disponible ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $salle->disponible ? 'Disponible' : 'Indisponible' }}
                                </span>
                            </div>
                            
                            <div class="space-y-2 text-sm text-gray-600 mb-4">
                                <p><span class="font-medium">Capacité :</span> {{ $salle->capacite }} places</p>
                                <p><span class="font-medium">Localisation :</span> {{ $salle->localisation }}</p>
                                <p><span class="font-medium">Réservations actives :</span> {{ $salle->reservations->count() }}</p>
                            </div>

                            <div class="flex justify-between items-center">
                                <a href="{{ route('salles.show', $salle) }}" class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                    Voir détails
                                </a>
                                
                                @if(Auth::user()->role->name === 'Administrateur')
                                    <div class="flex space-x-2">
                                        <a href="{{ route('salles.edit', $salle) }}" class="text-yellow-600 hover:text-yellow-900 text-sm font-medium">
                                            Modifier
                                        </a>
                                        <form method="POST" action="{{ route('salles.destroy', $salle) }}" class="inline" id="delete-salle-{{ $salle->id }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="text-red-600 hover:text-red-900 text-sm font-medium" 
                                                    onclick="confirmDelete('delete-salle-{{ $salle->id }}', 'Cette salle')">
                                                Supprimer
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($salles->count() === 0)
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6 text-center">
                        <p class="text-gray-500">Aucune salle trouvée.</p>
                    </div>
                </div>
            @endif

            <!-- Pagination -->
            @if($salles->hasPages())
                <div class="mt-6">
                    {{ $salles->links() }}
                </div>
            @endif
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
