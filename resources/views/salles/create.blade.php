<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Nouvelle Salle') }}
            </h2>
            <a href="{{ route('salles.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                Retour
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Messages d'erreur -->
            @if($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('salles.store') }}">
                        @csrf
                        
                        <!-- Nom de la salle -->
                        <div class="mb-6">
                            <label for="nom_salle" class="block text-sm font-medium text-gray-700 mb-2">Nom de la salle</label>
                            <input type="text" name="nom_salle" id="nom_salle" 
                                   value="{{ old('nom_salle') }}" 
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                                   placeholder="Ex: Amphithéâtre A, Salle de cours B1..." required>
                        </div>

                        <!-- Capacité -->
                        <div class="mb-6">
                            <label for="capacite" class="block text-sm font-medium text-gray-700 mb-2">Capacité</label>
                            <input type="number" name="capacite" id="capacite" 
                                   value="{{ old('capacite') }}" 
                                   min="1" max="1000"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                                   placeholder="Nombre de places" required>
                        </div>

                        <!-- Localisation -->
                        <div class="mb-6">
                            <label for="localisation" class="block text-sm font-medium text-gray-700 mb-2">Localisation</label>
                            <textarea name="localisation" id="localisation" rows="3" 
                                      class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                                      placeholder="Ex: Bâtiment Principal - 1er étage" required>{{ old('localisation') }}</textarea>
                        </div>

                        <!-- Géolocalisation -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Géolocalisation (optionnel)</label>
                            <p class="text-xs text-gray-500 mb-2">Cliquez sur la carte pour définir l'emplacement de la salle</p>
                            <div id="map" style="height: 300px; width: 100%; border: 1px solid #ddd; border-radius: 8px;"></div>
                            <div class="mt-2 grid grid-cols-2 gap-4">
                                <div>
                                    <label for="latitude" class="block text-xs text-gray-600 mb-1">Latitude</label>
                                    <input type="number" step="any" name="latitude" id="latitude" 
                                           value="{{ old('latitude') }}" 
                                           class="w-full border-gray-300 rounded-md shadow-sm text-sm"
                                           placeholder="Ex: 48.8566" readonly>
                                </div>
                                <div>
                                    <label for="longitude" class="block text-xs text-gray-600 mb-1">Longitude</label>
                                    <input type="number" step="any" name="longitude" id="longitude" 
                                           value="{{ old('longitude') }}" 
                                           class="w-full border-gray-300 rounded-md shadow-sm text-sm"
                                           placeholder="Ex: 2.3522" readonly>
                                </div>
                            </div>
                        </div>

                        <!-- Disponibilité -->
                        <div class="mb-6">
                            <div class="flex items-center">
                                <input type="checkbox" name="disponible" id="disponible" value="1" 
                                       {{ old('disponible', true) ? 'checked' : '' }}
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="disponible" class="ml-2 block text-sm text-gray-900">
                                    Salle disponible
                                </label>
                            </div>
                        </div>

                        <!-- Boutons -->
                        <div class="flex justify-end space-x-4">
                            <a href="{{ route('salles.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                                Annuler
                            </a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                                Créer la salle
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        // Coordonnées par défaut du campus de l'université de d'abomey-calavi
        const defaultLat = 6.41586901;
        const defaultLng = 2.34154701;

        // initialisation de la carte
        const map = L.map('map').setView([defaultLat, defaultLng], 15);

        // ajout de la couche OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        let marker = null;
        const latitudeInput = document.getElementById('latitude');
        const longitudeInput = document.getElementById('longitude');

        // création d'un marqueur initial au centre de la carte
        marker = L.marker([defaultLat, defaultLng], {
            draggable: true
        }).addTo(map);

        // mise à jour des coordonnées quand on déplace le marqueur
        marker.on('dragend', function(e) {
            const position = marker.getLatLng();
            latitudeInput.value = position.lat.toFixed(8);
            longitudeInput.value = position.lng.toFixed(8);
        });

        // mise à jour du marqueur quand on clique sur la carte
        map.on('click', function(e) {
            const lat = e.latlng.lat;
            const lng = e.latlng.lng;
            
            if (marker) {
                marker.setLatLng([lat, lng]);
            } else {
                marker = L.marker([lat, lng], {draggable: true}).addTo(map);
                marker.on('dragend', function(e) {
                    const position = marker.getLatLng();
                    latitudeInput.value = position.lat.toFixed(8);
                    longitudeInput.value = position.lng.toFixed(8);
                });
            }
            
            latitudeInput.value = lat.toFixed(8);
            longitudeInput.value = lng.toFixed(8);
        });

        // si des valeurs existent déjà (après erreur de validation)
        @if(old('latitude') && old('longitude'))
            const oldLat = {{ old('latitude') }};
            const oldLng = {{ old('longitude') }};
            map.setView([oldLat, oldLng], 15);
            if (marker) {
                marker.setLatLng([oldLat, oldLng]);
            } else {
                marker = L.marker([oldLat, oldLng], {draggable: true}).addTo(map);
                marker.on('dragend', function(e) {
                    const position = marker.getLatLng();
                    latitudeInput.value = position.lat.toFixed(8);
                    longitudeInput.value = position.lng.toFixed(8);
                });
            }
        @endif
    </script>
</x-app-layout>
