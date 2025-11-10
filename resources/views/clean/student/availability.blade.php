<x-clean-layout>
    <div class="bg-white/85 backdrop-blur rounded-xl shadow p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Disponibilité rapide</h2>
        <p class="text-gray-600 mb-6">Consultez les salles et matériels disponibles, puis faites une demande.</p>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div>
                <h3 class="text-lg font-semibold mb-3">Salles</h3>
                <div class="space-y-2">
                    @forelse($salles as $s)
                        <div class="flex items-center justify-between p-3 border rounded">
                            <div>
                                <div class="font-medium">{{ $s->nom_salle }}</div>
                                <div class="text-sm text-gray-600">Capacité: {{ $s->capacite }} • {{ $s->localisation }}</div>
                            </div>
                            <span class="px-2 py-1 rounded text-white {{ $s->disponible ? 'bg-green-600' : 'bg-red-600' }}">{{ $s->disponible ? 'Disponible' : 'Indisponible' }}</span>
                        </div>
                    @empty
                        <div class="text-gray-500">Aucune salle.</div>
                    @endforelse
                </div>
            </div>

            <div>
                <h3 class="text-lg font-semibold mb-3">Matériels</h3>
                <div class="space-y-2">
                    @forelse($materiels as $m)
                        <div class="flex items-center justify-between p-3 border rounded">
                            <div class="font-medium">{{ $m->nom_materiel }}</div>
                            <span class="px-2 py-1 rounded text-white {{ $m->disponible ? 'bg-green-600' : 'bg-red-600' }}">{{ $m->disponible ? 'Disponible' : 'Indisponible' }}</span>
                        </div>
                    @empty
                        <div class="text-gray-500">Aucun matériel.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="mt-6">
            <a href="{{ route('clean.student.create') }}" class="px-4 py-2 rounded bg-indigo-600 text-white hover:bg-indigo-500">Demande de réservation</a>
        </div>
    </div>
</x-clean-layout>