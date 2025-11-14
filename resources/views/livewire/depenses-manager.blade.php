<div class="min-h-screen bg-gray-50">
    <div class="container mx-auto px-4 py-8">
        <!-- En-tête -->
        <div class="bg-gradient-to-r from-[#1e3a8a] to-[#1e3a8a]/80 shadow-2xl rounded-2xl overflow-hidden transform transition-all duration-300 hover:shadow-3xl mb-8">
            <div class="px-8 py-6">
                <h2 class="text-2xl font-bold text-white flex items-center">
                    <i class="fas fa-money-bill-wave mr-3"></i>
                    Gestion des Dépenses
                </h2>
            </div>
            <div class="p-8">
                <p class="text-white opacity-90">Gérez les dépenses du cabinet médical</p>
            </div>
        </div>

        <!-- Messages de succès/erreur -->
        @if (session()->has('message'))
            <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('message') }}</span>
            </div>
        @endif
        
        @if (session()->has('error'))
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <!-- Statistiques -->
        <div class="bg-white shadow-2xl rounded-2xl overflow-hidden transform transition-all duration-300 hover:shadow-3xl mb-8">
            <div class="bg-gradient-to-r from-[#1e3a8a] to-[#1e3a8a]/80 px-8 py-6">
                <h2 class="text-2xl font-bold text-white flex items-center">
                    <i class="fas fa-chart-line mr-3"></i>
                    Statistiques
                </h2>
            </div>
            <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-gradient-to-br from-[#1e3a8a]/10 to-white rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow">
                        <dt class="text-sm font-semibold text-[#1e3a8a] mb-2">Total des dépenses</dt>
                        <dd class="text-3xl font-bold text-red-600">{{ number_format($totalDepenses, 0, ',', ' ') }} MRU</dd>
                    </div>
                    <div class="bg-gradient-to-br from-[#1e3a8a]/10 to-white rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow">
                        <dt class="text-sm font-semibold text-[#1e3a8a] mb-2">Période</dt>
                        <dd class="text-lg font-semibold text-[#1e3a8a]">
                            {{ \Carbon\Carbon::parse($dateDebut)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($dateFin)->format('d/m/Y') }}
                        </dd>
                    </div>
                    <div class="bg-gradient-to-br from-[#1e3a8a]/10 to-white rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow">
                        <dt class="text-sm font-semibold text-[#1e3a8a] mb-2">Nombre d'opérations</dt>
                        <dd class="text-3xl font-bold text-[#1e3a8a]">{{ $depenses->total() }}</dd>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulaire d'ajout/modification -->
        <div class="bg-white shadow-2xl rounded-2xl overflow-hidden transform transition-all duration-300 hover:shadow-3xl mb-8">
            <div class="bg-gradient-to-r from-[#1e3a8a] to-[#1e3a8a]/80 px-8 py-6">
                <h2 class="text-2xl font-bold text-white flex items-center">
                    <i class="fas fa-plus mr-3"></i>
                    {{ $isEditing ? 'Modifier la Dépense' : 'Nouvelle Dépense' }}
                </h2>
            </div>
            <div class="p-8">
                <form wire:submit.prevent="save">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Date de dépense -->
                        <div>
                            <label for="dateDepense" class="block text-sm font-semibold text-[#1e3a8a] mb-2">
                                Date de dépense *
                            </label>
                            <input type="date" id="dateDepense" wire:model="dateDepense" 
                                   class="w-full px-4 py-3 rounded-lg border border-[#1e3a8a] focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] transition-colors">
                            @error('dateDepense') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Montant -->
                        <div>
                            <label for="montant" class="block text-sm font-semibold text-[#1e3a8a] mb-2">
                                Montant (MRU) *
                            </label>
                            <input type="number" id="montant" wire:model="montant" step="0.01" min="0"
                                   class="w-full px-4 py-3 rounded-lg border border-[#1e3a8a] focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] transition-colors">
                            @error('montant') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Type de tiers -->
                        <div>
                            <label for="fkIdTypeTiers" class="block text-sm font-semibold text-[#1e3a8a] mb-2">
                                Type de tiers *
                            </label>
                            <select id="fkIdTypeTiers" wire:model="fkIdTypeTiers"
                                    class="w-full px-4 py-3 rounded-lg border border-[#1e3a8a] focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] transition-colors">
                                <option value="">Sélectionner un type</option>
                                @foreach($typesTiers as $type)
                                    <option value="{{ $type->IdTypeTiers }}">{{ $type->LibelleTypeTiers }}</option>
                                @endforeach
                            </select>
                            @error('fkIdTypeTiers') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Type de paiement -->
                        <div>
                            <label for="TypePAie" class="block text-sm font-semibold text-[#1e3a8a] mb-2">
                                Type de paiement *
                            </label>
                            <select id="TypePAie" wire:model="TypePAie"
                                    class="w-full px-4 py-3 rounded-lg border border-[#1e3a8a] focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] transition-colors">
                                <option value="">Sélectionner un type</option>
                                @foreach($typesPaiement as $type)
                                    <option value="{{ $type->LibPaie }}">{{ $type->LibPaie }}</option>
                                @endforeach
                            </select>
                            @error('TypePAie') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>


                    </div>

                    <!-- Motif -->
                    <div class="mt-6">
                        <label for="motif" class="block text-sm font-semibold text-[#1e3a8a] mb-2">
                            Motif *
                        </label>
                        <textarea id="motif" wire:model="motif" rows="3"
                                  class="w-full px-4 py-3 rounded-lg border border-[#1e3a8a] focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] transition-colors"
                                  placeholder="Décrivez la nature de cette dépense..."></textarea>
                        @error('motif') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Boutons d'action -->
                    <div class="mt-6 flex justify-end space-x-3">
                        @if($isEditing)
                            <button type="button" wire:click="cancelEdit"
                                    class="px-6 py-3 border border-[#1e3a8a] text-[#1e3a8a] rounded-lg hover:bg-[#1e3a8a]/10 transition-colors font-semibold">
                                Annuler
                            </button>
                        @endif
                        <button type="submit"
                                class="px-6 py-3 bg-[#1e3a8a] text-white rounded-lg hover:bg-[#1e3a8a]/90 focus:outline-none focus:ring-2 focus:ring-[#1e3a8a] transition-colors font-semibold">
                            {{ $isEditing ? 'Modifier' : 'Ajouter' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Filtres -->
        <div class="bg-gradient-to-r from-[#1e3a8a] to-[#1e3a8a]/80 shadow-2xl rounded-2xl overflow-hidden transform transition-all duration-300 hover:shadow-3xl mb-8">
            <div class="px-8 py-6">
                <h2 class="text-2xl font-bold text-white flex items-center">
                    <i class="fas fa-filter mr-3"></i>
                    Filtres
                </h2>
            </div>
            <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Date début -->
                    <div>
                        <label class="block text-sm font-semibold text-white mb-2">Date début</label>
                        <input type="date" wire:model="dateDebut"
                               class="w-full px-4 py-3 rounded-lg border border-[#1e3a8a] focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] transition-colors">
                    </div>

                    <!-- Date fin -->
                    <div>
                        <label class="block text-sm font-semibold text-white mb-2">Date fin</label>
                        <input type="date" wire:model="dateFin"
                               class="w-full px-4 py-3 rounded-lg border border-[#1e3a8a] focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] transition-colors">
                    </div>

                    <!-- Type de tiers -->
                    <div>
                        <label class="block text-sm font-semibold text-white mb-2">Type de tiers</label>
                        <select wire:model="typeTiersFilter"
                                class="w-full px-4 py-3 rounded-lg border border-[#1e3a8a] focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] transition-colors">
                            <option value="">Tous les types</option>
                            @foreach($typesTiers as $type)
                                <option value="{{ $type->IdTypeTiers }}">{{ $type->LibelleTypeTiers }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button wire:click="resetFilters"
                            class="px-6 py-3 bg-white text-[#1e3a8a] rounded-lg hover:bg-[#1e3a8a]/10 transition-colors font-semibold">
                        <i class="fas fa-refresh mr-2"></i>
                        Réinitialiser les filtres
                    </button>
                </div>
            </div>
        </div>

        <!-- Liste des dépenses -->
        <div class="bg-gradient-to-r from-[#1e3a8a] to-[#1e3a8a]/80 px-8 py-6 flex justify-between items-center">
            <div class="flex items-center">
                <h2 class="text-2xl font-bold text-white flex items-center">
                    <i class="fas fa-list mr-3"></i>
                    Liste des Dépenses
                </h2>
            </div>
            <div class="text-white text-sm">
                <i class="fas fa-calendar mr-1"></i>
                {{ \Carbon\Carbon::parse($dateDebut)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($dateFin)->format('d/m/Y') }}
            </div>
        </div>
        <div class="p-8">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-[#1e3a8a]/20">
                    <thead class="bg-[#1e3a8a]/5">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-[#1e3a8a] uppercase tracking-wider">Date</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-[#1e3a8a] uppercase tracking-wider">Motif</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-[#1e3a8a] uppercase tracking-wider">Type</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-[#1e3a8a] uppercase tracking-wider">Montant</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-[#1e3a8a] uppercase tracking-wider">Mode</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-[#1e3a8a]/20">
                        @forelse($depenses as $depense)
                            <tr class="hover:bg-[#1e3a8a]/5 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-[#1e3a8a]">
                                    {{ \Carbon\Carbon::parse($depense->dateoper)->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 text-sm text-[#1e3a8a]">
                                    {{ $depense->designation }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-[#1e3a8a]">
                                    @php
                                        $typeTiers = $typesTiers->firstWhere('IdTypeTiers', $depense->fkIdTypeTiers);
                                    @endphp
                                    {{ $typeTiers ? $typeTiers->LibelleTypeTiers : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-red-600">
                                    {{ number_format($depense->retraitEspece, 0, ',', ' ') }} MRU
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-[#1e3a8a]">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $depense->TypePAie === 'CASH' ? 'bg-green-100 text-green-800' : 
                                           ($depense->TypePAie === 'CARD' ? 'bg-blue-100 text-blue-800' : 
                                            'bg-gray-100 text-gray-800') }}">
                                        {{ $depense->TypePAie }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-[#1e3a8a]">
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-info-circle text-2xl mb-2"></i>
                                        Aucune dépense trouvée pour cette période.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-[#1e3a8a]/20">
                {{ $depenses->links() }}
            </div>
        </div>
    </div>
</div>
