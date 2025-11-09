<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
            {{ __('Créer un projet') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md rounded-lg p-8">

                <!-- Message de succès -->
                @if(session('success'))
                    <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Erreurs de validation -->
                @if ($errors->any())
                    <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('projets.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <!-- Titre -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Titre du projet :</label>
                        <input type="text" name="titre" value="{{ old('titre') }}"
                               class="border border-gray-300 rounded-lg px-4 py-2 w-full focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Description :</label>
                        <textarea name="description"
                                  class="border border-gray-300 rounded-lg px-4 py-2 w-full focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                  rows="4">{{ old('description') }}</textarea>
                    </div>

                    <!-- Encadrant -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Encadrant :</label>
                        <select name="encadrant_id"
                                class="border border-gray-300 rounded-lg px-4 py-2 w-full focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <option value="">-- Choisir un encadrant --</option>
                            @foreach($encadrants as $encadrant)
                                <option value="{{ $encadrant->id }}" {{ old('encadrant_id') == $encadrant->id ? 'selected' : '' }}>
                                    {{ $encadrant->nom }} {{ $encadrant->prenom }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Étudiants à inviter -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Inviter des étudiants :</label>
                        <select name="invitations[]" multiple
                                class="border border-gray-300 rounded-lg px-4 py-2 w-full focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            @foreach($etudiants as $etudiant)
                                @if($etudiant->id !== Auth::id())
                                    <option value="{{ $etudiant->id }}">
                                        {{ $etudiant->nom }} {{ $etudiant->prenom }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                        <small class="text-gray-500">Maintenez Ctrl (ou Cmd) pour sélectionner plusieurs étudiants</small>
                    </div>

                    <button type="submit"
                            class="bg-blue-600 text-white font-semibold px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                        Créer le projet
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
