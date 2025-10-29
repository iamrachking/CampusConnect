<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Déposer un Livrable') }}
            </h2>
            <a href="{{ route('projets.livrables.index', $projet) }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
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

            <!-- Informations du projet -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $projet->titre }}</h3>
                    <p class="text-gray-600">{{ Str::limit($projet->description, 200) }}</p>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('projets.livrables.store', $projet) }}" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- Nom du livrable -->
                        <div class="mb-6">
                            <label for="nom_livrable" class="block text-sm font-medium text-gray-700 mb-2">Nom du livrable</label>
                            <input type="text" name="nom_livrable" id="nom_livrable" 
                                   value="{{ old('nom_livrable') }}" 
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                                   placeholder="Ex: Rapport final, Présentation soutenance..." required>
                        </div>

                        <!-- Type de livrable -->
                        <div class="mb-6">
                            <label for="type_livrable" class="block text-sm font-medium text-gray-700 mb-2">Type de livrable</label>
                            <select name="type_livrable" id="type_livrable" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                                <option value="">Sélectionnez un type</option>
                                <option value="Rapport" {{ old('type_livrable') === 'Rapport' ? 'selected' : '' }}>Rapport</option>
                                <option value="Présentation" {{ old('type_livrable') === 'Présentation' ? 'selected' : '' }}>Présentation</option>
                                <option value="Code source" {{ old('type_livrable') === 'Code source' ? 'selected' : '' }}>Code source</option>
                                <option value="Documentation" {{ old('type_livrable') === 'Documentation' ? 'selected' : '' }}>Documentation</option>
                                <option value="Autre" {{ old('type_livrable') === 'Autre' ? 'selected' : '' }}>Autre</option>
                            </select>
                        </div>

                        <!-- Description -->
                        <div class="mb-6">
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea name="description" id="description" rows="4" 
                                      class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                                      placeholder="Décrivez le contenu de ce livrable..." required>{{ old('description') }}</textarea>
                        </div>

                        <!-- Fichier -->
                        <div class="mb-6">
                            <label for="fichier" class="block text-sm font-medium text-gray-700 mb-2">Fichier</label>
                            <input type="file" name="fichier" id="fichier" 
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                                   accept=".pdf,.doc,.docx,.ppt,.pptx,.zip,.rar,.txt,.md" required>
                            <p class="mt-1 text-sm text-gray-500">
                                Formats acceptés : PDF, DOC, DOCX, PPT, PPTX, ZIP, RAR, TXT, MD (Max: 10MB)
                            </p>
                        </div>

                        <!-- Information importante -->
                        <div class="mb-6 bg-blue-50 border border-blue-200 rounded-md p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-blue-800">
                                        Information importante
                                    </h3>
                                    <div class="mt-2 text-sm text-blue-700">
                                        <ul class="list-disc list-inside space-y-1">
                                            <li>Assurez-vous que le fichier est correct avant de le déposer</li>
                                            <li>Vous pourrez modifier ou supprimer ce livrable plus tard</li>
                                            <li>Le fichier sera visible par tous les membres de l'équipe et l'encadrant</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Boutons -->
                        <div class="flex justify-end space-x-4">
                            <a href="{{ route('projets.livrables.index', $projet) }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                                Annuler
                            </a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                                Déposer le livrable
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Validation de la taille du fichier
        document.getElementById('fichier').addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const maxSize = 10 * 1024 * 1024; // 10MB
                if (file.size > maxSize) {
                    alert('Le fichier est trop volumineux. La taille maximale autorisée est de 10MB.');
                    this.value = '';
                }
            }
        });

        // Mise à jour automatique du nom du livrable basé sur le fichier
        document.getElementById('fichier').addEventListener('change', function() {
            const file = this.files[0];
            const nomLivrable = document.getElementById('nom_livrable');
            
            if (file && !nomLivrable.value) {
                const fileName = file.name.split('.')[0];
                nomLivrable.value = fileName;
            }
        });
    </script>
</x-app-layout>
