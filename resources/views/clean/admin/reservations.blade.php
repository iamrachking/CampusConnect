<x-clean-layout>
    <div class="bg-white/85 backdrop-blur rounded-xl shadow p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Réservations en attente</h2>

        @if(session('status'))
            <div class="mb-4 rounded-md border border-green-300 bg-green-50 text-green-700 p-3">{{ session('status') }}</div>
        @endif

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-600">
                        <th class="py-2 px-3">Demandeur</th>
                        <th class="py-2 px-3">Type</th>
                        <th class="py-2 px-3">Cible</th>
                        <th class="py-2 px-3">Début</th>
                        <th class="py-2 px-3">Fin</th>
                        <th class="py-2 px-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($pending as $reservation)
                    <tr class="border-t border-gray-200">
                        <td class="py-2 px-3">{{ optional($reservation->user)->email }}</td>
                        <td class="py-2 px-3">{{ class_basename($reservation->item_type) }}</td>
                        <td class="py-2 px-3">#{{ $reservation->item_id }}</td>
                        <td class="py-2 px-3">{{ $reservation->date_debut }}</td>
                        <td class="py-2 px-3">{{ $reservation->date_fin }}</td>
                        <td class="py-2 px-3 flex gap-2">
                            <form method="POST" action="{{ route('clean.admin.reservations.approve', $reservation) }}">
                                @csrf
                                <button class="px-3 py-1 rounded bg-indigo-600 text-white hover:bg-indigo-500">Valider</button>
                            </form>
                            <form method="POST" action="{{ route('clean.admin.reservations.reject', $reservation) }}">
                                @csrf
                                <button class="px-3 py-1 rounded bg-red-600 text-white hover:bg-red-500">Refuser</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-4 px-3 text-gray-500">Aucune demande en attente.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $pending->links() }}</div>
    </div>
</x-clean-layout>