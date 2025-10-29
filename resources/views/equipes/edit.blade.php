<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Modifier le Rôle') }}
            </h2>
            <a href="{{ route('projets.show', $equipe->projet) }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
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
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $equipe->projet->titre }}</h3>
                    <p class="text-gray-600">{{ Str::limit($equipe->projet->description, 200) }}</p>
                </div>
            </div>

            <!-- Informations du membre -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $equipe->user->name }}</h3>
                    <p class="text-gray-600">{{ $equipe->user->email }}</p>
                    <p class="text-sm text-gray-500 mt-2">Membre depuis le {{ $equipe->created_at->format('d/m/Y') }}</p>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('equipes.update', $equipe) }}">
                        @csrf
                        @method('PUT')
                        
                        <!-- Rôle -->
                        <div class="mb-6">
                            <label for="role_membre" class="block text-sm font-medium text-gray-700 mb-2">Rôle dans l'équipe</label>
                            <select name="role_membre" id="role_membre" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                                <option value="">Sélectionnez un rôle</option>
                                <option value="Membre" {{ old('role_membre', $equipe->role_membre) === 'Membre' ? 'selected' : '' }}>Membre</option>
                                <option value="Développeur" {{ old('role_membre', $equipe->role_membre) === 'Développeur' ? 'selected' : '' }}>Développeur</option>
                                <option value="Designer" {{ old('role_membre', $equipe->role_membre) === 'Designer' ? 'selected' : '' }}>Designer</option>
                                <option value="Testeur" {{ old('role_membre', $equipe->role_membre) === 'Testeur' ? 'selected' : '' }}>Testeur</option>
                                <option value="Documentaliste" {{ old('role_membre', $equipe->role_membre) === 'Documentaliste' ? 'selected' : '' }}>Documentaliste</option>
                            </select>
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
                                        <p>La modification du rôle affectera les permissions de ce membre dans l'équipe. Assurez-vous que le changement est validé.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Boutons -->
                        <div class="flex justify-end space-x-4">
                            <a href="{{ route('projets.show', $equipe->projet) }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
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
