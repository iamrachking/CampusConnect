<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Gestion des Salles
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-6 flex items-center justify-between">
            <a href="{{ route('admin.salles.create') }}" class="btn btn-primary">Ajouter une salle</a>
            @if(session('status'))
                <span class="badge badge-success">{{ session('status') }}</span>
            @endif
        </div>

        <div class="card">
            <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th class="px-3 py-2 text-left text-sm font-medium">Nom</th>
                        <th class="px-3 py-2 text-left text-sm font-medium">Capacité</th>
                        <th class="px-3 py-2 text-left text-sm font-medium">Localisation</th>
                        <th class="px-3 py-2 text-left text-sm font-medium">Disponible</th>
                        <th class="px-3 py-2 text-left text-sm font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($salles as $salle)
                    <tr>
                        <td class="px-3 py-2">{{ $salle->nom_salle }}</td>
                        <td class="px-3 py-2">{{ $salle->capacite }}</td>
                        <td class="px-3 py-2">{{ $salle->localisation }}</td>
                        <td class="px-3 py-2">
                            <span class="badge {{ $salle->disponible ? 'badge-success' : 'badge-danger' }}">
                                {{ $salle->disponible ? 'Oui' : 'Non' }}
                            </span>
                        </td>
                        <td class="px-3 py-2 flex gap-2">
                            <a href="{{ route('admin.salles.edit', $salle) }}" class="btn btn-secondary">Modifier</a>
                            <form method="POST" action="{{ route('admin.salles.toggle', $salle) }}" class="inline">
                                @csrf
                                <button class="btn btn-primary">Basculer</button>
                            </form>
                            <form method="POST" action="{{ route('admin.salles.destroy', $salle) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <div class="mt-4">{{ $salles->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>