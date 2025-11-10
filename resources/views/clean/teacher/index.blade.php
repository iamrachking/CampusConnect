<x-clean-layout>
    <div class="bg-white/85 backdrop-blur rounded-xl shadow p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Mes réservations</h2>

        @if(session('status'))
            <div class="mb-4 rounded-md border border-green-300 bg-green-50 text-green-700 p-3">{{ session('status') }}</div>
        @endif

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-600">
                        <th class="py-2 px-3">Type</th>
                        <th class="py-2 px-3">Cible</th>
                        <th class="py-2 px-3">Début</th>
                        <th class="py-2 px-3">Fin</th>
                        <th class="py-2 px-3">Statut</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($reservations as $reservation)
                    <tr class="border-t border-gray-200">
                        <td class="py-2 px-3">{{ class_basename($reservation->item_type) }}</td>
                        <td class="py-2 px-3">#{{ $reservation->item_id }}</td>
                        <td class="py-2 px-3">{{ $reservation->date_debut }}</td>
                        <td class="py-2 px-3">{{ $reservation->date_fin }}</td>
                        <td class="py-2 px-3">
                            <span class="px-2 py-1 rounded text-white {{ $reservation->statut === 'approved' ? 'bg-green-600' : ($reservation->statut === 'rejected' ? 'bg-red-600' : 'bg-yellow-500') }}">
                                {{ $reservation->statut }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-4 px-3 text-gray-500">Aucune réservation.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $reservations->links() }}</div>

        <div class="mt-6">
            <a href="{{ route('clean.teacher.create') }}" class="px-4 py-2 rounded bg-indigo-600 text-white hover:bg-indigo-500">Nouvelle demande</a>
        </div>
    </div>
</x-clean-layout>