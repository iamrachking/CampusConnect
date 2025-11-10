<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Disponibilité des Salles</h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow sm:rounded-lg p-6">
            <form method="GET" action="{{ route('availability.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium">Date début</label>
                    <input type="datetime-local" name="date_debut" value="{{ $date_debut }}" class="mt-1 w-full border rounded p-2">
                </div>
                <div>
                    <label class="block text-sm font-medium">Date fin</label>
                    <input type="datetime-local" name="date_fin" value="{{ $date_fin }}" class="mt-1 w-full border rounded p-2">
                </div>
                <div class="flex items-end">
                    <button class="px-4 py-2 bg-indigo-600 text-white rounded">Filtrer</button>
                </div>
            </form>

            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-3 py-2 text-left text-sm font-medium">Salle</th>
                        <th class="px-3 py-2 text-left text-sm font-medium">Capacité</th>
                        <th class="px-3 py-2 text-left text-sm font-medium">Localisation</th>
                        <th class="px-3 py-2 text-left text-sm font-medium">Disponible sur créneau</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                @foreach($salles as $salle)
                    @php
                        $hasReservation = false;
                        if(isset($reservations[$salle->id])) { $hasReservation = true; }
                    @endphp
                    <tr>
                        <td class="px-3 py-2">{{ $salle->nom_salle }}</td>
                        <td class="px-3 py-2">{{ $salle->capacite }}</td>
                        <td class="px-3 py-2">{{ $salle->localisation }}</td>
                        <td class="px-3 py-2">
                            <span class="px-2 py-1 rounded text-white {{ $hasReservation ? 'bg-red-600' : 'bg-green-600' }}">
                                {{ $hasReservation ? 'Réservée' : 'Disponible' }}
                            </span>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>