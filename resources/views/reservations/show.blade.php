<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Détails de la Réservation') }}
            </h2>
            <a href="{{ route('reservations.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
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
                    
                    <!-- Informations principales -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        
                        <!-- Type et élément -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-900 mb-3">Réservation</h3>
                            <div class="space-y-2">
                                <p><span class="font-medium">Type :</span> 
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $reservation->item_type === 'App\\Models\\Salle' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                        {{ $reservation->item_type === 'App\\Models\\Salle' ? 'Salle' : 'Matériel' }}
                                    </span>
                                </p>
                                <p><span class="font-medium">Élément :</span> {{ $reservation->item->nom_salle ?? $reservation->item->nom_materiel }}</p>
                                @if($reservation->item_type === 'App\\Models\\Salle')
                                    <p><span class="font-medium">Capacité :</span> {{ $reservation->item->capacite }} places</p>
                                    <p><span class="font-medium">Localisation :</span> {{ $reservation->item->localisation }}</p>
                                @endif
                            </div>
                        </div>

                        <!-- Utilisateur et statut -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-900 mb-3">Informations</h3>
                            <div class="space-y-2">
                                <p><span class="font-medium">Demandeur :</span> {{ $reservation->user->getFullNameAttribute() }}</p>
                                <p><span class="font-medium">Email :</span> {{ $reservation->user->email }}</p>
                                <p><span class="font-medium">Statut :</span> 
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $reservation->statut === 'approved' ? 'bg-green-100 text-green-800' : 
                                           ($reservation->statut === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                        {{ ucfirst($reservation->statut) }}
                                    </span>
                                </p>
                                <p><span class="font-medium">Créée le :</span> {{ $reservation->created_at->format('d/m/Y à H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Dates et horaires -->
                    <div class="bg-gray-50 p-4 rounded-lg mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Horaires</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="font-medium text-gray-700">Date et heure de début</p>
                                <p class="text-lg">{{ $reservation->date_debut->format('d/m/Y à H:i') }}</p>
                            </div>
                            <div>
                                <p class="font-medium text-gray-700">Date et heure de fin</p>
                                <p class="text-lg">{{ $reservation->date_fin->format('d/m/Y à H:i') }}</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <p class="font-medium text-gray-700">Durée</p>
                            <p class="text-lg">{{ $reservation->date_debut->diffForHumans($reservation->date_fin, true) }}</p>
                        </div>
                    </div>

                    <!-- Motif -->
                    <div class="bg-gray-50 p-4 rounded-lg mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Motif</h3>
                        <p class="text-gray-700">{{ $reservation->motif }}</p>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-end space-x-4">
                        @if(Auth::user()->role->name === 'Enseignant' && $reservation->user_id === Auth::id() && $reservation->statut === 'pending')
                            <a href="{{ route('reservations.edit', $reservation) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded">
                                Modifier
                            </a>
                            <form method="POST" action="{{ route('reservations.destroy', $reservation) }}" class="inline" id="delete-reservation-{{ $reservation->id }}">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded" 
                                        onclick="confirmDelete('delete-reservation-{{ $reservation->id }}', 'Cette réservation')">
                                    Supprimer
                                </button>
                            </form>
                        @endif
                        
                        @if(Auth::user()->role->name === 'Administrateur' && $reservation->statut === 'pending')
                            <form method="POST" action="{{ route('reservations.approve', $reservation) }}" class="inline">
                                @csrf
                                <button type="submit" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded">
                                    Approuver
                                </button>
                            </form>
                            <form method="POST" action="{{ route('reservations.reject', $reservation) }}" class="inline">
                                @csrf
                                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded">
                                    Rejeter
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
