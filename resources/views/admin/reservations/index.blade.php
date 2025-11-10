<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Réservations en attente</h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        @if(session('status'))
            <span class="badge badge-success mb-4">{{ session('status') }}</span>
        @endif
        <div class="card">
            <div class="card-body">
            <table class="table">
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
                <tbody>
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
                        <td class="px-3 py-2 flex gap-2">
                            <form method="POST" action="{{ route('admin.reservations.approve', $reservation) }}" class="inline">
                                @csrf
                                <button class="btn btn-primary">Valider</button>
                            </form>
                            <form method="POST" action="{{ route('admin.reservations.reject', $reservation) }}" class="inline">
                                @csrf
                                <button class="btn btn-danger">Rejeter</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <div class="mt-4">{{ $pending->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>