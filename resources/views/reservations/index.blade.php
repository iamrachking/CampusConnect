<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Gestion des Réservations') }}
            </h2>
            @if(Auth::user()->role->name === 'Enseignant')
                <a href="{{ route('reservations.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                    Nouvelle Réservation
                </a>
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

            <!-- Filtres -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Filtres</h3>
                    <form method="GET" action="{{ route('reservations.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <select name="statut" class="border-gray-300 rounded-md shadow-sm">
                            <option value="">Tous les statuts</option>
                            <option value="pending" {{ request('statut') === 'pending' ? 'selected' : '' }}>En attente</option>
                            <option value="approved" {{ request('statut') === 'approved' ? 'selected' : '' }}>Approuvées</option>
                            <option value="rejected" {{ request('statut') === 'rejected' ? 'selected' : '' }}>Rejetées</option>
                        </select>
                        <select name="type" class="border-gray-300 rounded-md shadow-sm">
                            <option value="">Tous les types</option>
                            <option value="salle" {{ request('type') === 'salle' ? 'selected' : '' }}>Salles</option>
                            <option value="materiel" {{ request('type') === 'materiel' ? 'selected' : '' }}>Matériels</option>
                        </select>
                        <input type="text" name="search" value="{{ request('search') }}" class="border-gray-300 rounded-md shadow-sm" placeholder="Rechercher par motif...">
                        <div class="flex space-x-2">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                                Filtrer
                            </button>
                            <a href="{{ route('reservations.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded text-center">
                                Effacer
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Liste des réservations -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Réservations</h3>
                    
                    @if($reservations->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Élément</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Utilisateur</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date début</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date fin</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($reservations as $reservation)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                    {{ $reservation->item_type === 'App\\Models\\Salle' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                                    {{ $reservation->item_type === 'App\\Models\\Salle' ? 'Salle' : 'Matériel' }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $reservation->item->nom_salle ?? $reservation->item->nom_materiel }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $reservation->user->getFullNameAttribute() }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $reservation->date_debut->format('d/m/Y H:i') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $reservation->date_fin->format('d/m/Y H:i') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                    {{ $reservation->statut === 'approved' ? 'bg-green-100 text-green-800' : 
                                                       ($reservation->statut === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                                    {{ ucfirst($reservation->statut) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex space-x-2">
                                                    <a href="{{ route('reservations.show', $reservation) }}" class="text-blue-600 hover:text-blue-900">Voir</a>
                                                    
                                                    @if(Auth::user()->role->name === 'Enseignant' && $reservation->user_id === Auth::id() && $reservation->statut === 'pending')
                                                        <a href="{{ route('reservations.edit', $reservation) }}" class="text-yellow-600 hover:text-yellow-900">Modifier</a>
                                                        <form method="POST" action="{{ route('reservations.destroy', $reservation) }}" class="inline" id="delete-reservation-{{ $reservation->id }}">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" class="text-red-600 hover:text-red-900" onclick="confirmDelete('delete-reservation-{{ $reservation->id }}', 'Cette réservation')">Supprimer</button>
                                                        </form>
                                                    @endif
                                                    
                                                    @if(Auth::user()->role->name === 'Administrateur' && $reservation->statut === 'pending')
                                                        <form method="POST" action="{{ route('reservations.approve', $reservation) }}" class="inline">
                                                            @csrf
                                                            <button type="submit" class="text-green-600 hover:text-green-900">Approuver</button>
                                                        </form>
                                                        <form method="POST" action="{{ route('reservations.reject', $reservation) }}" class="inline">
                                                            @csrf
                                                            <button type="submit" class="text-red-600 hover:text-red-900">Rejeter</button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="mt-6">
                            {{ $reservations->links() }}
                        </div>
                    @else
                        <div class="text-center py-8">
                            <p class="text-gray-500">Aucune réservation trouvée.</p>
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
