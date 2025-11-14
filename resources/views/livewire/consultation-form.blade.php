<div>
    <div class="space-y-6">
        <div class="p-6 rounded-xl bg-primary text-white shadow-lg">
            <h2 class="text-xl font-bold">Nouvelle consultation</h2>
            <p class="text-primary-light mt-1">Créez une nouvelle consultation pour le patient sélectionné</p>
        </div>

        <div class="bg-white rounded-lg shadow-xl overflow-hidden">
            <div class="p-6">
                @if ($errors->any())
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                        <strong class="font-bold">Erreurs de validation :</strong>
                        <ul class="mt-2">
                            @foreach ($errors->all() as $error)
                                <li class="text-sm">• {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <!-- Sélection du patient -->
                        @if($patient)
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Patient sélectionné</label>
                                <div class="p-2 bg-primary-light rounded border border-primary">
                                    <span class="font-medium text-primary">
                                        {{ is_array($patient) ? ($patient['Prenom'] ?? '') : $patient->Prenom }}
                                    </span>
                                    @if(is_array($patient) ? ($patient['Telephone1'] ?? null) : ($patient->Telephone1 ?? null))
                                        <span class="text-sm text-primary ml-2">Tél: {{ is_array($patient) ? $patient['Telephone1'] : $patient->Telephone1 }}</span>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Patient sélectionné</label>
                                <div class="p-2 bg-gray-100 rounded border border-gray-300">
                                    <span class="text-gray-500">Veuillez sélectionner un patient depuis la page principale</span>
                                </div>
                            </div>
                        @endif

                        <!-- Sélection du médecin -->
                        <div>
                            <label for="medecin_id" class="block text-sm font-medium text-gray-700">Médecin</label>
                            <select id="medecin_id" wire:model.live="medecin_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Sélectionner un médecin</option>
                                @foreach($medecins as $medecin)
                                    <option value="{{ $medecin->idMedecin }}">Dr. {{ $medecin->Nom }}</option>
                                @endforeach
                            </select>
                            @error('medecin_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Mode de paiement -->
                        <div>
                            <label for="mode_paiement" class="block text-sm font-medium text-gray-700">Mode de paiement</label>
                            <select id="mode_paiement" wire:model.live="mode_paiement" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Sélectionner un mode de paiement</option>
                                @foreach($typesPaiement as $type)
                                    <option value="{{ $type }}">{{ $type }}</option>
                                @endforeach
                            </select>
                            @error('mode_paiement') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Montant de la consultation (en lecture seule) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Montant</label>
                            <div class="bg-gray-50 p-3 rounded-lg border border-gray-200 text-lg font-semibold text-gray-900">{{ number_format($montant, 2) }} MRU</div>
                        </div>
                    </div>

                    <!-- Debug Livewire variables -->
                    {{-- <pre>
                    tauxPEC={{ $tauxPEC ?? 'null' }}
                    nomAssureur={{ $nomAssureur ?? 'null' }}
                    selectedPatient={{ print_r($selectedPatient, true) }}
                    </pre> --}}

                    <!-- Affichage de l'assurance -->
                    @if($selectedPatient && $selectedPatient['Assureur'] > 0)
                        <div class="mt-2 p-3 bg-green-50 rounded-lg border border-green-200">
                            <div class="flex items-center space-x-4">
                                <span class="text-green-800 font-semibold">Patient assuré</span>
                                <span class="text-green-700">Assureur : {{ $selectedPatient['NomAssureur'] ?? '' }}</span>
                                <span class="text-green-700">Taux de prise en charge : {{ number_format(($selectedPatient['TauxPEC'] ?? 0) * 100, 0) }}%</span>
                            </div>
                            <div class="mt-2 grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-xs text-gray-600">Montant assurance</p>
                                    @php
                                        $montantAssurance = isset($selectedPatient['TauxPEC']) ? $montant * $selectedPatient['TauxPEC'] : 0;
                                    @endphp
                                    <p class="text-sm font-medium text-green-800">{{ number_format($montantAssurance, 2) }} MRU</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-600">Reste à payer</p>
                                    @php
                                        $montantPatient = isset($selectedPatient['TauxPEC']) ? $montant * (1 - $selectedPatient['TauxPEC']) : 0;
                                    @endphp
                                    <p class="text-sm font-medium text-green-800">{{ number_format($montantPatient, 2) }} MRU</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Bouton de soumission -->
                    <div class="mt-6 flex justify-end">
                        <button type="button" wire:click="save" class="px-4 py-2 bg-primary text-white rounded hover:bg-primary-dark">
                            Créer la consultation
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        [x-cloak] { display: none !important; }
        .form-control {
            @apply block w-full rounded-r-md border-gray-300 focus:border-blue-500 focus:ring-blue-500 sm:text-sm;
        }
        @media print {
            body * {
                visibility: hidden;
            }
            #receipt-iframe, #receipt-iframe * {
                visibility: visible;
            }
            #receipt-iframe {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
            }
        }
    </style>
    @endpush

    {{-- L'écouteur open-receipt est géré globalement dans le layout principal pour éviter les doublons --}} 