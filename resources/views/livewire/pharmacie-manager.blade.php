<div class="p-4 sm:p-6">
    <div class="mb-6">
        <h2 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-2">
            <i class="fas fa-pills text-primary mr-2"></i>Gestion de la Pharmacie
        </h2>
        <p class="text-gray-600">Gestion du stock de médicaments dermatologiques</p>
    </div>

    {{-- Notifications Toast fixes et visibles --}}
    @if (session()->has('message'))
        <div x-data="{ show: true }" 
             x-show="show" 
             x-init="setTimeout(() => show = false, 8000)"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-y-2"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform translate-y-0"
             x-transition:leave-end="opacity-0 transform translate-y-2"
             class="fixed top-4 right-4 z-50 max-w-md w-full mx-4 bg-green-50 border-l-4 border-green-500 shadow-2xl rounded-lg p-4"
             role="alert">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-500 text-2xl"></i>
                </div>
                <div class="ml-3 flex-1">
                    <p class="text-sm font-semibold text-green-800">
                        Succès
                    </p>
                    <p class="mt-1 text-sm text-green-700">
                        {{ session('message') }}
                    </p>
                </div>
                <div class="ml-4 flex-shrink-0">
                    <button @click="show = false" 
                            class="inline-flex text-green-500 hover:text-green-700 focus:outline-none">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div x-data="{ show: true }" 
             x-show="show" 
             x-init="setTimeout(() => show = false, 15000); $nextTick(() => { window.dispatchEvent(new CustomEvent('pharmacie-error')); })"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-y-2 scale-95"
             x-transition:enter-end="opacity-100 transform translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 transform translate-y-2 scale-95"
             class="fixed top-4 right-4 z-[9999] max-w-lg w-full mx-4 bg-gradient-to-r from-red-50 to-red-100 border-2 border-red-500 shadow-2xl rounded-lg p-5 animate-pulse-once"
             style="box-shadow: 0 20px 25px -5px rgba(220, 38, 38, 0.3), 0 10px 10px -5px rgba(220, 38, 38, 0.2);"
             role="alert">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center w-10 h-10 rounded-full bg-red-500 animate-pulse">
                        <i class="fas fa-exclamation-triangle text-white text-xl"></i>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <p class="text-base font-bold text-red-900 uppercase tracking-wide">
                        ⚠️ Erreur lors de la création de la facture
                    </p>
                    <p class="mt-2 text-sm text-red-800 font-semibold leading-relaxed bg-white/50 p-3 rounded border border-red-200">
                        {{ session('error') }}
                    </p>
                    <p class="mt-2 text-xs text-red-600 italic">
                        Veuillez vérifier les informations et réessayer.
                    </p>
                </div>
                <div class="ml-4 flex-shrink-0">
                    <button @click="show = false" 
                            class="inline-flex items-center justify-center w-8 h-8 rounded-full text-red-500 hover:bg-red-200 hover:text-red-700 focus:outline-none transition-colors">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
            </div>
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
    @if(!$venteOnly)
    <div class="mb-6 border-b border-gray-200">
        <nav class="flex space-x-4 overflow-x-auto">
            <button wire:click="$set('activeTab', 'dashboard')" 
                    class="px-4 py-3 border-b-2 font-medium text-sm whitespace-nowrap transition-colors {{ $activeTab === 'dashboard' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                <i class="fas fa-chart-line mr-2"></i>Tableau de bord
            </button>
            <button wire:click="$set('activeTab', 'stock')" 
                    class="px-4 py-3 border-b-2 font-medium text-sm whitespace-nowrap transition-colors {{ $activeTab === 'stock' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                <i class="fas fa-boxes mr-2"></i>Stock actuel
            </button>
            <button wire:click="$set('activeTab', 'entree')" 
                    class="px-4 py-3 border-b-2 font-medium text-sm whitespace-nowrap transition-colors {{ $activeTab === 'entree' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                <i class="fas fa-arrow-down mr-2"></i>Entrées de stock
            </button>
            <button wire:click="$set('activeTab', 'historique')" 
                    class="px-4 py-3 border-b-2 font-medium text-sm whitespace-nowrap transition-colors {{ $activeTab === 'historique' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                <i class="fas fa-history mr-2"></i>Historique
            </button>
        </nav>
    </div>
    @endif

    {{-- Contenu des onglets --}}

    {{-- ONGLET TABLEAU DE BORD --}}
    @if($activeTab === 'dashboard')
    <div>
        @php
            $stats = $this->statistiquesDashboard;
        @endphp
        
        {{-- Cartes de statistiques principales --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            {{-- Total médicaments --}}
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl shadow-lg p-8 border-l-6 border-blue-600 hover:shadow-xl transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-base font-semibold text-blue-800 uppercase tracking-wide mb-3">Total médicaments</p>
                        <p class="text-5xl font-extrabold text-blue-900 mb-1">{{ $stats['totalMedicaments'] }}</p>
                        <p class="text-sm font-medium text-blue-700 mt-2">Médicament(s) en stock</p>
                    </div>
                    <div class="bg-blue-500 rounded-2xl p-5 ml-4 shadow-md">
                        <i class="fas fa-pills text-white text-3xl"></i>
                    </div>
                </div>
            </div>

            {{-- Valeur du stock --}}
            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl shadow-lg p-8 border-l-6 border-green-600 hover:shadow-xl transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-base font-semibold text-green-800 uppercase tracking-wide mb-3">Valeur du stock</p>
                        <p class="text-4xl font-extrabold text-green-900 mb-1">{{ number_format($stats['valeurStock'], 0, ',', ' ') }}</p>
                        <p class="text-sm font-medium text-green-700 mt-2">MRU</p>
                    </div>
                    <div class="bg-green-500 rounded-2xl p-5 ml-4 shadow-md">
                        <i class="fas fa-coins text-white text-3xl"></i>
                    </div>
                </div>
            </div>

            {{-- Total quantité --}}
            <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl shadow-lg p-8 border-l-6 border-purple-600 hover:shadow-xl transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-base font-semibold text-purple-800 uppercase tracking-wide mb-3">Quantité totale</p>
                        <p class="text-5xl font-extrabold text-purple-900 mb-1">{{ number_format($stats['totalQuantiteStock'], 0, ',', ' ') }}</p>
                        <p class="text-sm font-medium text-purple-700 mt-2">Unité(s) disponible(s)</p>
                    </div>
                    <div class="bg-purple-500 rounded-2xl p-5 ml-4 shadow-md">
                        <i class="fas fa-cubes text-white text-3xl"></i>
                    </div>
                </div>
            </div>

            {{-- Médicaments en rupture --}}
            <button wire:click="ouvrirModalRupture" 
                    class="bg-gradient-to-br from-red-50 to-red-100 rounded-xl shadow-lg p-8 border-l-6 border-red-600 hover:shadow-xl transition-all duration-300 cursor-pointer hover:scale-105 text-left w-full">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-base font-semibold text-red-800 uppercase tracking-wide mb-3">En rupture</p>
                        <p class="text-5xl font-extrabold text-red-900 mb-1">{{ $stats['medicamentsRupture'] }}</p>
                        <p class="text-sm font-medium text-red-700 mt-2">Médicament(s) épuisé(s)</p>
                    </div>
                    <div class="bg-red-500 rounded-2xl p-5 ml-4 shadow-md">
                        <i class="fas fa-exclamation-circle text-white text-3xl"></i>
                    </div>
                </div>
            </button>
        </div>

        {{-- Cartes d'alertes et mouvements --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            {{-- Stock faible --}}
            <button wire:click="ouvrirModalStockFaible" 
                    class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl shadow-lg p-8 border-l-6 border-yellow-500 hover:shadow-xl transition-all duration-300 cursor-pointer hover:scale-105 text-left w-full">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-yellow-900 uppercase tracking-wide">Stock faible</h3>
                    <div class="bg-yellow-500 rounded-xl p-3 shadow-md">
                        <i class="fas fa-exclamation-triangle text-white text-2xl"></i>
                    </div>
                </div>
                <p class="text-5xl font-extrabold text-yellow-700 mb-3">{{ $stats['medicamentsStockFaible'] }}</p>
                <p class="text-base font-semibold text-yellow-800">Médicament(s) sous le seuil minimum</p>
            </button>

            {{-- Lots expirés --}}
            <button wire:click="ouvrirModalLotsExpires" 
                    class="bg-gradient-to-br from-red-50 to-red-100 rounded-xl shadow-lg p-8 border-l-6 border-red-600 hover:shadow-xl transition-all duration-300 cursor-pointer hover:scale-105 text-left w-full">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-red-900 uppercase tracking-wide">Lots expirés</h3>
                    <div class="bg-red-600 rounded-xl p-3 shadow-md">
                        <i class="fas fa-times-circle text-white text-2xl"></i>
                    </div>
                </div>
                <p class="text-5xl font-extrabold text-red-700 mb-3">{{ $stats['lotsExpires'] }}</p>
                <p class="text-base font-semibold text-red-800">Lot(s) avec date d'expiration dépassée</p>
            </button>

            {{-- Lots expirant bientôt --}}
            <button wire:click="ouvrirModalExpireBientot" 
                    class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl shadow-lg p-8 border-l-6 border-orange-500 hover:shadow-xl transition-all duration-300 cursor-pointer hover:scale-105 text-left w-full">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-orange-900 uppercase tracking-wide">Expire bientôt</h3>
                    <div class="bg-orange-500 rounded-xl p-3 shadow-md">
                        <i class="fas fa-clock text-white text-2xl"></i>
                    </div>
                </div>
                <p class="text-5xl font-extrabold text-orange-700 mb-3">{{ $stats['lotsExpireBientot'] }}</p>
                <p class="text-base font-semibold text-orange-800">Lot(s) expirant dans 30 jours</p>
            </button>
        </div>

        {{-- Mouvements du mois --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Entrées ce mois --}}
            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl shadow-lg p-8 border-l-6 border-green-600 hover:shadow-xl transition-shadow duration-300">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-green-900 uppercase tracking-wide">
                        <i class="fas fa-arrow-down text-green-600 mr-3 text-2xl"></i>Entrées ce mois
                    </h3>
                    <div class="bg-green-600 rounded-xl p-3 shadow-md">
                        <i class="fas fa-arrow-down text-white text-2xl"></i>
                    </div>
                </div>
                <p class="text-6xl font-extrabold text-green-700 mb-3">{{ $stats['entreesCeMois'] }}</p>
                <p class="text-base font-semibold text-green-800">Mouvement(s) d'entrée enregistré(s)</p>
            </div>

            {{-- Sorties ce mois --}}
            <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-xl shadow-lg p-8 border-l-6 border-red-600 hover:shadow-xl transition-shadow duration-300">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-red-900 uppercase tracking-wide">
                        <i class="fas fa-arrow-up text-red-600 mr-3 text-2xl"></i>Sorties ce mois
                    </h3>
                    <div class="bg-red-600 rounded-xl p-3 shadow-md">
                        <i class="fas fa-arrow-up text-white text-2xl"></i>
                    </div>
                </div>
                <p class="text-6xl font-extrabold text-red-700 mb-3">{{ $stats['sortiesCeMois'] }}</p>
                <p class="text-base font-semibold text-red-800">Mouvement(s) de sortie enregistré(s)</p>
            </div>
        </div>
    </div>
    @endif

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
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock disponible</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Seuil min</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prix achat</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prix vente</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($stocks as $stock)
                        @php
                            $stockDisponible = $this->calculerStockDisponible($stock->fkidMedicament);
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $stock->medicament->LibelleMedic ?? 'N/A' }}</div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="flex flex-col">
                                    <span class="text-sm font-semibold {{ $stockDisponible <= $stock->quantiteMin ? 'text-red-600' : 'text-gray-900' }}">
                                        Disponible: {{ number_format($stockDisponible, 0) }}
                                    </span>
                                    @if($stock->quantiteStock != $stockDisponible)
                                        <span class="text-xs text-gray-400">
                                            Total: {{ number_format($stock->quantiteStock, 0) }}
                                        </span>
                                    @endif
                                </div>
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
                                @if($stockDisponible <= $stock->quantiteMin)
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>Faible
                                    </span>
                                @elseif($stockDisponible <= 0)
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                        <i class="fas fa-times-circle mr-1"></i>Indisponible
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
                                <div class="relative">
                                    <input 
                                        type="text" 
                                        wire:model.live.debounce.300ms="entreeSearchMedicament"
                                        placeholder="Rechercher un médicament..."
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                                        autocomplete="off"
                                    >
                                    
                                    <!-- Indicateur de chargement -->
                                    <div wire:loading class="absolute right-3 top-1/2 transform -translate-y-1/2">
                                        <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-primary"></div>
                                    </div>
                                    
                                    <!-- Résultats de recherche -->
                                    @if($entreeShowMedicamentResults && count($entreeMedicamentsResults) > 0)
                                        <div class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-auto">
                                            @foreach($entreeMedicamentsResults as $medicament)
                                                <div 
                                                    wire:click="selectEntreeMedicament({{ $medicament->IDMedic }})"
                                                    class="p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0 transition-colors"
                                                >
                                                    <div class="flex items-center justify-between">
                                                        <div class="flex-1">
                                                            <div class="font-medium text-gray-900">
                                                                {{ $medicament->LibelleMedic }}
                                                            </div>
                                                            @if($medicament->PrixRef)
                                                                <div class="text-sm text-gray-600 mt-1">
                                                                    <i class="fas fa-tag w-4 text-gray-400 mr-2"></i>
                                                                    Prix: {{ number_format($medicament->PrixRef, 0, ',', ' ') }} MRU
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @elseif($entreeShowMedicamentResults && strlen(trim($entreeSearchMedicament)) >= 1 && !$entreeIsSearchingMedicament && count($entreeMedicamentsResults) === 0)
                                        <div class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg">
                                            <div class="p-3 text-gray-400 text-center">
                                                Aucun médicament trouvé pour "{{ $entreeSearchMedicament }}"
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                
                                @if($entreeMedicamentId)
                                    <div class="mt-2 p-2 bg-green-50 border border-green-200 rounded text-sm text-green-700">
                                        <i class="fas fa-check-circle mr-2"></i>
                                        Médicament sélectionné: <strong>{{ $entreeLibelleMedic }}</strong>
                                    </div>
                                @endif
                                
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
                                <label class="block text-sm font-medium text-gray-700 mb-1">Prix de vente unitaire *</label>
                                <input type="number" step="1" wire:model="entreePrixVente" 
                                       value="{{ $entreePrixVente ?? 0 }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary" min="0">
                                @error('entreePrixVente') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                <p class="text-xs text-gray-500 mt-1">Prix de vente pour ce médicament (pré-rempli depuis le PrixRef du médicament)</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Seuil minimum *</label>
                                <input type="number" step="1" wire:model="entreeQuantiteMin" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary" min="0"
                                       placeholder="Quantité minimale à maintenir en stock">
                                @error('entreeQuantiteMin') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                <p class="text-xs text-gray-500 mt-1">Quantité minimale à maintenir en stock pour déclencher une alerte</p>
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
    @if($activeTab === 'vente' && $venteOnly)
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
                            @php
                                $stockDisponible = $this->calculerStockDisponible($stock->fkidMedicament);
                            @endphp
                            @if($stockDisponible > 0)
                            <div class="p-4 border-b border-gray-100 hover:bg-gray-50 flex items-center justify-between">
                                <div class="flex-1">
                                    <div class="font-medium text-gray-900">{{ $stock->medicament->LibelleMedic ?? 'N/A' }}</div>
                                    <div class="text-sm text-gray-500 mt-1">
                                        Stock disponible: <span class="font-semibold {{ $stockDisponible <= $stock->quantiteMin ? 'text-red-600' : 'text-gray-700' }}">{{ number_format($stockDisponible, 0) }}</span>
                                        @if($stock->quantiteStock != $stockDisponible)
                                            <span class="text-xs text-gray-400">(Stock total: {{ number_format($stock->quantiteStock, 0) }})</span>
                                        @endif
                                        @php
                                            $prix = $stock->medicament->PrixRef ?? 0;
                                        @endphp
                                        @if($prix > 0)
                                            | Prix: <span class="font-semibold">{{ number_format($prix, 0) }} MRU</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 ml-4">
                                    <input type="number" 
                                           wire:model="quantiteVente.{{ $stock->fkidMedicament }}" 
                                           value="{{ $quantiteVente[$stock->fkidMedicament] ?? 1 }}" 
                                           min="1" step="1" 
                                           class="w-20 px-2 py-1 border border-gray-300 rounded text-sm"
                                           max="{{ $stockDisponible }}">
                                    <button wire:click="ajouterAuPanierVente({{ $stock->fkidMedicament }})" 
                                            @if(!$patientId || $stockDisponible <= 0) disabled @endif
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
                        <button wire:click="$emit('ouvrirFacturationDepuisPharmacie', {{ $factureVenteId }})" 
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

    {{-- Modal Médicaments en rupture --}}
    @if($showModalRupture)
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75" wire:click="fermerModalRupture"></div>
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-bold text-red-800 flex items-center gap-2">
                            <i class="fas fa-exclamation-circle text-red-600"></i>
                            Médicaments en rupture de stock
                        </h3>
                        <button wire:click="fermerModalRupture" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    <div class="overflow-x-auto max-h-96">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50 sticky top-0">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Médicament</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Seuil min</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prix achat</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prix vente</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($this->medicamentsRupture as $stock)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $stock->medicament->LibelleMedic ?? 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-red-600 font-semibold">
                                        {{ number_format($stock->quantiteStock, 0) }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                        {{ number_format($stock->quantiteMin, 0) }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                        {{ number_format($stock->prixAchat, 0) }} MRU
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                        {{ number_format($stock->prixVente, 0) }} MRU
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                        <i class="fas fa-check-circle text-green-500 text-3xl mb-2"></i>
                                        <p>Aucun médicament en rupture</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button wire:click="fermerModalRupture" class="w-full sm:w-auto px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark">
                        Fermer
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Modal Stock faible --}}
    @if($showModalStockFaible)
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75" wire:click="fermerModalStockFaible"></div>
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-bold text-yellow-800 flex items-center gap-2">
                            <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                            Médicaments en stock faible
                        </h3>
                        <button wire:click="fermerModalStockFaible" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    <div class="overflow-x-auto max-h-96">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50 sticky top-0">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Médicament</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Seuil min</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prix achat</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prix vente</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($this->medicamentsStockFaible as $stock)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $stock->medicament->LibelleMedic ?? 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-yellow-600 font-semibold">
                                        {{ number_format($stock->quantiteStock, 0) }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                        {{ number_format($stock->quantiteMin, 0) }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                        {{ number_format($stock->prixAchat, 0) }} MRU
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                        {{ number_format($stock->prixVente, 0) }} MRU
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                        <i class="fas fa-check-circle text-green-500 text-3xl mb-2"></i>
                                        <p>Aucun médicament en stock faible</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button wire:click="fermerModalStockFaible" class="w-full sm:w-auto px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark">
                        Fermer
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Modal Lots expirés --}}
    @if($showModalLotsExpires)
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75" wire:click="fermerModalLotsExpires"></div>
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-bold text-red-800 flex items-center gap-2">
                            <i class="fas fa-times-circle text-red-600"></i>
                            Lots expirés
                        </h3>
                        <button wire:click="fermerModalLotsExpires" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    <div class="overflow-x-auto max-h-96">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50 sticky top-0">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Médicament</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">N° Lot</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantité restante</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date expiration</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fournisseur</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($this->lotsExpires as $lot)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $lot->stock->medicament->LibelleMedic ?? 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                        {{ $lot->numeroLot ?? 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-red-600 font-semibold">
                                        {{ number_format($lot->quantiteRestante, 0) }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-red-600 font-semibold">
                                        {{ $lot->dateExpiration ? \Carbon\Carbon::parse($lot->dateExpiration)->format('d/m/Y') : 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                        {{ $lot->fournisseur ?? 'N/A' }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                        <i class="fas fa-check-circle text-green-500 text-3xl mb-2"></i>
                                        <p>Aucun lot expiré</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button wire:click="fermerModalLotsExpires" class="w-full sm:w-auto px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark">
                        Fermer
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Modal Expire bientôt --}}
    @if($showModalExpireBientot)
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75" wire:click="fermerModalExpireBientot"></div>
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-bold text-orange-800 flex items-center gap-2">
                            <i class="fas fa-clock text-orange-600"></i>
                            Lots expirant bientôt (30 jours)
                        </h3>
                        <button wire:click="fermerModalExpireBientot" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    <div class="overflow-x-auto max-h-96">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50 sticky top-0">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Médicament</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">N° Lot</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantité restante</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date expiration</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jours restants</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fournisseur</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($this->lotsExpireBientot as $lot)
                                @php
                                    $joursRestants = $lot->dateExpiration ? \Carbon\Carbon::parse($lot->dateExpiration)->diffInDays(\Carbon\Carbon::now(), false) : null;
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $lot->stock->medicament->LibelleMedic ?? 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                        {{ $lot->numeroLot ?? 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-orange-600 font-semibold">
                                        {{ number_format($lot->quantiteRestante, 0) }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                        {{ $lot->dateExpiration ? \Carbon\Carbon::parse($lot->dateExpiration)->format('d/m/Y') : 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm">
                                        @if($joursRestants !== null)
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $joursRestants <= 7 ? 'bg-red-100 text-red-800' : ($joursRestants <= 15 ? 'bg-orange-100 text-orange-800' : 'bg-yellow-100 text-yellow-800') }}">
                                                {{ abs($joursRestants) }} jour(s)
                                            </span>
                                        @else
                                            <span class="text-gray-400">N/A</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                        {{ $lot->fournisseur ?? 'N/A' }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                        <i class="fas fa-check-circle text-green-500 text-3xl mb-2"></i>
                                        <p>Aucun lot n'expire bientôt</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button wire:click="fermerModalExpireBientot" class="w-full sm:w-auto px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark">
                        Fermer
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

{{-- Styles et scripts pour les notifications --}}
<style>
    @keyframes pulse-once {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: 0.8;
        }
    }
    
    .animate-pulse-once {
        animation: pulse-once 2s ease-in-out;
    }
    
    /* Amélioration de la visibilité des notifications d'erreur */
    .fixed.top-4.right-4 {
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3), 0 8px 10px -6px rgba(0, 0, 0, 0.2);
    }
    
    /* Animation de shake pour les erreurs critiques */
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
        20%, 40%, 60%, 80% { transform: translateX(5px); }
    }
    
    .shake-once {
        animation: shake 0.5s ease-in-out;
    }
</style>

<script>
    // Améliorer la visibilité des notifications d'erreur
    document.addEventListener('DOMContentLoaded', function () {
        // Écouter l'événement personnalisé pour les erreurs de pharmacie
        window.addEventListener('pharmacie-error', function (event) {
            // Faire défiler vers le haut pour voir la notification
            window.scrollTo({ top: 0, behavior: 'smooth' });
            
            // Ajouter une animation shake à la notification d'erreur
            setTimeout(function() {
                const errorNotifications = document.querySelectorAll('[role="alert"].bg-red-50');
                errorNotifications.forEach(function(notification) {
                    notification.classList.add('shake-once');
                    // Faire clignoter la notification
                    notification.style.animation = 'shake 0.5s ease-in-out, pulse-once 2s ease-in-out';
                });
            }, 300);
        });
        
        // Écouter les événements Livewire
        if (typeof Livewire !== 'undefined') {
            document.addEventListener('livewire:load', function () {
                // Ajouter une classe shake aux notifications d'erreur au chargement
                setTimeout(function() {
                    const errorNotifications = document.querySelectorAll('[role="alert"]');
                    errorNotifications.forEach(function(notification) {
                        if (notification.classList.contains('bg-red-50')) {
                            notification.classList.add('shake-once');
                        }
                    });
                }, 100);
            });
            
            // Recharger les notifications après chaque mise à jour Livewire
            document.addEventListener('livewire:update', function () {
                setTimeout(function() {
                    const errorNotifications = document.querySelectorAll('[role="alert"].bg-red-50');
                    errorNotifications.forEach(function(notification) {
                        if (!notification.classList.contains('shake-once')) {
                            notification.classList.add('shake-once');
                        }
                    });
                    
                    // Si une notification d'erreur existe, scroller vers le haut
                    if (errorNotifications.length > 0) {
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }
                }, 100);
            });
        }
    });
</script>
