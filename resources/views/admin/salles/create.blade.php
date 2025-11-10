<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Ajouter une Salle</h2>
    </x-slot>

    <div class="py-6 max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow sm:rounded-lg p-6">
            <form method="POST" action="{{ route('admin.salles.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium">Nom de la salle</label>
                    <input type="text" name="nom_salle" value="{{ old('nom_salle') }}" class="mt-1 w-full border rounded p-2">
                    @error('nom_salle')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium">Capacité</label>
                    <input type="number" name="capacite" value="{{ old('capacite') }}" class="mt-1 w-full border rounded p-2">
                    @error('capacite')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium">Localisation</label>
                    <textarea name="localisation" class="mt-1 w-full border rounded p-2">{{ old('localisation') }}</textarea>
                    @error('localisation')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                </div>
                <div class="flex items-center space-x-2">
                    <input type="checkbox" name="disponible" id="disponible" {{ old('disponible') ? 'checked' : '' }}>
                    <label for="disponible">Disponible</label>
                </div>
                <div class="flex justify-end space-x-2">
                    <a href="{{ route('admin.salles.index') }}" class="px-4 py-2 bg-gray-200 rounded">Annuler</a>
                    <button class="px-4 py-2 bg-indigo-600 text-white rounded">Créer</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>