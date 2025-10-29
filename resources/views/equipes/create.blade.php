<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Ajouter un Membre') }}
            </h2>
            <a href="{{ route('projets.show', $projet) }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
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

            <!-- Informations du projet -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $projet->titre }}</h3>
                    <p class="text-gray-600">{{ Str::limit($projet->description, 200) }}</p>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('projets.equipes.store', $projet) }}">
                        @csrf
                        
                        <!-- Étudiant -->
                        <div class="mb-6">
                            <label for="user_id" class="block text-sm font-medium text-gray-700 mb-2">Étudiant</label>
                            <select name="user_id" id="user_id" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                                <option value="">Sélectionnez un étudiant</option>
                                @foreach($etudiantsDisponibles as $etudiant)
                                    <option value="{{ $etudiant->id }}" {{ old('user_id') == $etudiant->id ? 'selected' : '' }}>
                                        {{ $etudiant->getFullNameAttribute() }} ({{ $etudiant->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Rôle -->
                        <div class="mb-6">
                            <label for="role_membre" class="block text-sm font-medium text-gray-700 mb-2">Rôle dans l'équipe</label>
                            <select name="role_membre" id="role_membre" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                                <option value="">Sélectionnez un rôle</option>
                                <option value="Membre" {{ old('role_membre') === 'Membre' ? 'selected' : '' }}>Membre</option>
                                <option value="Développeur" {{ old('role_membre') === 'Développeur' ? 'selected' : '' }}>Développeur</option>
                                <option value="Designer" {{ old('role_membre') === 'Designer' ? 'selected' : '' }}>Designer</option>
                                <option value="Testeur" {{ old('role_membre') === 'Testeur' ? 'selected' : '' }}>Testeur</option>
                                <option value="Documentaliste" {{ old('role_membre') === 'Documentaliste' ? 'selected' : '' }}>Documentaliste</option>
                            </select>
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
                                        <p>L'étudiant sélectionné recevra une notification et pourra accepter ou refuser l'invitation à rejoindre l'équipe.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Boutons -->
                        <div class="flex justify-end space-x-4">
                            <a href="{{ route('projets.show', $projet) }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                                Annuler
                            </a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                                Ajouter le membre
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
