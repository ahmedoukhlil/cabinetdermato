<div class="p-4 sm:p-6">
    <div class="mb-6">
        <h2 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-2">
            <i class="fas fa-pills text-primary mr-2"></i>Gestion de la Pharmacie
        </h2>
        <p class="text-gray-600">Gestion du stock de médicaments dermatologiques</p>
    </div>

    @if (session()->has('message'))
        <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded">
            <i class="fas fa-check-circle mr-2"></i>{{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded">
            <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
        </div>
    @endif

    {{-- Alertes --}}
    @if($alertesStockFaible > 0 || $alertesExpires > 0 || $alertesExpireBientot > 0)
    <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
        @if($alertesStockFaible > 0)
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded">
            <div class="flex items-center">
                <i class="fas fa-exclamation-triangle text-yellow-600 text-2xl mr-3"></i>
                <div>
                    <p class="font-semibold text-yellow-800">{{ $alertesStockFaible }} médicament(s) en stock faible</p>
                    <p class="text-sm text-yellow-600">Quantité inférieure au seuil minimum</p>
                </div>
            </div>
        </div>
        @endif

        @if($alertesExpires > 0)
        <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded">
            <div class="flex items-center">
                <i class="fas fa-times-circle text-red-600 text-2xl mr-3"></i>
                <div>
                    <p class="font-semibold text-red-800">{{ $alertesExpires }} lot(s) expiré(s)</p>
                    <p class="text-sm text-red-600">Date d'expiration dépassée</p>
                </div>
            </div>
        </div>
        @endif

        @if($alertesExpireBientot > 0)
        <div class="bg-orange-50 border-l-4 border-orange-400 p-4 rounded">
            <div class="flex items-center">
                <i class="fas fa-clock text-orange-600 text-2xl mr-3"></i>
                <div>
                    <p class="font-semibold text-orange-800">{{ $alertesExpireBientot }} lot(s) expire(nt) bientôt</p>
                    <p class="text-sm text-orange-600">Dans les 30 prochains jours</p>
                </div>
            </div>
        </div>
        @endif
    </div>
    @endif

    {{-- Onglets --}}
    <div class="mb-6 border-b border-gray-200">
        <nav class="flex space-x-4 overflow-x-auto">
            <button wire:click="$set('activeTab', 'stock')" 
                    class="px-4 py-3 border-b-2 font-medium text-sm whitespace-nowrap transition-colors {{ $activeTab === 'stock' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                <i class="fas fa-boxes mr-2"></i>Stock actuel
            </button>
            <button wire:click="$set('activeTab', 'entree')" 
                    class="px-4 py-3 border-b-2 font-medium text-sm whitespace-nowrap transition-colors {{ $activeTab === 'entree' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                <i class="fas fa-arrow-down mr-2"></i>Entrées de stock
            </button>
            <button wire:click="$set('activeTab', 'vente')" 
                    class="px-4 py-3 border-b-2 font-medium text-sm whitespace-nowrap transition-colors {{ $activeTab === 'vente' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                <i class="fas fa-shopping-cart mr-2"></i>Vente
            </button>
            <button wire:click="$set('activeTab', 'historique')" 
                    class="px-4 py-3 border-b-2 font-medium text-sm whitespace-nowrap transition-colors {{ $activeTab === 'historique' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                <i class="fas fa-history mr-2"></i>Historique
            </button>
        </nav>
    </div>

    {{-- Contenu des onglets --}}

    {{-- ONGLET STOCK ACTUEL --}}
    @if($activeTab === 'stock')
    <div>
        {{-- Filtres --}}
        <div class="mb-4 flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <input type="text" wire:model="searchStock" placeholder="Rechercher un médicament..." 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
            </div>
            <div>
                <select wire:model="filterStock" class="w-full md:w-auto px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                    <option value="tous">Tous</option>
                    <option value="faible">Stock faible</option>
                    <option value="expires">Expirés</option>
                    <option value="expire_bientot">Expire bientôt</option>
                </select>
            </div>
        </div>

        {{-- Tableau des stocks --}}
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Médicament</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Seuil min</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prix achat</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prix vente</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($stocks as $stock)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $stock->medicament->LibelleMedic ?? 'N/A' }}</div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span class="text-sm font-semibold {{ $stock->isStockFaible() ? 'text-red-600' : 'text-gray-900' }}">
                                    {{ number_format($stock->quantiteStock, 0) }}
                                </span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ number_format($stock->quantiteMin, 0) }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ number_format($stock->prixAchat, 0) }} MRU
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ number_format($stock->prixVente, 0) }} MRU
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                @if($stock->isStockFaible())
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>Faible
                                    </span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i>OK
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-400">
                                <i class="fas fa-inbox text-4xl mb-2"></i>
                                <p>Aucun stock trouvé</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3 bg-gray-50 border-t border-gray-200">
                {{ $stocks->links() }}
            </div>
        </div>
    </div>
    @endif

    {{-- ONGLET ENTRÉES DE STOCK --}}
    @if($activeTab === 'entree')
    <div>
        <div class="mb-4">
            <button wire:click="openEntreeModal" 
                    class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors">
                <i class="fas fa-plus mr-2"></i>Nouvelle entrée de stock
            </button>
        </div>

        {{-- Modal d'entrée --}}
        @if($showEntreeModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                <div class="bg-primary text-white p-4 rounded-t-lg flex justify-between items-center">
                    <h3 class="text-xl font-bold">Nouvelle entrée de stock</h3>
                    <button wire:click="closeEntreeModal" class="text-white hover:text-gray-200 text-2xl">&times;</button>
                </div>
                <div class="p-6">
                    <form wire:submit.prevent="enregistrerEntree">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Médicament *</label>
                                <select wire:model="entreeMedicamentId" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                                    <option value="">Sélectionner un médicament</option>
                                    @foreach($medicaments as $medicament)
                                        <option value="{{ $medicament->IDMedic }}">{{ $medicament->LibelleMedic }}</option>
                                    @endforeach
                                </select>
                                @error('entreeMedicamentId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Quantité *</label>
                                <input type="number" step="1" wire:model="entreeQuantite" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary" min="1">
                                @error('entreeQuantite') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Prix d'achat unitaire *</label>
                                <input type="number" step="1" wire:model="entreePrixAchat" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary" min="0">
                                @error('entreePrixAchat') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Numéro de lot</label>
                                <input type="text" wire:model="entreeNumeroLot" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Date d'expiration</label>
                                <input type="date" wire:model="entreeDateExpiration" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Fournisseur</label>
                                <input type="text" wire:model="entreeFournisseur" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Référence facture</label>
                                <input type="text" wire:model="entreeReferenceFacture" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                                <textarea wire:model="entreeNotes" rows="3" 
                                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"></textarea>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end space-x-3">
                            <button type="button" wire:click="closeEntreeModal" 
                                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                                Annuler
                            </button>
                            <button type="submit" 
                                    class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark">
                                <i class="fas fa-save mr-2"></i>Enregistrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif
    </div>
    @endif

    {{-- ONGLET VENTE --}}
    @if($activeTab === 'vente')
    <div>
        @if(!$patientId)
        <div class="mb-4 p-4 bg-yellow-50 border-l-4 border-yellow-400 rounded">
            <p class="text-yellow-800">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <strong>Attention :</strong> Aucun patient n'est sélectionné. Veuillez sélectionner un patient pour pouvoir créer une facture.
            </p>
        </div>
        @else
        @php
            $patient = \App\Models\Patient::find($patientId);
        @endphp
        <div class="mb-4 p-4 bg-green-50 border-l-4 border-green-400 rounded">
            <p class="text-green-800">
                <i class="fas fa-user-check mr-2"></i>
                <strong>Patient sélectionné :</strong> {{ $patient ? trim($patient->Nom . ' ' . $patient->Prenom) : 'N/A' }}
            </p>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Liste des médicaments en stock --}}
            <div>
                <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
                    <i class="fas fa-boxes text-primary"></i>
                    Médicaments disponibles
                </h3>
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="p-4 border-b">
                        <input type="text" wire:model="searchStock" placeholder="Rechercher un médicament..." 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                    </div>
                    <div class="max-h-96 overflow-y-auto">
                        @forelse($stocks as $stock)
                            @if($stock->quantiteStock > 0)
                            <div class="p-4 border-b border-gray-100 hover:bg-gray-50 flex items-center justify-between">
                                <div class="flex-1">
                                    <div class="font-medium text-gray-900">{{ $stock->medicament->LibelleMedic ?? 'N/A' }}</div>
                                    <div class="text-sm text-gray-500 mt-1">
                                        Stock: <span class="font-semibold {{ $stock->quantiteStock <= $stock->quantiteMin ? 'text-red-600' : 'text-gray-700' }}">{{ number_format($stock->quantiteStock, 0) }}</span>
                                        @php
                                            $prix = $stock->prixVente > 0 ? $stock->prixVente : ($stock->medicament->PrixRef ?? 0);
                                        @endphp
                                        @if($prix > 0)
                                            | Prix: <span class="font-semibold">{{ number_format($prix, 0) }} MRU</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 ml-4">
                                    <input type="number" 
                                           wire:model.defer="quantiteVente.{{ $stock->fkidMedicament }}" 
                                           value="1" min="1" step="1" 
                                           class="w-20 px-2 py-1 border border-gray-300 rounded text-sm"
                                           max="{{ $stock->quantiteStock }}">
                                    <button wire:click="ajouterAuPanierVente({{ $stock->fkidMedicament }}, {{ $quantiteVente[$stock->fkidMedicament] ?? 1 }})" 
                                            @if(!$patientId) disabled @endif
                                            class="px-3 py-1 bg-primary text-white rounded hover:bg-primary-dark text-sm disabled:bg-gray-300 disabled:cursor-not-allowed">
                                        <i class="fas fa-plus mr-1"></i>Ajouter
                                    </button>
                                </div>
                            </div>
                            @endif
                        @empty
                        <div class="p-8 text-center text-gray-400">
                            <i class="fas fa-box-open text-4xl mb-2"></i>
                            <p>Aucun médicament en stock</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Panier --}}
            <div>
                <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
                    <i class="fas fa-shopping-cart text-primary"></i>
                    Panier
                </h3>
                <div class="bg-white rounded-lg shadow">
                    @if(count($panierVente) > 0)
                    <div class="divide-y divide-gray-200">
                        @foreach($panierVente as $index => $item)
                        <div class="p-4 hover:bg-gray-50">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="font-medium text-gray-900">{{ $item['libelle'] }}</div>
                                    <div class="text-sm text-gray-500 mt-1">
                                        Stock disponible: {{ number_format($item['stockDisponible'], 0) }}
                                    </div>
                                    <div class="mt-2 space-y-2">
                                        <div class="flex items-center gap-2">
                                            <label class="text-sm text-gray-600">Quantité:</label>
                                            <input type="number" 
                                                   wire:change="modifierQuantitePanier({{ $index }}, $event.target.value)"
                                                   value="{{ $item['quantite'] }}" 
                                                   min="1" step="1" 
                                                   max="{{ $item['stockDisponible'] }}"
                                                   class="w-24 px-2 py-1 border border-gray-300 rounded text-sm">
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <label class="text-sm text-gray-600">Prix Ref:</label>
                                            <span class="text-sm text-gray-500">{{ number_format($item['prixRef'] ?? $item['prixUnitaire'] ?? 0, 0) }} MRU</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <label class="text-sm text-gray-600">Prix Facturé:</label>
                                            <input type="number" 
                                                   wire:change="modifierPrixFacturePanier({{ $index }}, $event.target.value)"
                                                   value="{{ $item['prixFacture'] ?? $item['prixRef'] ?? $item['prixUnitaire'] ?? 0 }}" 
                                                   min="0" step="1"
                                                   class="w-32 px-2 py-1 border border-gray-300 rounded text-sm">
                                            <span class="text-sm text-gray-500">MRU</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="ml-4 text-right">
                                    <div class="font-semibold text-gray-900">{{ number_format($item['montant'], 0) }} MRU</div>
                                    <button wire:click="retirerDuPanierVente({{ $index }})" 
                                            class="mt-2 text-red-600 hover:text-red-800 text-sm">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="p-4 bg-gray-50 border-t">
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-lg font-semibold">Total:</span>
                            <span class="text-xl font-bold text-primary">{{ number_format($this->totalPanier, 0) }} MRU</span>
                        </div>
                        <button wire:click="creerFacture" 
                                @if(!$patientId) disabled @endif
                                class="w-full px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark disabled:bg-gray-300 disabled:cursor-not-allowed">
                            <i class="fas fa-file-invoice-dollar mr-2"></i>Créer la facture
                        </button>
                    </div>
                    @else
                    <div class="p-8 text-center text-gray-400">
                        <i class="fas fa-shopping-cart text-4xl mb-2"></i>
                        <p>Le panier est vide</p>
                        <p class="text-sm mt-2">Sélectionnez des médicaments pour les ajouter au panier</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Modal de confirmation de facture créée --}}
        @if($showFactureModal && $factureVenteId)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4 mt-16">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
                <div class="p-6">
                    <div class="flex items-center justify-center w-12 h-12 mx-auto bg-green-100 rounded-full mb-4">
                        <i class="fas fa-check text-green-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-center mb-2">Facture créée avec succès</h3>
                    <p class="text-gray-600 text-center mb-4">
                        La facture a été créée. Le stock sera déduit lors du paiement complet de la facture.
                    </p>
                    <div class="flex gap-3">
                        <button wire:click="fermerFactureModal" 
                                class="flex-1 px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                            Fermer
                        </button>
                        <button wire:click="$emit('ouvrirFacturationDepuisPharmacie')" 
                                class="flex-1 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark">
                            <i class="fas fa-file-invoice-dollar mr-2"></i>Voir la facture
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
    @endif

    {{-- ONGLET HISTORIQUE --}}
    @if($activeTab === 'historique')
    <div>
        {{-- Filtres --}}
        <div class="mb-4 grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <input type="text" wire:model="searchHistorique" placeholder="Rechercher..." 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
            </div>
            <div>
                <select wire:model="filterTypeMouvement" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                    <option value="">Tous les types</option>
                    <option value="ENTREE">Entrées</option>
                    <option value="SORTIE">Sorties</option>
                    <option value="AJUSTEMENT">Ajustements</option>
                </select>
            </div>
            <div>
                <input type="date" wire:model="filterDateDebut" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
            </div>
            <div>
                <input type="date" wire:model="filterDateFin" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
            </div>
        </div>

        {{-- Tableau historique --}}
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Médicament</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantité</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prix unitaire</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Montant</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Utilisateur</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($mouvements as $mouvement)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $mouvement->dateMouvement ? \Carbon\Carbon::parse($mouvement->dateMouvement)->format('d/m/Y H:i') : '-' }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $mouvement->medicament->LibelleMedic ?? 'N/A' }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                @if($mouvement->typeMouvement === 'ENTREE')
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        <i class="fas fa-arrow-down mr-1"></i>Entrée
                                    </span>
                                @elseif($mouvement->typeMouvement === 'SORTIE')
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                        <i class="fas fa-arrow-up mr-1"></i>Sortie
                                    </span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-adjust mr-1"></i>Ajustement
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ number_format(abs($mouvement->quantite), 0) }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ number_format($mouvement->prixUnitaire, 0) }} MRU
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                {{ number_format($mouvement->montantTotal, 0) }} MRU
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $mouvement->user->NomComplet ?? 'N/A' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-400">
                                <i class="fas fa-inbox text-4xl mb-2"></i>
                                <p>Aucun mouvement trouvé</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3 bg-gray-50 border-t border-gray-200">
                {{ $mouvements->links() }}
            </div>
        </div>
    </div>
    @endif
</div>
