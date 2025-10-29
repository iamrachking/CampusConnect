<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Détails de l\'Utilisateur') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Informations de l'utilisateur</h3>

                    <div class="space-y-4">
                        <div>
                            <span class="font-medium text-gray-700">Nom :</span>
                            <span class="ml-2 text-gray-900">{{ $user->getFullNameAttribute() }}</span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700">Email :</span>
                            <span class="ml-2 text-gray-900">{{ $user->email }}</span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700">Rôle :</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ml-2
                                @if($user->role->name === 'Administrateur') bg-red-100 text-red-800
                                @elseif($user->role->name === 'Enseignant') bg-blue-100 text-blue-800
                                @else bg-green-100 text-green-900
                                @endif">
                                {{ $user->role->name }}
                            </span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700">Créé le :</span>
                            <span class="ml-2 text-gray-900">{{ $user->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-4 mt-6">
                        <a href="{{ route('users.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                            Retour
                        </a>
                        <a href="{{ route('users.edit', $user) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded">
                            Modifier
                        </a>
                        @if($user->id !== Auth::id())
                            <form method="POST" action="{{ route('users.destroy', $user) }}" class="inline" id="delete-user-{{ $user->id }}">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded" 
                                    onclick="confirmDelete('delete-user-{{ $user->id }}', '{{ $user->getFullNameAttribute() }}')">
                                    Supprimer
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete(formId, itemName) {
            Swal.fire({
                title: 'Êtes-vous sûr ?',
                text: 'Vous êtes sur le point de supprimer ' + itemName,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Oui, supprimer',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(formId).submit();
                }
            });
        }
    </script>
</x-app-layout>

