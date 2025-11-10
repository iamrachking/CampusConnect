<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Demande de Réservation (Étudiant)</h2>
    </x-slot>

    <div class="py-6 max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="card">
            <form method="POST" action="{{ route('student.reservations.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="form-label">Type</label>
                    <select name="item_type" class="input">
                        <option value="App\\Models\\Salle">Salle</option>
                        <option value="App\\Models\\Materiel">Matériel</option>
                    </select>
                    @error('item_type')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="form-label">Objet</label>
                    <select name="item_id" class="input">
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
                        <label class="form-label">Date début</label>
                        <input type="datetime-local" name="date_debut" value="{{ old('date_debut') }}" class="input">
                        @error('date_debut')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label class="form-label">Date fin</label>
                        <input type="datetime-local" name="date_fin" value="{{ old('date_fin') }}" class="input">
                        @error('date_fin')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div>
                    <label class="form-label">Motif</label>
                    <textarea name="motif" class="input">{{ old('motif') }}</textarea>
                    @error('motif')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                </div>
                <div class="flex justify-end gap-2">
                    <a href="{{ route('availability.index') }}" class="btn-secondary">Annuler</a>
                    <button class="btn-primary">Envoyer la demande</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>