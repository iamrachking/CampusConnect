<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Détails de la Salle') }}
            </h2>
            <div class="flex space-x-2">
                @if(Auth::user()->role->name === 'Administrateur')
                    <a href="{{ route('salles.edit', $salle) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded">
                        Modifier
                    </a>
                @endif
                <a href="{{ route('salles.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
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
                
                <!-- Informations de la salle -->
                <div class="lg:col-span-1">
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ $salle->nom_salle }}</h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <span class="font-medium text-gray-700">Statut :</span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ml-2
                                        {{ $salle->disponible ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $salle->disponible ? 'Disponible' : 'Indisponible' }}
                                    </span>
                                </div>
                                
                               
                                <div>
                                    <span class="font-medium text-gray-700">Capacité :</span>
                                    <span class="ml-2">{{ $salle->capacite }} places</span>
                                </div>
                                
                                
                                <div>
                                    <span class="font-medium text-gray-700">Localisation :</span>
                                    <p class="ml-2 text-gray-600">{{ $salle->localisation }}</p>
                                </div>

                                @if($salle->latitude && $salle->longitude)
                                    <div>
                                        <span class="font-medium text-gray-700">Coordonnées GPS :</span>
                                        <p class="ml-2 text-gray-600 text-xs">{{ number_format($salle->latitude, 6) }}, {{ number_format($salle->longitude, 6) }}</p>
                                    </div>
                                @endif
                                
                            </div>

                            @if($salle->latitude && $salle->longitude)
                                <div class="mt-4">
                                    <h4 class="text-sm font-medium text-gray-700 mb-2">Carte</h4>
                                    <div id="map" style="height: 300px; width: 100%; border: 1px solid #ddd; border-radius: 8px;"></div>
                                    <a href="https://www.google.com/maps?q={{ $salle->latitude }},{{ $salle->longitude }}" 
                                       target="_blank" 
                                       class="mt-2 inline-block text-sm text-blue-600 hover:text-blue-800">
                                        Ouvrir dans Google Maps →
                                    </a>
                                </div>
                            @endif

                            @if(Auth::user()->role->name === 'Enseignant')
                                <div class="mt-6">
                                    <a href="{{ route('reservations.create') }}?type=salle&id={{ $salle->id }}" 
                                       class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded text-center block">
                                        Réserver cette salle
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Réservations -->
                <div class="lg:col-span-2">
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">Réservations</h3>
                                <button onclick="checkAvailability()" class="bg-green-500 hover:bg-green-600 text-white font-bold py-1 px-3 rounded text-sm">
                                    Vérifier disponibilité
                                </button>
                            </div>

                            @if($reservations->count() > 0)
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
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
                                                        <a href="{{ route('reservations.show', $reservation) }}" class="text-blue-600 hover:text-blue-900">Voir</a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                
                                <!-- Pagination -->
                                <div class="mt-4">
                                    {{ $reservations->links() }}
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <p class="text-gray-500">Aucune réservation trouvée pour cette salle.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal pour vérifier la disponibilité -->
    <div id="availabilityModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden">
        <div class="flex items-center justify-center min-h-screen">
            <div class="bg-white p-6 rounded-lg shadow-xl max-w-md w-full mx-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Vérifier la disponibilité</h3>
                <form id="availabilityForm" method="POST" action="{{ route('salles.availability', $salle) }}">
                    @csrf
                    <div class="mb-4">
                        <label for="check_date_debut" class="block text-sm font-medium text-gray-700 mb-2">Date de début</label>
                        <input type="datetime-local" id="check_date_debut" name="date_debut" 
                               min="{{ now()->format('Y-m-d\TH:i') }}"
                               class="w-full border-gray-300 rounded-md shadow-sm" required>
                    </div>
                    <div class="mb-4">
                        <label for="check_date_fin" class="block text-sm font-medium text-gray-700 mb-2">Date de fin</label>
                        <input type="datetime-local" id="check_date_fin" name="date_fin" 
                               min="{{ now()->format('Y-m-d\TH:i') }}"
                               class="w-full border-gray-300 rounded-md shadow-sm" required>
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button type="button" onclick="closeAvailabilityModal()" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                            Annuler
                        </button>
                        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                            Vérifier
                        </button>
                    </div>
                </form>
                <div id="availabilityResult" class="mt-4 hidden"></div>
            </div>
        </div>
    </div>

    <script>
        function checkAvailability() {
            document.getElementById('availabilityModal').classList.remove('hidden');
        }

        function closeAvailabilityModal() {
            document.getElementById('availabilityModal').classList.add('hidden');
            document.getElementById('availabilityResult').classList.add('hidden');
        }

        document.getElementById('availabilityForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const resultDiv = document.getElementById('availabilityResult');
            
            fetch(`{{ route('salles.availability', $salle) }}`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                resultDiv.classList.remove('hidden');
                if (data.success) {
                    if (data.available) {
                        resultDiv.innerHTML = '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">Salle disponible pour cette période</div>';
                    } else {
                        resultDiv.innerHTML = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">Salle non disponible pour cette période</div>';
                    }
                } else {
                    resultDiv.innerHTML = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">Erreur lors de la vérification</div>';
                }
            })
            .catch(error => {
                resultDiv.classList.remove('hidden');
                resultDiv.innerHTML = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">Erreur lors de la vérification</div>';
            });
        });

        // Validation des dates
        document.getElementById('check_date_debut').addEventListener('change', function() {
            const dateFin = document.getElementById('check_date_fin');
            dateFin.min = this.value;
        });
    </script>

    @if($salle->latitude && $salle->longitude)
        <!-- Leaflet CSS -->
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
        <!-- Leaflet JS -->
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        
        <script>
            // Initialiser la carte avec les coordonnées de la salle
            const map = L.map('map').setView([{{ $salle->latitude }}, {{ $salle->longitude }}], 17);

            // Ajouter la couche OpenStreetMap
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            // Ajouter un marqueur à l'emplacement de la salle
            L.marker([{{ $salle->latitude }}, {{ $salle->longitude }}])
                .addTo(map)
                .bindPopup('<strong>{{ addslashes($salle->nom_salle) }}</strong><br>{{ addslashes($salle->localisation) }}')
                .openPopup();
        </script>
    @endif
</x-app-layout>
