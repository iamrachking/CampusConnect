<x-clean-layout>
    <div class="bg-white/85 backdrop-blur rounded-xl shadow p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Demande de réservation</h2>

        <form method="POST" action="{{ route('clean.student.store') }}" class="space-y-4">
            @csrf
            @if ($errors->any())
                <div class="rounded-md border border-red-300 bg-red-50 text-red-700 p-3">
                    Veuillez corriger les erreurs ci-dessous.
                </div>
            @endif

            <div>
                <label class="block text-sm font-medium">Type</label>
                <select name="item_type" class="mt-1 w-full border rounded p-2">
                    <option value="salle" @selected(old('item_type')==='salle')>Salle</option>
                    <option value="materiel" @selected(old('item_type')==='materiel')>Matériel</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium">Cible (Salle ou Matériel)</label>
                <select name="item_id" class="mt-1 w-full border rounded p-2">
                    <optgroup label="Salles">
                        @foreach($salles as $s)
                            <option value="{{ $s->id }}">Salle: {{ $s->nom_salle }} ({{ $s->capacite }} places)</option>
                        @endforeach
                    </optgroup>
                    <optgroup label="Matériels">
                        @foreach($materiels as $m)
                            <option value="{{ $m->id }}">Matériel: {{ $m->nom_materiel }}</option>
                        @endforeach
                    </optgroup>
                </select>
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
                <label class="block text-sm font-medium">Motif (optionnel)</label>
                <input type="text" name="motif" value="{{ old('motif') }}" class="mt-1 w-full border rounded p-2">
            </div>

            <div class="flex gap-3">
                <button class="px-4 py-2 rounded bg-indigo-600 text-white hover:bg-indigo-500">Envoyer la demande</button>
                <a href="{{ route('clean.student.availability') }}" class="px-4 py-2 rounded bg-gray-200 text-gray-900 hover:bg-gray-300">Retour</a>
            </div>
        </form>
    </div>
</x-clean-layout>