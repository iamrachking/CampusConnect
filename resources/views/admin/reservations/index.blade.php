<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Réservations en attente</h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        @if(session('status'))
            <div class="mb-4 text-green-700">{{ session('status') }}</div>
        @endif
        <div class="bg-white shadow sm:rounded-lg p-4">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-3 py-2 text-left text-sm font-medium">Enseignant</th>
                        <th class="px-3 py-2 text-left text-sm font-medium">Type</th>
                        <th class="px-3 py-2 text-left text-sm font-medium">Objet</th>
                        <th class="px-3 py-2 text-left text-sm font-medium">Début</th>
                        <th class="px-3 py-2 text-left text-sm font-medium">Fin</th>
                        <th class="px-3 py-2 text-left text-sm font-medium">Motif</th>
                        <th class="px-3 py-2 text-left text-sm font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                @foreach($pending as $reservation)
                    <tr>
                        <td class="px-3 py-2">{{ $reservation->user->getFullNameAttribute() }}</td>
                        <td class="px-3 py-2">{{ class_basename($reservation->item_type) }}</td>
                        <td class="px-3 py-2">
                            @php
                                $model = $reservation->item_type::find($reservation->item_id);
                            @endphp
                            @if($model)
                                {{ $model instanceof \App\Models\Salle ? $model->nom_salle : $model->nom_materiel }}
                            @else
                                N/A
                            @endif
                        </td>
                        <td class="px-3 py-2">{{ $reservation->date_debut->format('d/m/Y H:i') }}</td>
                        <td class="px-3 py-2">{{ $reservation->date_fin->format('d/m/Y H:i') }}</td>
                        <td class="px-3 py-2">{{ $reservation->motif }}</td>
                        <td class="px-3 py-2 space-x-2">
                            <form method="POST" action="{{ route('admin.reservations.approve', $reservation) }}" class="inline">
                                @csrf
                                <button class="px-3 py-1 bg-green-600 text-white rounded">Valider</button>
                            </form>
                            <form method="POST" action="{{ route('admin.reservations.reject', $reservation) }}" class="inline">
                                @csrf
                                <button class="px-3 py-1 bg-red-600 text-white rounded">Rejeter</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <div class="mt-4">{{ $pending->links() }}</div>
        </div>
    </div>
</x-app-layout>