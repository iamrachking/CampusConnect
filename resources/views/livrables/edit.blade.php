<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Modifier le Livrable') }}
            </h2>
            <a href="{{ route('projets.livrables.index', $livrable->projet) }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
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
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $livrable->projet->titre }}</h3>
                    <p class="text-gray-600">{{ Str::limit($livrable->projet->description, 200) }}</p>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('livrables.update', $livrable) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <!-- Nom du livrable -->
                        <div class="mb-6">
                            <label for="nom_livrable" class="block text-sm font-medium text-gray-700 mb-2">Nom du livrable</label>
                            <input type="text" name="nom_livrable" id="nom_livrable" 
                                   value="{{ old('nom_livrable', $livrable->nom_livrable) }}" 
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                                   placeholder="Ex: Rapport final, Présentation soutenance..." required>
                        </div>

                        <!-- Type de livrable -->
                        <div class="mb-6">
                            <label for="type_livrable" class="block text-sm font-medium text-gray-700 mb-2">Type de livrable</label>
                            <select name="type_livrable" id="type_livrable" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                                <option value="">Sélectionnez un type</option>
                                <option value="Rapport" {{ old('type_livrable', $livrable->type_livrable) === 'Rapport' ? 'selected' : '' }}>Rapport</option>
                                <option value="Présentation" {{ old('type_livrable', $livrable->type_livrable) === 'Présentation' ? 'selected' : '' }}>Présentation</option>
                                <option value="Code source" {{ old('type_livrable', $livrable->type_livrable) === 'Code source' ? 'selected' : '' }}>Code source</option>
                                <option value="Documentation" {{ old('type_livrable', $livrable->type_livrable) === 'Documentation' ? 'selected' : '' }}>Documentation</option>
                                <option value="Autre" {{ old('type_livrable', $livrable->type_livrable) === 'Autre' ? 'selected' : '' }}>Autre</option>
                            </select>
                        </div>

                        <!-- Fichier actuel -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Fichier actuel</label>
                            <div class="bg-gray-50 border border-gray-200 rounded-md p-3">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ basename($livrable->url_livrable) }}</p>
                                        <p class="text-xs text-gray-500">Déposé le {{ $livrable->created_at->format('d/m/Y à H:i') }}</p>
                                    </div>
                                    <a href="{{ route('livrables.download', $livrable) }}" 
                                       class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-1 px-3 rounded text-sm">
                                        Télécharger
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Nouveau fichier -->
                        <div class="mb-6">
                            <label for="fichier" class="block text-sm font-medium text-gray-700 mb-2">Nouveau fichier (optionnel)</label>
                            <input type="file" name="fichier" id="fichier" 
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                                   accept=".pdf,.doc,.docx,.ppt,.pptx,.zip,.rar,.txt,.md">
                            <p class="mt-1 text-sm text-gray-500">
                                Formats acceptés : PDF, DOC, DOCX, PPT, PPTX, ZIP, RAR, TXT, MD (Max: 10MB)
                            </p>
                            <p class="mt-1 text-sm text-gray-500">
                                Si aucun fichier n'est sélectionné, le fichier actuel sera conservé.
                            </p>
                        </div>

                        <!-- Information importante -->
                        <div class="mb-6 bg-yellow-50 border border-yellow-200 rounded-md p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-yellow-800">
                                        Attention
                                    </h3>
                                    <div class="mt-2 text-sm text-yellow-700">
                                        <p>La modification du livrable affectera tous les membres de l'équipe et l'encadrant. Assurez-vous que les changements sont validés.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Boutons -->
                        <div class="flex justify-end space-x-4">
                            <a href="{{ route('projets.livrables.index', $livrable->projet) }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
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
    </script>
</x-app-layout>
