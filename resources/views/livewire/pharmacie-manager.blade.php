<div class="p-4 sm:p-6">
    <div class="mb-6">
        <h2 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-2">
            <i class="fas fa-chart-line text-primary mr-2"></i>Dashboard de suivi de stock
        </h2>
        <p class="text-gray-600">Vue d'ensemble du stock de médicaments</p>
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

    {{-- Contenu du tableau de bord uniquement --}}
    <div>
        @php
            $stats = $this->statistiquesDashboard;
        @endphp
        
        {{-- Cartes de statistiques principales --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            {{-- Total médicaments --}}
            <div wire:click="ouvrirDetailModal('total')" class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl shadow-lg p-8 border-l-6 border-blue-600 hover:shadow-xl transition-all duration-300 cursor-pointer transform hover:scale-105">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-base font-semibold text-blue-800 uppercase tracking-wide mb-3">Total médicaments</p>
                        <p class="text-5xl font-extrabold text-blue-900 mb-1">{{ $stats['totalMedicaments'] }}</p>
                        <p class="text-sm font-medium text-blue-700 mt-2">Médicament(s) en stock</p>
                        <p class="text-xs text-blue-600 mt-2"><i class="fas fa-mouse-pointer mr-1"></i>Cliquer pour voir les détails</p>
                    </div>
                    <div class="bg-blue-500 rounded-2xl p-5 ml-4 shadow-md">
                        <i class="fas fa-pills text-white text-3xl"></i>
                    </div>
                </div>
            </div>

            {{-- Valeur du stock --}}
            <div wire:click="ouvrirDetailModal('valeur')" class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl shadow-lg p-8 border-l-6 border-green-600 hover:shadow-xl transition-all duration-300 cursor-pointer transform hover:scale-105">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-base font-semibold text-green-800 uppercase tracking-wide mb-3">Valeur du stock</p>
                        <p class="text-4xl font-extrabold text-green-900 mb-1">{{ number_format($stats['valeurStock'], 0, ',', ' ') }}</p>
                        <p class="text-sm font-medium text-green-700 mt-2">MRU</p>
                        <p class="text-xs text-green-600 mt-2"><i class="fas fa-mouse-pointer mr-1"></i>Cliquer pour voir les détails</p>
                    </div>
                    <div class="bg-green-500 rounded-2xl p-5 ml-4 shadow-md">
                        <i class="fas fa-coins text-white text-3xl"></i>
                    </div>
                </div>
            </div>

            {{-- Total quantité --}}
            <div wire:click="ouvrirDetailModal('quantite')" class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl shadow-lg p-8 border-l-6 border-purple-600 hover:shadow-xl transition-all duration-300 cursor-pointer transform hover:scale-105">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-base font-semibold text-purple-800 uppercase tracking-wide mb-3">Unités disponibles</p>
                        <p class="text-5xl font-extrabold text-purple-900 mb-1">{{ number_format($stats['totalQuantiteStock'], 0, ',', ' ') }}</p>
                        <p class="text-sm font-medium text-purple-700 mt-2">Unité(s) disponible(s)</p>
                        <p class="text-xs text-purple-600 mt-2"><i class="fas fa-mouse-pointer mr-1"></i>Cliquer pour voir les détails</p>
                    </div>
                    <div class="bg-purple-500 rounded-2xl p-5 ml-4 shadow-md">
                        <i class="fas fa-cubes text-white text-3xl"></i>
                    </div>
                </div>
            </div>

            {{-- Médicaments en rupture --}}
            <div wire:click="ouvrirDetailModal('rupture')" class="bg-gradient-to-br from-red-50 to-red-100 rounded-xl shadow-lg p-8 border-l-6 border-red-600 hover:shadow-xl transition-all duration-300 cursor-pointer transform hover:scale-105">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-base font-semibold text-red-800 uppercase tracking-wide mb-3">En rupture</p>
                        <p class="text-5xl font-extrabold text-red-900 mb-1">{{ $stats['medicamentsRupture'] }}</p>
                        <p class="text-sm font-medium text-red-700 mt-2">Médicament(s) épuisé(s)</p>
                        <p class="text-xs text-red-600 mt-2"><i class="fas fa-mouse-pointer mr-1"></i>Cliquer pour voir les détails</p>
                    </div>
                    <div class="bg-red-500 rounded-2xl p-5 ml-4 shadow-md">
                        <i class="fas fa-exclamation-circle text-white text-3xl"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Cartes d'alertes et mouvements --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            {{-- Stock faible --}}
            <div wire:click="ouvrirDetailModal('faible')" class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl shadow-lg p-8 border-l-6 border-yellow-500 hover:shadow-xl transition-all duration-300 cursor-pointer transform hover:scale-105">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-yellow-900 uppercase tracking-wide">Stock faible</h3>
                    <div class="bg-yellow-500 rounded-xl p-3 shadow-md">
                        <i class="fas fa-exclamation-triangle text-white text-2xl"></i>
                    </div>
                </div>
                <p class="text-5xl font-extrabold text-yellow-700 mb-3">{{ $stats['medicamentsStockFaible'] }}</p>
                <p class="text-base font-semibold text-yellow-800">Médicament(s) sous le seuil minimum</p>
                <p class="text-xs text-yellow-600 mt-2"><i class="fas fa-mouse-pointer mr-1"></i>Cliquer pour voir les détails</p>
            </div>

            {{-- Lots expirés --}}
            <div wire:click="ouvrirDetailModal('expires')" class="bg-gradient-to-br from-red-50 to-red-100 rounded-xl shadow-lg p-8 border-l-6 border-red-600 hover:shadow-xl transition-all duration-300 cursor-pointer transform hover:scale-105">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-red-900 uppercase tracking-wide">Lots expirés</h3>
                    <div class="bg-red-600 rounded-xl p-3 shadow-md">
                        <i class="fas fa-times-circle text-white text-2xl"></i>
                    </div>
                </div>
                <p class="text-5xl font-extrabold text-red-700 mb-3">{{ $stats['lotsExpires'] }}</p>
                <p class="text-base font-semibold text-red-800">Lot(s) avec date d'expiration dépassée</p>
                <p class="text-xs text-red-600 mt-2"><i class="fas fa-mouse-pointer mr-1"></i>Cliquer pour voir les détails</p>
            </div>

            {{-- Lots expirant bientôt --}}
            <div wire:click="ouvrirDetailModal('expire_bientot')" class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl shadow-lg p-8 border-l-6 border-orange-500 hover:shadow-xl transition-all duration-300 cursor-pointer transform hover:scale-105">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-orange-900 uppercase tracking-wide">Expire bientôt</h3>
                    <div class="bg-orange-500 rounded-xl p-3 shadow-md">
                        <i class="fas fa-clock text-white text-2xl"></i>
                    </div>
                </div>
                <p class="text-5xl font-extrabold text-orange-700 mb-3">{{ $stats['lotsExpireBientot'] }}</p>
                <p class="text-base font-semibold text-orange-800">Lot(s) expirant dans 30 jours</p>
                <p class="text-xs text-orange-600 mt-2"><i class="fas fa-mouse-pointer mr-1"></i>Cliquer pour voir les détails</p>
            </div>
        </div>

        {{-- Mouvements du mois --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Entrées ce mois --}}
            <div wire:click="ouvrirDetailModal('entrees')" class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl shadow-lg p-8 border-l-6 border-green-600 hover:shadow-xl transition-all duration-300 cursor-pointer transform hover:scale-105">
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
                <p class="text-xs text-green-600 mt-2"><i class="fas fa-mouse-pointer mr-1"></i>Cliquer pour voir les détails</p>
            </div>

            {{-- Sorties ce mois --}}
            <div wire:click="ouvrirDetailModal('sorties')" class="bg-gradient-to-br from-red-50 to-red-100 rounded-xl shadow-lg p-8 border-l-6 border-red-600 hover:shadow-xl transition-all duration-300 cursor-pointer transform hover:scale-105">
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
                <p class="text-xs text-red-600 mt-2"><i class="fas fa-mouse-pointer mr-1"></i>Cliquer pour voir les détails</p>
            </div>
        </div>
    </div>

    {{-- Modal de détails --}}
    @if($showDetailModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4" wire:click.self="fermerDetailModal">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-6xl max-h-[90vh] overflow-hidden flex flex-col">
            <div class="bg-primary text-white p-4 rounded-t-lg flex justify-between items-center">
                <h3 class="text-xl font-bold">
                    @if($detailType === 'total') Détails - Total médicaments
                    @elseif($detailType === 'valeur') Détails - Valeur du stock
                    @elseif($detailType === 'quantite') Détails - Unités disponibles
                    @elseif($detailType === 'rupture') Détails - Médicaments en rupture
                    @elseif($detailType === 'faible') Détails - Stock faible
                    @elseif($detailType === 'expires') Détails - Lots expirés
                    @elseif($detailType === 'expire_bientot') Détails - Lots expirant bientôt
                    @elseif($detailType === 'entrees') Détails - Entrées ce mois
                    @elseif($detailType === 'sorties') Détails - Sorties ce mois
                    @endif
                </h3>
                <button wire:click="fermerDetailModal" class="text-white hover:text-gray-200 text-2xl">&times;</button>
            </div>
            <div class="p-6 overflow-y-auto flex-1">
                @if(count($detailData) > 0)
                    <div class="mb-4 flex justify-between items-center">
                        <p class="text-sm text-gray-600">
                            Affichage de <span class="font-semibold">{{ (($this->detailPage - 1) * $this->detailPerPage) + 1 }}</span> 
                            à <span class="font-semibold">{{ min($this->detailPage * $this->detailPerPage, count($detailData)) }}</span> 
                            sur <span class="font-semibold">{{ count($detailData) }}</span> résultat(s)
                        </p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    @if($detailType === 'total' || $detailType === 'valeur' || $detailType === 'quantite' || $detailType === 'rupture' || $detailType === 'faible')
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Médicament</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantité</th>
                                        @if($detailType === 'total' || $detailType === 'valeur' || $detailType === 'rupture' || $detailType === 'faible')
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prix d'achat</th>
                                        @endif
                                        @if($detailType === 'total' || $detailType === 'valeur')
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Valeur</th>
                                        @endif
                                        @if($detailType === 'total' || $detailType === 'quantite' || $detailType === 'faible')
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Seuil min</th>
                                        @endif
                                        @if($detailType === 'faible')
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Déficit</th>
                                        @endif
                                        @if($detailType === 'total')
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                                        @endif
                                    @elseif($detailType === 'expires' || $detailType === 'expire_bientot')
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Médicament</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">N° Lot</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantité</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date expiration</th>
                                        @if($detailType === 'expires')
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jours expirés</th>
                                        @else
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jours restants</th>
                                        @endif
                                    @elseif($detailType === 'entrees' || $detailType === 'sorties')
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Médicament</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantité</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            @if($detailType === 'entrees')
                                                Prix d'achat
                                            @else
                                                Prix de vente
                                            @endif
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Montant</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Utilisateur</th>
                                        @if($detailType === 'sorties')
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Patient</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Facture</th>
                                        @endif
                                        @if($detailType === 'entrees')
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Référence</th>
                                        @endif
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($this->detailDataPaginated as $item)
                                <tr class="hover:bg-gray-50">
                                    @if($detailType === 'total' || $detailType === 'valeur' || $detailType === 'quantite' || $detailType === 'rupture' || $detailType === 'faible')
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">{{ $item['medicament'] }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ number_format($item['quantite'], 0) }}</td>
                                        @if($detailType === 'total' || $detailType === 'valeur' || $detailType === 'rupture' || $detailType === 'faible')
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ number_format($item['prix_achat'] ?? 0, 0) }} MRU</td>
                                        @endif
                                        @if($detailType === 'total' || $detailType === 'valeur')
                                            <td class="px-4 py-3 whitespace-nowrap text-sm font-semibold text-gray-900">{{ number_format($item['valeur'] ?? 0, 0) }} MRU</td>
                                        @endif
                                        @if($detailType === 'total' || $detailType === 'quantite' || $detailType === 'faible')
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ number_format($item['seuil_min'] ?? 0, 0) }}</td>
                                        @endif
                                        @if($detailType === 'faible')
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-red-600 font-semibold">{{ number_format($item['difference'] ?? 0, 0) }}</td>
                                        @endif
                                        @if($detailType === 'total')
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                @if(($item['statut'] ?? '') === 'faible')
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Faible</span>
                                                @elseif(($item['statut'] ?? '') === 'rupture')
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Rupture</span>
                                                @else
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">OK</span>
                                                @endif
                                            </td>
                                        @endif
                                    @elseif($detailType === 'expires' || $detailType === 'expire_bientot')
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">{{ $item['medicament'] }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $item['numero_lot'] ?? 'N/A' }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ number_format($item['quantite'], 0) }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $item['date_expiration'] }}</td>
                                        @if($detailType === 'expires')
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-red-600 font-semibold">{{ abs($item['jours_expires'] ?? 0) }} jours</td>
                                        @else
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-orange-600 font-semibold">{{ $item['jours_restants'] ?? 0 }} jours</td>
                                        @endif
                                    @elseif($detailType === 'entrees' || $detailType === 'sorties')
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $item['date'] }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">{{ $item['medicament'] }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ number_format($item['quantite'], 0) }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ number_format($item['prix_unitaire'] ?? 0, 0) }} MRU</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-semibold text-gray-900">{{ number_format($item['montant'] ?? 0, 0) }} MRU</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $item['utilisateur'] }}</td>
                                        @if($detailType === 'sorties')
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $item['patient'] ?? 'N/A' }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $item['facture'] ?? 'N/A' }}</td>
                                        @endif
                                        @if($detailType === 'entrees')
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $item['reference'] ?? 'N/A' }}</td>
                                        @endif
                                    @endif
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    {{-- Pagination --}}
                    @if($this->detailTotalPages > 1)
                    <div class="mt-4 flex items-center justify-between border-t border-gray-200 pt-4">
                        <div class="flex items-center gap-2">
                            <button wire:click="previousDetailPage" 
                                    @if($this->detailPage <= 1) disabled @endif
                                    class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                                <i class="fas fa-chevron-left"></i> Précédent
                            </button>
                            
                            <div class="flex items-center gap-1">
                                @php
                                    $startPage = max(1, $this->detailPage - 2);
                                    $endPage = min($this->detailTotalPages, $this->detailPage + 2);
                                @endphp
                                
                                @if($startPage > 1)
                                    <button wire:click="goToDetailPage(1)" class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">1</button>
                                    @if($startPage > 2)
                                        <span class="px-2 text-gray-500">...</span>
                                    @endif
                                @endif
                                
                                @for($i = $startPage; $i <= $endPage; $i++)
                                    <button wire:click="goToDetailPage({{ $i }})" 
                                            class="px-3 py-2 text-sm font-medium {{ $i == $this->detailPage ? 'bg-primary text-white' : 'text-gray-700 bg-white border border-gray-300' }} rounded-lg hover:bg-gray-50">
                                        {{ $i }}
                                    </button>
                                @endfor
                                
                                @if($endPage < $this->detailTotalPages)
                                    @if($endPage < $this->detailTotalPages - 1)
                                        <span class="px-2 text-gray-500">...</span>
                                    @endif
                                    <button wire:click="goToDetailPage({{ $this->detailTotalPages }})" class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">{{ $this->detailTotalPages }}</button>
                                @endif
                            </div>
                            
                            <button wire:click="nextDetailPage" 
                                    @if($this->detailPage >= $this->detailTotalPages) disabled @endif
                                    class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                                Suivant <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                        
                        <div class="text-sm text-gray-600">
                            Page <span class="font-semibold">{{ $this->detailPage }}</span> sur <span class="font-semibold">{{ $this->detailTotalPages }}</span>
                        </div>
                    </div>
                    @endif
                @else
                    <div class="text-center py-8 text-gray-400">
                        <i class="fas fa-inbox text-4xl mb-2"></i>
                        <p>Aucun détail disponible</p>
                    </div>
                @endif
            </div>
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end">
                <button wire:click="fermerDetailModal" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                    Fermer
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
