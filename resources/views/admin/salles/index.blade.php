<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Gestion des Salles
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-4 flex justify-between">
            <a href="{{ route('admin.salles.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded">Ajouter une salle</a>
            @if(session('status'))
                <div class="text-green-700">{{ session('status') }}</div>
            @endif
        </div>

        <div class="bg-white shadow sm:rounded-lg p-4">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-3 py-2 text-left text-sm font-medium">Nom</th>
                        <th class="px-3 py-2 text-left text-sm font-medium">Capacité</th>
                        <th class="px-3 py-2 text-left text-sm font-medium">Localisation</th>
                        <th class="px-3 py-2 text-left text-sm font-medium">Disponible</th>
                        <th class="px-3 py-2 text-left text-sm font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                @foreach($salles as $salle)
                    <tr>
                        <td class="px-3 py-2">{{ $salle->nom_salle }}</td>
                        <td class="px-3 py-2">{{ $salle->capacite }}</td>
                        <td class="px-3 py-2">{{ $salle->localisation }}</td>
                        <td class="px-3 py-2">
                            <span class="px-2 py-1 rounded text-white {{ $salle->disponible ? 'bg-green-600' : 'bg-red-600' }}">
                                {{ $salle->disponible ? 'Oui' : 'Non' }}
                            </span>
                        </td>
                        <td class="px-3 py-2 space-x-2">
                            <a href="{{ route('admin.salles.edit', $salle) }}" class="px-3 py-1 bg-yellow-500 text-white rounded">Modifier</a>
                            <form method="POST" action="{{ route('admin.salles.toggle', $salle) }}" class="inline">
                                @csrf
                                <button class="px-3 py-1 bg-blue-600 text-white rounded">Basculer</button>
                            </form>
                            <form method="POST" action="{{ route('admin.salles.destroy', $salle) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button class="px-3 py-1 bg-red-600 text-white rounded">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <div class="mt-4">{{ $salles->links() }}</div>
        </div>
    </div>
</x-app-layout>