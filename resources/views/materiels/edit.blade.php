<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Modifier le Matériel') }}
            </h2>
            <a href="{{ route('materiels.show', $materiel) }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
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
                    <form method="POST" action="{{ route('materiels.update', $materiel) }}">
                        @csrf
                        @method('PUT')
                        
                        <!-- Nom du matériel -->
                        <div class="mb-6">
                            <label for="nom_materiel" class="block text-sm font-medium text-gray-700 mb-2">Nom du matériel</label>
                            <input type="text" name="nom_materiel" id="nom_materiel" 
                                   value="{{ old('nom_materiel', $materiel->nom_materiel) }}" 
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                                   placeholder="Ex: Vidéoprojecteur HD, Ordinateur portable..." required>
                        </div>

                        <!-- Type de matériel -->
                        <div class="mb-6">
                            <label for="type_materiel" class="block text-sm font-medium text-gray-700 mb-2">Type de matériel</label>
                            <select name="type_materiel" id="type_materiel" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                                <option value="">Sélectionnez un type</option>
                                <option value="Vidéoprojecteur" {{ old('type_materiel', $materiel->type_materiel) === 'Vidéoprojecteur' ? 'selected' : '' }}>Vidéoprojecteur</option>
                                <option value="Ordinateur" {{ old('type_materiel', $materiel->type_materiel) === 'Ordinateur' ? 'selected' : '' }}>Ordinateur</option>
                                <option value="Tablette" {{ old('type_materiel', $materiel->type_materiel) === 'Tablette' ? 'selected' : '' }}>Tablette</option>
                                <option value="Écran" {{ old('type_materiel', $materiel->type_materiel) === 'Écran' ? 'selected' : '' }}>Écran</option>
                                <option value="Audio" {{ old('type_materiel', $materiel->type_materiel) === 'Audio' ? 'selected' : '' }}>Équipement audio</option>
                                <option value="Autre" {{ old('type_materiel', $materiel->type_materiel) === 'Autre' ? 'selected' : '' }}>Autre</option>
                            </select>
                        </div>

                        <!-- Disponibilité -->
                        <div class="mb-6">
                            <div class="flex items-center">
                                <input type="checkbox" name="disponible" id="disponible" value="1" 
                                       {{ old('disponible', $materiel->disponible) ? 'checked' : '' }}
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="disponible" class="ml-2 block text-sm text-gray-900">
                                    Matériel disponible
                                </label>
                            </div>
                        </div>

                        <!-- Boutons -->
                        <div class="flex justify-end space-x-4">
                            <a href="{{ route('materiels.show', $materiel) }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                                Annuler
                            </a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                                Mettre à jour
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
