<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Disponibilité des Salles</h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="card">
            <div class="card-body">
            <form method="GET" action="{{ route('availability.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium">Date début</label>
                    <input type="datetime-local" name="date_debut" value="{{ $date_debut }}" class="mt-1 input">
                </div>
                <div>
                    <label class="block text-sm font-medium">Date fin</label>
                    <input type="datetime-local" name="date_fin" value="{{ $date_fin }}" class="mt-1 input">
                </div>
                <div class="flex items-end">
                    <button class="btn btn-primary">Filtrer</button>
                </div>
            </form>

            <table class="table">
                <thead>
                    <tr>
                        <th class="px-3 py-2 text-left text-sm font-medium">Salle</th>
                        <th class="px-3 py-2 text-left text-sm font-medium">Capacité</th>
                        <th class="px-3 py-2 text-left text-sm font-medium">Localisation</th>
                        <th class="px-3 py-2 text-left text-sm font-medium">Disponible sur créneau</th>
                    </tr>
                </thead>
                <tbody>
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
                            <span class="badge {{ $hasReservation ? 'badge-danger' : 'badge-success' }}">
                                {{ $hasReservation ? 'Réservée' : 'Disponible' }}
                            </span>
                        </td>
                </tr>
                @endforeach
                </tbody>
            </table>
            </div>
        </div>
    </div>
</x-app-layout>