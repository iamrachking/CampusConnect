<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
            {{ __('Invitations aux projets') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md rounded-lg p-8">
                @if(session('success'))
                    <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                        {{ session('success') }}
                    </div>
                @endif
                @if($invitations->isEmpty())
                    <p class="text-gray-600">Vous n'avez aucune invitation pour le moment.</p>
                @else
                    <ul class="space-y-4">
                        @foreach($invitations as $invitation)
                            <li class="border border-gray-300 rounded-lg p-4 flex justify-between items-center
                                       {{ $invitation->is_read ? '' : 'bg-blue-50' }}">
                                <div>
                                    <strong>{{ $invitation->expediteur->nom }} {{ $invitation->expediteur->prenom }}</strong>
                                    vous invite à rejoindre le projet
                                    <strong>{{ $invitation->projet->titre }}</strong>.
                                    <div class="text-sm text-gray-500">Statut: {{ ucfirst($invitation->statut) }}</div>
                                </div>
                                @if($invitation->statut === 'pending')
                                    <form action="{{ route('invitations.respond', $invitation) }}" method="POST" class="inline-flex gap-3">
                                        @csrf
                                        <button type="submit" name="action" value="accept"
                                                style="background-color: #10b981; color: white;"
                                                class="font-semibold py-2 px-4 rounded hover:opacity-90">
                                            Accepter
                                        </button>
                                        <button type="submit" name="action" value="decline"
                                                class="bg-red-600 text-white font-semibold py-2 px-4 rounded hover:bg-red-700">
                                            Refuser
                                        </button>
                                    </form>
                                @else
                                    <span class="text-gray-500 italic">{{ ucfirst($invitation->statut) }}</span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>