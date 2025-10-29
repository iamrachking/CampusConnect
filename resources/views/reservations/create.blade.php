<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Nouvelle Réservation') }}
            </h2>
            <a href="{{ route('reservations.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                Retour
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            
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
                    <form method="POST" action="{{ route('reservations.store') }}">
                        @csrf
                        
                        <!-- Type de réservation -->
                        <div class="mb-6">
                            <label for="item_type" class="block text-sm font-medium text-gray-700 mb-2">Type de réservation</label>
                            <select name="item_type" id="item_type" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                                <option value="">Sélectionnez un type</option>
                                <option value="salle" {{ old('item_type') === 'salle' ? 'selected' : '' }}>Salle</option>
                                <option value="materiel" {{ old('item_type') === 'materiel' ? 'selected' : '' }}>Matériel</option>
                            </select>
                        </div>

                        <!-- Élément à réserver -->
                        <div class="mb-6">
                            <label for="item_id" class="block text-sm font-medium text-gray-700 mb-2">Élément à réserver</label>
                            <select name="item_id" id="item_id" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                                <option value="">Sélectionnez d'abord un type</option>
                            </select>
                        </div>

                        <!-- Date de début -->
                        <div class="mb-6">
                            <label for="date_debut" class="block text-sm font-medium text-gray-700 mb-2">Date et heure de début</label>
                            <input type="datetime-local" name="date_debut" id="date_debut" 
                                   value="{{ old('date_debut') }}" 
                                   min="{{ now()->format('Y-m-d\TH:i') }}"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                        </div>

                        <!-- Date de fin -->
                        <div class="mb-6">
                            <label for="date_fin" class="block text-sm font-medium text-gray-700 mb-2">Date et heure de fin</label>
                            <input type="datetime-local" name="date_fin" id="date_fin" 
                                   value="{{ old('date_fin') }}" 
                                   min="{{ now()->format('Y-m-d\TH:i') }}"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                        </div>

                        <!-- Motif -->
                        <div class="mb-6">
                            <label for="motif" class="block text-sm font-medium text-gray-700 mb-2">Motif de la réservation</label>
                            <textarea name="motif" id="motif" rows="4" 
                                      class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                                      placeholder="Décrivez le motif de votre réservation..." required>{{ old('motif') }}</textarea>
                        </div>

                        <!-- Boutons -->
                        <div class="flex justify-end space-x-4">
                            <a href="{{ route('reservations.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                                Annuler
                            </a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                                Créer la réservation
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('item_type').addEventListener('change', function() {
            const type = this.value;
            const itemSelect = document.getElementById('item_id');
            
            // Vider les options
            itemSelect.innerHTML = '<option value="">Chargement...</option>';
            
            if (type === 'salle') {
                // Charger les salles disponibles
                fetch('{{ route("salles.index") }}?ajax=1')
                    .then(response => response.json())
                    .then(data => {
                        itemSelect.innerHTML = '<option value="">Sélectionnez une salle</option>';
                        data.forEach(salle => {
                            if (salle.disponible) {
                                const option = document.createElement('option');
                                option.value = salle.id;
                                option.textContent = `${salle.nom_salle} (${salle.capacite} places) - ${salle.localisation}`;
                                itemSelect.appendChild(option);
                            }
                        });
                    })
                    .catch(error => {
                        itemSelect.innerHTML = '<option value="">Erreur de chargement</option>';
                    });
            } else if (type === 'materiel') {
                // Charger le matériel disponible
                fetch('{{ route("materiels.index") }}?ajax=1')
                    .then(response => response.json())
                    .then(data => {
                        itemSelect.innerHTML = '<option value="">Sélectionnez un matériel</option>';
                        data.forEach(materiel => {
                            if (materiel.disponible) {
                                const option = document.createElement('option');
                                option.value = materiel.id;
                                option.textContent = materiel.nom_materiel;
                                itemSelect.appendChild(option);
                            }
                        });
                    })
                    .catch(error => {
                        itemSelect.innerHTML = '<option value="">Erreur de chargement</option>';
                    });
            } else {
                itemSelect.innerHTML = '<option value="">Sélectionnez d\'abord un type</option>';
            }
        });

        // Validation des dates
        document.getElementById('date_debut').addEventListener('change', function() {
            const dateFin = document.getElementById('date_fin');
            dateFin.min = this.value;
        });
    </script>
</x-app-layout>
