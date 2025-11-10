<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Mes Réservations</h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        @if(session('status'))
            <div class="mb-4 text-green-700">{{ session('status') }}</div>
        @endif
        <div class="mb-4">
            <a href="{{ route('reservations.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded">Nouvelle réservation</a>
        </div>
        <div class="bg-white shadow sm:rounded-lg p-4">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-3 py-2 text-left text-sm font-medium">Type</th>
                        <th class="px-3 py-2 text-left text-sm font-medium">Objet</th>
                        <th class="px-3 py-2 text-left text-sm font-medium">Début</th>
                        <th class="px-3 py-2 text-left text-sm font-medium">Fin</th>
                        <th class="px-3 py-2 text-left text-sm font-medium">Statut</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                @foreach($reservations as $reservation)
                    <tr>
                        <td class="px-3 py-2">{{ class_basename($reservation->item_type) }}</td>
                        <td class="px-3 py-2">
                            @php $model = $reservation->item_type::find($reservation->item_id); @endphp
                            @if($model)
                                {{ $model instanceof \App\Models\Salle ? $model->nom_salle : $model->nom_materiel }}
                            @else
                                N/A
                            @endif
                        </td>
                        <td class="px-3 py-2">{{ $reservation->date_debut->format('d/m/Y H:i') }}</td>
                        <td class="px-3 py-2">{{ $reservation->date_fin->format('d/m/Y H:i') }}</td>
                        <td class="px-3 py-2">
                            <span class="px-2 py-1 rounded text-white 
                                {{ $reservation->statut === 'approved' ? 'bg-green-600' : ($reservation->statut === 'rejected' ? 'bg-red-600' : 'bg-yellow-500') }}">
                                {{ ucfirst($reservation->statut) }}
                            </span>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <div class="mt-4">{{ $reservations->links() }}</div>
        </div>
    </div>
</x-app-layout>