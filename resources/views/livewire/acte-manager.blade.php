<div class="p-6">
    <h2 class="text-2xl font-bold mb-4">Gestion des actes</h2>

    @if (session()->has('message'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('message') }}</div>
    @endif

    <!-- Formulaire d'ajout d'acte -->
    <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
        <h3 class="text-lg font-semibold text-green-800 mb-4">Ajouter un acte</h3>
        <form wire:submit.prevent="save" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
            <div>
                <label for="acteNom" class="block text-sm font-medium text-gray-700 mb-1">Nom de l'acte *</label>
                <input type="text" wire:model.defer="acteNom" id="acteNom" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                       placeholder="Nom de l'acte">
                @error('acteNom') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="montant" class="block text-sm font-medium text-gray-700 mb-1">Prix de référence *</label>
                <input type="number" step="0.01" wire:model.defer="montant" id="montant" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                       placeholder="0.00">
                @error('montant') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="assureurId" class="block text-sm font-medium text-gray-700 mb-1">Assureur *</label>
                <select wire:model.defer="assureurId" id="assureurId" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                    <option value="">Sélectionner un assureur</option>
                    @foreach($assureurs as $assureur)
                        <option value="{{ $assureur->IDAssureur ?? $assureur->ID }}">{{ $assureur->LibAssurance }}</option>
                    @endforeach
                </select>
                @error('assureurId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    Enregistrer
                </button>
            </div>
        </form>
        <!-- Champ nom arabe (optionnel) -->
        <div class="mt-4">
            <label for="acteArab" class="block text-sm font-medium text-gray-700 mb-1">Nom arabe (optionnel)</label>
            <input type="text" wire:model.defer="acteArab" id="acteArab" 
                   class="w-full md:w-1/2 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                   placeholder="Nom arabe de l'acte">
        </div>
    </div>

    <!-- Filtres et recherche -->
    <div class="mb-4 flex flex-col md:flex-row items-center gap-4">
        <div class="flex items-center gap-2">
            <label class="font-semibold text-sm">Filtrer par assureur :</label>
            <select wire:model="selectedAssureur" class="rounded border border-gray-300 px-3 py-2">
                <option value="">Tous</option>
                @foreach($assureurs as $assureur)
                    <option value="{{ $assureur->IDAssureur ?? $assureur->ID }}">{{ $assureur->LibAssurance }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex-1">
            <input type="text" wire:model="search" placeholder="Rechercher un acte..." 
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
        </div>
    </div>

    <!-- Tableau des actes -->
    <div class="mt-2 max-h-[60vh] overflow-y-auto">
        <div class="overflow-x-auto">
            <table class="min-w-full w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acte</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prix de référence</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assureur</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($actes as $acte)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $acte->Acte }}
                                @if($acte->ActeArab)
                                    <div class="text-xs text-gray-500">{{ $acte->ActeArab }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ number_format($acte->PrixRef, 2) }} MRU
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $acte->assureur->LibAssurance ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <button wire:click="openModal({{ $acte->ID }})" 
                                        class="text-blue-600 hover:text-blue-800 mr-3">Modifier</button>
                                <button wire:click="confirmDelete({{ $acte->ID }})" 
                                        class="text-red-600 hover:text-red-800">Supprimer</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-2 text-center text-gray-400">Aucun acte trouvé</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $actes->links() }}
        </div>
    </div>

    <!-- Modal édition (pour modification uniquement) -->
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-8 relative">
                <div class="bg-primary text-white p-4 rounded-t-lg -mt-8 -mx-8 mb-6">
                    <h2 class="text-xl font-bold">Modifier un acte</h2>
                </div>
                <button wire:click="closeModal" class="absolute top-4 right-4 text-gray-500 hover:text-red-600 text-2xl font-bold">&times;</button>
                <form wire:submit.prevent="save">
                    <div class="mb-4">
                        <label for="acteNom" class="block text-sm font-medium text-gray-700">Nom de l'acte *</label>
                        <input type="text" wire:model.defer="acteNom" id="acteNom" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                        @error('acteNom') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-4">
                        <label for="montant" class="block text-sm font-medium text-gray-700">Prix de référence *</label>
                        <input type="number" step="0.01" wire:model.defer="montant" id="montant" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                        @error('montant') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-4">
                        <label for="assureurId" class="block text-sm font-medium text-gray-700">Assureur *</label>
                        <select wire:model.defer="assureurId" id="assureurId" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                            <option value="">Sélectionner un assureur</option>
                            @foreach($assureurs as $assureur)
                                <option value="{{ $assureur->IDAssureur ?? $assureur->ID }}">{{ $assureur->LibAssurance }}</option>
                            @endforeach
                        </select>
                        @error('assureurId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-4">
                        <label for="acteArab" class="block text-sm font-medium text-gray-700">Nom arabe</label>
                        <input type="text" wire:model.defer="acteArab" id="acteArab" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                    </div>
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" wire:click="closeModal" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">Annuler</button>
                        <button type="submit" class="px-4 py-2 bg-primary text-white rounded-md hover:bg-primary-dark">Enregistrer l'acte</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Modal de suppression -->
    @if($showDeleteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-8 relative">
                <div class="text-xl font-bold mb-4">Confirmer la suppression</div>
                <p class="mb-6">Êtes-vous sûr de vouloir supprimer cet acte ? Cette action est irréversible.</p>
                <div class="flex justify-end space-x-3">
                    <button wire:click="deleteActe" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Supprimer</button>
                    <button wire:click="$set('showDeleteModal', false)" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">Annuler</button>
                </div>
            </div>
        </div>
    @endif
</div> 