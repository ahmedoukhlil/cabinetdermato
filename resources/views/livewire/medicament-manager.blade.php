<div class="p-6">
    <h2 class="text-2xl font-bold mb-4">Gestion des médicaments</h2>

    @if (session()->has('message'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('message') }}</div>
    @endif

    <!-- Formulaire d'ajout de médicament -->
    <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
        <h3 class="text-lg font-semibold text-green-800 mb-4">Ajouter un médicament</h3>
        <form wire:submit.prevent="save" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
            <div>
                <label for="libelleMedic" class="block text-sm font-medium text-gray-700 mb-1">Libellé *</label>
                <input type="text" wire:model.defer="libelleMedic" id="libelleMedic" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                       placeholder="Libellé du médicament">
                @error('libelleMedic') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="fkidtype" class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
                <select wire:model.defer="fkidtype" id="fkidtype" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                    <option value="">Sélectionner un type</option>
                    @foreach($types as $type)
                        <option value="{{ $type['id'] }}">{{ $type['Type'] }}</option>
                    @endforeach
                </select>
                @error('fkidtype') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="prixRef" class="block text-sm font-medium text-gray-700 mb-1">Prix de référence</label>
                <input type="number" step="0.01" wire:model.defer="prixRef" id="prixRef" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                       placeholder="0.00" min="0">
                @error('prixRef') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    Enregistrer
                </button>
            </div>
        </form>
    </div>

    <!-- Filtres et recherche -->
    <div class="mb-4 flex flex-col md:flex-row items-center gap-4">
        <div class="flex items-center gap-2">
            <label class="font-semibold text-sm">Filtrer par type :</label>
            <select wire:model="selectedType" class="rounded border border-gray-300 px-3 py-2">
                <option value="">Tous</option>
                @foreach($types as $type)
                    <option value="{{ $type['id'] }}">{{ $type['Type'] }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex-1">
            <input type="text" wire:model="search" placeholder="Rechercher un médicament..." 
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
        </div>
    </div>

    <!-- Tableau des médicaments -->
    <div class="mt-2 max-h-[60vh] overflow-y-auto">
        <div class="overflow-x-auto">
            <table class="min-w-full w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Libellé</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prix de référence</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($medicaments as $medicament)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $medicament->LibelleMedic }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @php
                                    $typeText = match($medicament->fkidtype) {
                                        1 => 'Médicament',
                                        2 => 'Analyse',
                                        3 => 'Radio',
                                        default => '-'
                                    };
                                @endphp
                                <span class="px-2 py-1 text-xs rounded-full 
                                    @if($medicament->fkidtype == 1) bg-blue-100 text-blue-800
                                    @elseif($medicament->fkidtype == 2) bg-green-100 text-green-800
                                    @elseif($medicament->fkidtype == 3) bg-purple-100 text-purple-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ $typeText }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ number_format($medicament->PrixRef ?? 0, 2) }} MRU
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($medicament->fkidtype == 1)
                                    @if($medicament->quantiteStock !== null)
                                        <div class="flex items-center gap-2">
                                            <span class="px-3 py-1 rounded-full text-xs font-semibold
                                                @if($medicament->stockFaible) bg-red-100 text-red-800
                                                @elseif($medicament->quantiteStock == 0) bg-gray-100 text-gray-800
                                                @else bg-green-100 text-green-800
                                                @endif">
                                                {{ number_format($medicament->quantiteStock, 0) }}
                                            </span>
                                            @if($medicament->stockFaible && $medicament->quantiteStock > 0)
                                                <span class="text-xs text-orange-600" title="Stock faible (seuil: {{ number_format($medicament->quantiteMin, 0) }})">
                                                    <i class="fas fa-exclamation-triangle"></i>
                                                </span>
                                            @elseif($medicament->quantiteStock == 0)
                                                <span class="text-xs text-red-600" title="Rupture de stock">
                                                    <i class="fas fa-times-circle"></i>
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-gray-400 text-xs">Non en stock</span>
                                    @endif
                                @else
                                    <span class="text-gray-400 text-xs">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div class="flex items-center gap-2">
                                    <button wire:click="openModal({{ $medicament->IDMedic }})" 
                                            class="text-blue-600 hover:text-blue-800">Modifier</button>
                                    @if($medicament->fkidtype == 1)
                                        <button wire:click="openStockModal({{ $medicament->IDMedic }})" 
                                                class="text-green-600 hover:text-green-800" 
                                                title="Ajouter du stock">
                                            <i class="fas fa-plus-circle"></i> Stock
                                        </button>
                                    @endif
                                    <button wire:click="confirmDelete({{ $medicament->IDMedic }})" 
                                            class="text-red-600 hover:text-red-800">Supprimer</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-2 text-center text-gray-400">Aucun médicament trouvé</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $medicaments->links() }}
        </div>
    </div>

    <!-- Modal édition -->
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-8 relative">
                <div class="bg-primary text-white p-4 rounded-t-lg -mt-8 -mx-8 mb-6">
                    <h2 class="text-xl font-bold">Modifier un médicament</h2>
                </div>
                <button wire:click="closeModal" class="absolute top-4 right-4 text-gray-500 hover:text-red-600 text-2xl font-bold">&times;</button>
                <form wire:submit.prevent="save">
                    <div class="mb-4">
                        <label for="libelleMedic" class="block text-sm font-medium text-gray-700">Libellé *</label>
                        <input type="text" wire:model.defer="libelleMedic" id="libelleMedic" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                        @error('libelleMedic') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-4">
                        <label for="fkidtype" class="block text-sm font-medium text-gray-700">Type *</label>
                        <select wire:model.defer="fkidtype" id="fkidtype" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                            <option value="">Sélectionner un type</option>
                            @foreach($types as $type)
                                <option value="{{ $type['id'] }}">{{ $type['Type'] }}</option>
                            @endforeach
                        </select>
                        @error('fkidtype') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-4">
                        <label for="prixRef" class="block text-sm font-medium text-gray-700">Prix de référence</label>
                        <input type="number" step="0.01" wire:model.defer="prixRef" id="prixRef" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:border-primary focus:ring-primary" placeholder="0.00" min="0">
                        @error('prixRef') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" wire:click="closeModal" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">Annuler</button>
                        <button type="submit" class="px-4 py-2 bg-primary text-white rounded-md hover:bg-primary-dark">Enregistrer</button>
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
                <p class="mb-6">Êtes-vous sûr de vouloir supprimer ce médicament ? Cette action est irréversible.</p>
                <div class="flex justify-end space-x-3">
                    <button wire:click="deleteMedicament" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Supprimer</button>
                    <button wire:click="$set('showDeleteModal', false)" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">Annuler</button>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal d'ajout de stock -->
    @if($showStockModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl p-8 relative max-h-[90vh] overflow-y-auto">
                <div class="bg-green-600 text-white p-4 rounded-t-lg -mt-8 -mx-8 mb-6">
                    <h2 class="text-xl font-bold">Ajouter du stock</h2>
                </div>
                <button wire:click="closeStockModal" class="absolute top-4 right-4 text-gray-500 hover:text-red-600 text-2xl font-bold">&times;</button>
                <form wire:submit.prevent="saveStock">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Médicament</label>
                            <input type="text" 
                                   value="{{ \App\Models\Medicament::find($stockMedicamentId)->LibelleMedic ?? '' }}" 
                                   disabled
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100">
                        </div>
                        
                        <div>
                            <label for="stockQuantite" class="block text-sm font-medium text-gray-700 mb-1">Quantité *</label>
                            <input type="number" wire:model.defer="stockQuantite" id="stockQuantite" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                                   min="1" required>
                            @error('stockQuantite') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label for="stockPrixAchat" class="block text-sm font-medium text-gray-700 mb-1">Prix d'achat unitaire</label>
                            <input type="number" step="0.01" wire:model.defer="stockPrixAchat" id="stockPrixAchat" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                                   min="0" placeholder="Optionnel">
                            @error('stockPrixAchat') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label for="stockQuantiteMin" class="block text-sm font-medium text-gray-700 mb-1">Seuil minimum *</label>
                            <input type="number" wire:model.defer="stockQuantiteMin" id="stockQuantiteMin" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                                   min="0" required>
                            @error('stockQuantiteMin') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label for="stockNumeroLot" class="block text-sm font-medium text-gray-700 mb-1">Numéro de lot</label>
                            <input type="text" wire:model.defer="stockNumeroLot" id="stockNumeroLot" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                        </div>
                        
                        <div>
                            <label for="stockDateExpiration" class="block text-sm font-medium text-gray-700 mb-1">Date d'expiration</label>
                            <input type="date" wire:model.defer="stockDateExpiration" id="stockDateExpiration" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                        </div>
                        
                        <div>
                            <label for="stockFournisseur" class="block text-sm font-medium text-gray-700 mb-1">Fournisseur</label>
                            <input type="text" wire:model.defer="stockFournisseur" id="stockFournisseur" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                        </div>
                        
                        <div>
                            <label for="stockReferenceFacture" class="block text-sm font-medium text-gray-700 mb-1">Référence facture</label>
                            <input type="text" wire:model.defer="stockReferenceFacture" id="stockReferenceFacture" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                        </div>
                    </div>
                    
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" wire:click="closeStockModal" 
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">Annuler</button>
                        <button type="submit" 
                                class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                            <i class="fas fa-plus mr-2"></i>Ajouter au stock
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>

