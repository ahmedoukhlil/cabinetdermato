<div>
    {{-- Messages Flash --}}
    @if (session()->has('message'))
        <div class="mb-4 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('message') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Formulaire de création --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-4 py-3 border-b border-gray-200">
                <h4 class="text-lg font-semibold text-gray-800 flex items-center">
                    <svg class="h-5 w-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Nouvelle Fiche de Traitement
                </h4>
            </div>

            <div class="p-4">
                <form wire:submit.prevent="sauvegarderFiche">
                    {{-- Sélection de la facture --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Facture</label>
                        @if($factureLocked && $factureId)
                            {{-- Afficher la facture en lecture seule si elle est verrouillée --}}
                            @php
                                $factureDisplay = $factureLockedData ?? collect($facturesPatient)->firstWhere('Idfacture', $factureId);
                            @endphp
                            <div class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-sm text-gray-700">
                                {{ $factureDisplay['Nfacture'] ?? 'N°' . $factureId }} - 
                                @if(isset($factureDisplay['DtFacture']))
                                    {{ is_string($factureDisplay['DtFacture']) ? \Carbon\Carbon::parse($factureDisplay['DtFacture'])->format('d/m/Y') : $factureDisplay['DtFacture']->format('d/m/Y') }} - 
                                @endif
                                {{ number_format($factureDisplay['TotFacture'] ?? 0, 2) }} MRU
                            </div>
                            <input type="hidden" wire:model="factureId">
                        @else
                            {{-- Select normal si la facture n'est pas verrouillée --}}
                            <select wire:model="factureId"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                                <option value="">Sélectionner une facture</option>
                                @foreach($facturesPatient as $facture)
                                    <option value="{{ $facture['Idfacture'] }}">
                                        {{ $facture['Nfacture'] ?? 'N°' . $facture['Idfacture'] }} - 
                                        {{ \Carbon\Carbon::parse($facture['DtFacture'])->format('d/m/Y') }} - 
                                        {{ number_format($facture['TotFacture'] ?? 0, 2) }} MRU
                                    </option>
                                @endforeach
                            </select>
                        @endif
                        @error('factureId') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        @if(count($facturesPatient) == 0)
                            <p class="text-xs text-gray-500 mt-1">Aucune facture disponible pour ce patient. Veuillez créer une facture d'abord.</p>
                        @endif
                    </div>

                    {{-- Sélection du médecin --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Médecin</label>
                        @if($medecinLocked && $medecinId)
                            {{-- Afficher le médecin en lecture seule si il est verrouillé --}}
                            @php
                                $medecinDisplay = $medecinLockedData ?? collect($medecins)->firstWhere('idMedecin', $medecinId);
                            @endphp
                            <div class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-sm text-gray-700">
                                {{ is_array($medecinDisplay) ? ($medecinDisplay['Nom'] ?? 'Médecin') : ($medecinDisplay->Nom ?? 'Médecin') }}
                            </div>
                            <input type="hidden" wire:model="medecinId">
                        @else
                            {{-- Select normal si le médecin n'est pas verrouillé --}}
                            <select wire:model="medecinId"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                                <option value="">Sélectionner un médecin</option>
                                @foreach($medecins as $medecin)
                                    <option value="{{ $medecin->idMedecin }}">{{ $medecin->Nom }}</option>
                                @endforeach
                            </select>
                        @endif
                        @error('medecinId') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Lignes de traitement --}}
                    <div class="space-y-3">
                        <div class="text-sm font-medium text-gray-700 mb-2">
                            Traitements - Ajoutez une ou plusieurs lignes
                        </div>
                        
                        @foreach($lignesTraitement as $index => $ligne)
                        <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                            <div class="flex items-start space-x-2">
                                <div class="flex-shrink-0 w-8 h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center font-semibold text-sm mt-1">
                                    {{ $index + 1 }}
                                </div>
                                <div class="flex-1 space-y-2">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Acte</label>
                                        <div class="relative">
                                            @if(!empty($ligne['acte_id']) && !empty($ligne['acte_libelle']))
                                                {{-- Affichage de l'acte sélectionné --}}
                                                <div class="flex items-center justify-between px-3 py-2 border border-gray-300 rounded-lg bg-gray-50">
                                                    <span class="text-sm text-gray-800">{{ $ligne['acte_libelle'] }}</span>
                                                    <button type="button"
                                                            wire:click="clearActeSearch({{ $index }})"
                                                            class="text-gray-400 hover:text-red-600 transition-colors">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            @else
                                                {{-- Champ de recherche --}}
                                                <input type="text"
                                                       wire:model.live.debounce.300ms="searchTerms.{{ $index }}"
                                                       placeholder="Rechercher un acte..."
                                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                                                
                                                {{-- Indicateur de chargement --}}
                                                <div wire:loading class="absolute right-3 top-1/2 transform -translate-y-1/2">
                                                    <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-blue-500"></div>
                                                </div>
                                            @endif

                                            {{-- Résultats de recherche --}}
                                            @if(isset($showSearchResults[$index]) && $showSearchResults[$index] && count($searchResults[$index] ?? []) > 0)
                                                <div class="absolute z-50 w-full mt-1 border border-gray-200 rounded-lg shadow-lg bg-white max-h-60 overflow-y-auto">
                                                    @foreach($searchResults[$index] as $item)
                                                        <div wire:click="selectActe({{ $index }}, {{ $item['ID'] }})"
                                                             class="p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0 transition-colors">
                                                            <div class="font-medium text-gray-900 text-sm">
                                                                {{ $item['Acte'] }}
                                                            </div>
                                                            @if(isset($item['PrixRef']))
                                                            <div class="text-xs text-gray-500 mt-1">
                                                                Prix: {{ number_format($item['PrixRef'], 2) }} UM
                                                            </div>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif

                                            {{-- Message "Aucun résultat" --}}
                                            @if(isset($showSearchResults[$index]) && $showSearchResults[$index] && count($searchResults[$index] ?? []) === 0 && strlen(trim($searchTerms[$index] ?? '')) > 0)
                                                <div class="absolute z-50 w-full mt-1 p-3 text-center text-gray-500 bg-gray-50 rounded-lg border border-gray-200">
                                                    Aucun résultat trouvé pour "{{ $searchTerms[$index] }}"
                                                </div>
                                            @endif
                                        </div>
                                        @error('lignesTraitement.' . $index . '.acte_id')
                                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Traitement / Description</label>
                                            <input type="text"
                                                   wire:model.defer="lignesTraitement.{{ $index }}.traitement"
                                                   placeholder="Ex: Traitement de la carie..."
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Prix</label>
                                            <input type="number"
                                                   step="0.01"
                                                   wire:model.defer="lignesTraitement.{{ $index }}.prix"
                                                   placeholder="0.00"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                                        </div>
                                    </div>
                                </div>
                                <button type="button"
                                        wire:click="supprimerLigne({{ $index }})"
                                        @if(count($lignesTraitement) <= 1) disabled @endif
                                        class="flex-shrink-0 text-red-500 hover:text-red-700 disabled:text-gray-400 disabled:cursor-not-allowed transition-colors mt-1">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        @endforeach

                        <button type="button"
                                wire:click="ajouterLigneVide"
                                class="w-full px-4 py-2 bg-blue-50 text-blue-700 hover:bg-blue-100 rounded-lg transition-colors flex items-center justify-center space-x-2 text-sm">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span>Ajouter une ligne</span>
                        </button>
                    </div>

                    {{-- Bouton d'enregistrement --}}
                    <div class="mt-4">
                        <button type="submit"
                                class="w-full px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-lg transition-all shadow-md hover:shadow-lg flex items-center justify-center space-x-2">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Enregistrer la Fiche de Traitement</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Liste des fiches de traitement --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-50 to-purple-50 px-4 py-3 border-b border-gray-200">
                <h4 class="text-lg font-semibold text-gray-800 flex items-center">
                    <svg class="h-5 w-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Fiches de Traitement du Patient
                </h4>
            </div>

            <div class="p-4 max-h-[600px] overflow-y-auto">
                <div class="mb-3">
                    <button wire:click="toggleAccordeon"
                            class="w-full flex items-center justify-between px-4 py-3 bg-gradient-to-r from-blue-50 to-indigo-50 hover:from-blue-100 hover:to-indigo-100 rounded-lg transition-all border border-blue-200">
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0 w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center">
                                <i class="fas fa-folder-medical text-white"></i>
                            </div>
                            <div class="text-left">
                                <h5 class="font-semibold text-gray-800">Fiches de Traitement</h5>
                                <p class="text-xs text-gray-600">
                                    {{ count($fichesPatient) }} fiche(s)
                                </p>
                            </div>
                        </div>
                        <svg class="h-5 w-5 text-gray-600 transform transition-transform {{ $accordeonOuvert === 'fiches' ? 'rotate-180' : '' }}"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    @if($accordeonOuvert === 'fiches')
                    <div class="mt-2 space-y-2">
                        @php
                            // Grouper les fiches par facture
                            $fichesParFacture = collect($fichesPatient)->groupBy('fkidfacture');
                        @endphp
                        @forelse($fichesParFacture as $factureId => $fiches)
                            @php
                                $premiereFiche = $fiches->first();
                                $facture = $premiereFiche['facture'] ?? null;
                            @endphp
                            <div class="bg-white border border-blue-200 rounded-lg p-3 hover:shadow-md transition-shadow mb-3">
                                <div class="flex justify-between items-start mb-2">
                                    <div class="flex-1">
                                        <p class="text-xs text-gray-500 mb-2">
                                            <strong>Facture:</strong> {{ $facture['Nfacture'] ?? 'N°' . $factureId }} - 
                                            {{ $facture['DtFacture'] ? \Carbon\Carbon::parse($facture['DtFacture'])->format('d/m/Y') : '' }}
                                        </p>
                                        <div class="space-y-1 mb-2">
                                            @foreach($fiches as $fiche)
                                            <div class="text-sm">
                                                <span class="font-medium text-gray-800">
                                                    {{ $fiche['dateTraite'] ? \Carbon\Carbon::parse($fiche['dateTraite'])->format('d/m/Y') : '' }} - 
                                                    {{ $fiche['Traitement'] ?? $fiche['Acte'] ?? 'Traitement' }}
                                                </span>
                                                @if($fiche['Prix'])
                                                <span class="text-gray-600 ml-2">- {{ number_format($fiche['Prix'], 2) }} MRU</span>
                                                @endif
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="flex gap-2">
                                        <button wire:click="imprimerFiche({{ $factureId }})"
                                                class="text-blue-600 hover:text-blue-800 transition-colors"
                                                title="Imprimer la fiche médicale">
                                            <i class="fas fa-print"></i>
                                        </button>
                                        @if(count($fiches) === 1)
                                        <button wire:click="supprimerFiche({{ $premiereFiche['idFicheTraitement'] }})"
                                                class="text-red-600 hover:text-red-800 transition-colors"
                                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette fiche ?')"
                                                title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                        <p class="text-sm text-gray-500 text-center py-4">Aucune fiche de traitement</p>
                        @endforelse
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- L'écouteur open-receipt est géré globalement dans le layout principal pour éviter les doublons --}}
</div>

