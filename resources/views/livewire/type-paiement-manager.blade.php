<div>
    {{-- If you look to others for fulfillment, you will never truly be fulfilled. --}}

    {{-- Formulaire d'ajout/édition de type de paiement --}}
    <div class="mb-6 p-4 bg-blue-50 rounded-lg shadow">
        <h3 class="text-lg font-bold text-blue-700 mb-3">
            {{ $editMode ? 'Modifier le type de paiement' : 'Ajouter un type de paiement' }}
        </h3>
        <form wire:submit.prevent="saveTypePaiement" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Libellé *</label>
                <input type="text" wire:model.defer="LibPaie" class="w-full border border-gray-300 rounded-md px-3 py-2" required>
                @error('LibPaie') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 font-semibold">
                    {{ $editMode ? 'Mettre à jour' : 'Enregistrer' }}
                </button>
                @if($editMode)
                    <button type="button" wire:click="resetForm" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 font-semibold">Annuler</button>
                @endif
            </div>
        </form>
    </div>

    {{-- Tableau des types de paiement --}}
    <div class="bg-white rounded-lg shadow p-4">
        <h3 class="text-lg font-bold text-blue-700 mb-4">Liste des types de paiement</h3>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-blue-50">
                <tr>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Libellé</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($types as $index => $type)
                    <tr>
                        <td class="px-3 py-2">{{ ($types->firstItem() ?? 0) + $index }}</td>
                        <td class="px-3 py-2">{{ $type->LibPaie }}</td>
                        <td class="px-3 py-2 flex gap-2">
                            <button wire:click="editTypePaiement({{ $type->idtypepaie }})" class="px-2 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 text-xs">Modifier</button>
                            <button wire:click="deleteTypePaiement({{ $type->idtypepaie }})" class="px-2 py-1 bg-red-500 text-white rounded hover:bg-red-600 text-xs" onclick="return confirm('Supprimer ce type de paiement ?')">Supprimer</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-3 py-4 text-center text-gray-500">Aucun type de paiement trouvé.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-4">
            {{ $types->links() }}
        </div>
    </div>
</div>
