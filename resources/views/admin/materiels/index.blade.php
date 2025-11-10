<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Gestion des Matériels
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-6 flex items-center justify-between">
            <a href="{{ route('admin.materiels.create') }}" class="btn btn-primary">Ajouter un matériel</a>
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
                        <th class="px-3 py-2 text-left text-sm font-medium">Disponible</th>
                        <th class="px-3 py-2 text-left text-sm font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($materiels as $materiel)
                    <tr>
                        <td class="px-3 py-2">{{ $materiel->nom_materiel }}</td>
                        <td class="px-3 py-2">
                            <span class="badge {{ $materiel->disponible ? 'badge-success' : 'badge-danger' }}">
                                {{ $materiel->disponible ? 'Oui' : 'Non' }}
                            </span>
                        </td>
                        <td class="px-3 py-2 flex gap-2">
                            <a href="{{ route('admin.materiels.edit', $materiel) }}" class="btn btn-secondary">Modifier</a>
                            <form method="POST" action="{{ route('admin.materiels.toggle', $materiel) }}" class="inline">
                                @csrf
                                <button class="btn btn-primary">Basculer</button>
                            </form>
                            <form method="POST" action="{{ route('admin.materiels.destroy', $materiel) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <div class="mt-4">{{ $materiels->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>