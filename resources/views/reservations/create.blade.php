<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Nouvelle Réservation</h2>
    </x-slot>

    <div class="py-6 max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow sm:rounded-lg p-6">
            <form method="POST" action="{{ route('reservations.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium">Type</label>
                    <select name="item_type" class="mt-1 w-full border rounded p-2">
                        <option value="App\\Models\\Salle">Salle</option>
                        <option value="App\\Models\\Materiel">Matériel</option>
                    </select>
                    @error('item_type')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium">Objet</label>
                    <select name="item_id" class="mt-1 w-full border rounded p-2">
                        <optgroup label="Salles">
                            @foreach($salles as $s)
                                <option value="{{ $s->id }}">{{ $s->nom_salle }} (cap. {{ $s->capacite }})</option>
                            @endforeach
                        </optgroup>
                        <optgroup label="Matériels">
                            @foreach($materiels as $m)
                                <option value="{{ $m->id }}">{{ $m->nom_materiel }}</option>
                            @endforeach
                        </optgroup>
                    </select>
                    @error('item_id')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium">Date début</label>
                        <input type="datetime-local" name="date_debut" value="{{ old('date_debut') }}" class="mt-1 w-full border rounded p-2">
                        @error('date_debut')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Date fin</label>
                        <input type="datetime-local" name="date_fin" value="{{ old('date_fin') }}" class="mt-1 w-full border rounded p-2">
                        @error('date_fin')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium">Motif</label>
                    <textarea name="motif" class="mt-1 w-full border rounded p-2">{{ old('motif') }}</textarea>
                    @error('motif')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                </div>
                <div class="flex justify-end space-x-2">
                    <a href="{{ route('reservations.index') }}" class="px-4 py-2 bg-gray-200 rounded">Annuler</a>
                    <button class="px-4 py-2 bg-indigo-600 text-white rounded">Réserver</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>