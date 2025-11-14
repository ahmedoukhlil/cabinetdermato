<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Ordonnanceref;
use App\Models\Ordonnance;
use App\Models\Medicament;
use App\Models\Patient;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class OrdonnanceManager extends Component
{
    // Propriétés du patient
    public $patient;
    public $patientId;

    // Type d'ordonnance à créer (1=Médicaments, 2=Analyses, 3=Radios)
    public $typeOrdonnance = 1; // Par défaut : Médicaments

    // Lignes de l'ordonnance (peut contenir plusieurs lignes du même type)
    public $lignesOrdonnance = [];

    // Recherche pour chaque ligne (index => search term)
    public $searchTerms = [];

    // Résultats de recherche pour chaque ligne (index => [results])
    public $searchResults = [];

    // Affichage des résultats pour chaque ligne
    public $showSearchResults = [];

    // Liste des ordonnances du patient
    public $ordonnancesPatient = [];

    // Accordéons pour l'affichage
    public $accordeonOuvert = null; // 'medicaments', 'analyses', 'radios'

    // Référentiel des médicaments/analyses/radios (tous chargés)
    public $medicaments = [];
    public $analyses = [];
    public $radios = [];

    protected $listeners = [
        'refreshOrdonnances' => 'loadOrdonnancesPatient',
        'patientSelected' => 'updatePatient'
    ];

    protected $rules = [
        'typeOrdonnance' => 'required|in:1,2,3',
        'lignesOrdonnance.*.medicament_id' => 'required|exists:medicaments,IDMedic',
        'lignesOrdonnance.*.posologie' => 'nullable|string',
    ];

    protected $messages = [
        'typeOrdonnance.required' => 'Veuillez sélectionner un type d\'ordonnance',
        'lignesOrdonnance.*.medicament_id.required' => 'Veuillez sélectionner un élément',
        'lignesOrdonnance.*.medicament_id.exists' => 'L\'élément sélectionné n\'existe pas',
    ];

    public function mount($patient = null)
    {
        try {
            Log::info('OrdonnanceManager: mount appelé', ['patient' => $patient ? 'existe' : 'null']);
            
            if ($patient) {
                $this->patient = $patient;
                if (is_array($patient)) {
                    $this->patientId = $patient['ID'] ?? $patient['id'] ?? null;
                } elseif (is_object($patient)) {
                    $this->patientId = $patient->ID ?? $patient->id ?? null;
                }
                
                if ($this->patientId) {
                    $this->loadOrdonnancesPatient();
                }
            }

            // Initialiser une ligne vide
            $this->ajouterLigneVide();

            // Charger le référentiel avec cache
            $this->loadReferentielMedicaments();
            
            Log::info('OrdonnanceManager: mount terminé avec succès');
        } catch (\Exception $e) {
            Log::error('OrdonnanceManager: Erreur dans mount', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'Erreur lors du chargement du formulaire d\'ordonnance.');
        }
    }

    public function loadReferentielMedicaments()
    {
        $cacheKey = 'referentiel_medicaments_' . Auth::user()->fkidcabinet;
        
        $referentiel = Cache::remember($cacheKey, 3600, function () {
            return [
                'medicaments' => Medicament::where('fkidtype', 1)
                    ->orderBy('LibelleMedic')
                    ->get()
                    ->toArray(),
                'analyses' => Medicament::where('fkidtype', 2)
                    ->orderBy('LibelleMedic')
                    ->get()
                    ->toArray(),
                'radios' => Medicament::where('fkidtype', 3)
                    ->orderBy('LibelleMedic')
                    ->get()
                    ->toArray(),
            ];
        });

        $this->medicaments = $referentiel['medicaments'];
        $this->analyses = $referentiel['analyses'];
        $this->radios = $referentiel['radios'];
    }

    public function loadOrdonnancesPatient()
    {
        if (!$this->patientId) return;

        $this->ordonnancesPatient = Ordonnanceref::where('fkidpatient', $this->patientId)
            ->with(['ordonnances', 'prescripteur'])
            ->orderBy('dtPrescript', 'desc')
            ->get()
            ->toArray();
    }

    public function updatePatient($patient)
    {
        $this->patient = $patient;
        if (is_array($patient)) {
            $this->patientId = $patient['ID'] ?? $patient['id'] ?? null;
        } elseif (is_object($patient)) {
            $this->patientId = $patient->ID ?? $patient->id ?? null;
        }
        $this->loadOrdonnancesPatient();
    }

    public function changerTypeOrdonnance($type)
    {
        $this->typeOrdonnance = $type;
        // Réinitialiser les lignes quand on change de type
        $this->lignesOrdonnance = [];
        $this->searchTerms = [];
        $this->searchResults = [];
        $this->showSearchResults = [];
        $this->ajouterLigneVide();
    }

    public function ajouterLigneVide()
    {
        $index = count($this->lignesOrdonnance);
        $this->lignesOrdonnance[] = [
            'medicament_id' => '',
            'posologie' => '',
            'medicament_libelle' => '' // Pour afficher le libellé sélectionné
        ];
        $this->searchTerms[$index] = '';
        $this->searchResults[$index] = [];
        $this->showSearchResults[$index] = false;
    }

    public function supprimerLigne($index)
    {
        if (count($this->lignesOrdonnance) > 1) {
            unset($this->lignesOrdonnance[$index]);
            unset($this->searchTerms[$index]);
            unset($this->searchResults[$index]);
            unset($this->showSearchResults[$index]);
            
            // Réindexer les tableaux
            $this->lignesOrdonnance = array_values($this->lignesOrdonnance);
            $newSearchTerms = [];
            $newSearchResults = [];
            $newShowSearchResults = [];
            foreach ($this->lignesOrdonnance as $newIndex => $ligne) {
                $oldIndex = $newIndex < $index ? $newIndex : $newIndex + 1;
                $newSearchTerms[$newIndex] = $this->searchTerms[$oldIndex] ?? '';
                $newSearchResults[$newIndex] = $this->searchResults[$oldIndex] ?? [];
                $newShowSearchResults[$newIndex] = $this->showSearchResults[$oldIndex] ?? false;
            }
            $this->searchTerms = $newSearchTerms;
            $this->searchResults = $newSearchResults;
            $this->showSearchResults = $newShowSearchResults;
        }
    }

    public function updatedSearchTerms($value, $key)
    {
        // $key est au format "searchTerms.0", "searchTerms.1", etc.
        // Extraire l'index
        $index = (int) str_replace('searchTerms.', '', $key);
        
        if (strlen(trim($value)) >= 1) {
            $this->searchMedicament($index, $value);
        } else {
            $this->searchResults[$index] = [];
            $this->showSearchResults[$index] = false;
        }
    }

    public function searchMedicament($index, $searchTerm = null)
    {
        $search = trim($searchTerm ?? $this->searchTerms[$index] ?? '');
        
        if (empty($search)) {
            $this->searchResults[$index] = [];
            $this->showSearchResults[$index] = false;
            return;
        }

        // Récupérer la liste selon le type d'ordonnance
        $liste = match($this->typeOrdonnance) {
            1 => $this->medicaments,
            2 => $this->analyses,
            3 => $this->radios,
            default => []
        };

        // Filtrer les résultats
        $this->searchResults[$index] = array_filter($liste, function($item) use ($search) {
            return stripos($item['LibelleMedic'], $search) !== false;
        });

        // Limiter à 10 résultats
        $this->searchResults[$index] = array_slice($this->searchResults[$index], 0, 10);
        $this->showSearchResults[$index] = true;
    }

    public function selectMedicament($index, $medicamentId)
    {
        // Trouver le médicament dans la liste appropriée
        $liste = match($this->typeOrdonnance) {
            1 => $this->medicaments,
            2 => $this->analyses,
            3 => $this->radios,
            default => []
        };

        $medicament = collect($liste)->firstWhere('IDMedic', $medicamentId);

        if ($medicament) {
            $this->lignesOrdonnance[$index]['medicament_id'] = $medicament['IDMedic'];
            $this->lignesOrdonnance[$index]['medicament_libelle'] = $medicament['LibelleMedic'];
            $this->searchTerms[$index] = $medicament['LibelleMedic'];
            $this->searchResults[$index] = [];
            $this->showSearchResults[$index] = false;
        }
    }

    public function clearMedicamentSearch($index)
    {
        $this->lignesOrdonnance[$index]['medicament_id'] = '';
        $this->lignesOrdonnance[$index]['medicament_libelle'] = '';
        $this->searchTerms[$index] = '';
        $this->searchResults[$index] = [];
        $this->showSearchResults[$index] = false;
    }

    public function getListeMedicamentsProperty()
    {
        return match($this->typeOrdonnance) {
            1 => $this->medicaments,  // Médicaments
            2 => $this->analyses,      // Analyses
            3 => $this->radios,        // Radios
            default => []
        };
    }

    public function getTypeOrdonnanceLibelleProperty()
    {
        return match($this->typeOrdonnance) {
            1 => 'Ordonnance Médicale',
            2 => 'Ordonnance d\'Analyses',
            3 => 'Ordonnance de Radiologie',
            default => 'Ordonnance'
        };
    }

    public function sauvegarderOrdonnance()
    {
        // Filtrer les lignes vides
        $lignesValides = array_filter($this->lignesOrdonnance, function($ligne) {
            return !empty($ligne['medicament_id']);
        });

        if (empty($lignesValides)) {
            session()->flash('error', 'Veuillez ajouter au moins une ligne à l\'ordonnance.');
            return;
        }

        // Réindexer le tableau
        $lignesValides = array_values($lignesValides);

        $this->validate();

        try {
            DB::beginTransaction();

            // Générer la référence d'ordonnance
            $annee = now()->year;
            $lastNumOrdre = Ordonnanceref::where('Annee', $annee)
                ->where('fkidCabinet', Auth::user()->fkidcabinet)
                ->max('numordre') ?? 0;

            $numOrdre = $lastNumOrdre + 1;
            $refOrd = 'ORD-' . $annee . '-' . str_pad($numOrdre, 4, '0', STR_PAD_LEFT);

            // Créer l'ordonnance référence
            $ordonnanceRef = Ordonnanceref::create([
                'refOrd' => $refOrd,
                'Annee' => $annee,
                'numordre' => $numOrdre,
                'fkidpatient' => $this->patientId,
                'fkidprescripteur' => Auth::id(),
                'dtPrescript' => now(),
                'fkidCabinet' => Auth::user()->fkidcabinet,
                'TypeOrdonnance' => $this->typeOrdonnanceLibelle
            ]);

            // Créer les lignes d'ordonnance (plusieurs lignes du même type)
            $numOrdreLigne = 1;
            foreach ($lignesValides as $ligne) {
                $medicament = Medicament::find($ligne['medicament_id']);
                
                if ($medicament) {
                    Ordonnance::create([
                        'Libelle' => $medicament->LibelleMedic,
                        'DtPrescription' => now(),
                        'fkidrefOrd' => $ordonnanceRef->id,
                        'NumordreOrd' => $numOrdreLigne++,
                        'Utilisation' => $ligne['posologie'] ?? null,
                        'fkiduser' => Auth::id()
                    ]);
                }
            }

            DB::commit();

            session()->flash('message', 'Ordonnance créée avec succès.');
            $this->loadOrdonnancesPatient();
            $this->resetForm();

            // Émettre un événement pour rafraîchir la liste
            $this->emit('ordonnanceCreated', $ordonnanceRef->id);
            
            // Émettre un événement pour fermer le modal parent si nécessaire
            $this->emit('fermerOrdonnanceModal');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Erreur lors de la création de l\'ordonnance : ' . $e->getMessage());
            Log::error('Erreur création ordonnance', [
                'error' => $e->getMessage(),
                'patient_id' => $this->patientId,
                'type' => $this->typeOrdonnance
            ]);
        }
    }

    public function resetForm()
    {
        $this->lignesOrdonnance = [];
        $this->searchTerms = [];
        $this->searchResults = [];
        $this->showSearchResults = [];
        $this->typeOrdonnance = 1;
        $this->ajouterLigneVide();
    }

    public function toggleAccordeon($type)
    {
        if ($this->accordeonOuvert === $type) {
            $this->accordeonOuvert = null;
        } else {
            $this->accordeonOuvert = $type;
        }
    }

    public function getOrdonnancesByType($type)
    {
        if (empty($this->ordonnancesPatient)) return [];

        $result = [];
        foreach ($this->ordonnancesPatient as $ordonnanceRef) {
            // Vérifier le type d'ordonnance selon le libellé
            $typeOrdonnance = match($ordonnanceRef['TypeOrdonnance'] ?? '') {
                'Ordonnance Médicale' => 1,
                'Ordonnance d\'Analyses' => 2,
                'Ordonnance de Radiologie' => 3,
                default => null
            };

            if ($typeOrdonnance === $type && isset($ordonnanceRef['ordonnances'])) {
                $result[] = [
                    'ref' => $ordonnanceRef,
                    'ordonnances' => $ordonnanceRef['ordonnances']
                ];
            }
        }

        return $result;
    }

    public function imprimerOrdonnance($ordonnanceId)
    {
        // Ouvrir l'URL d'impression dans un nouvel onglet (identique à la logique du reçu de consultation)
        $url = route('ordonnance.print', ['id' => $ordonnanceId]);
        \Log::info('Émission de l\'événement open-receipt pour ordonnance', ['url' => $url, 'ordonnance_id' => $ordonnanceId]);
        $this->dispatchBrowserEvent('open-receipt', ['url' => $url]);
    }

    public function supprimerOrdonnance($ordonnanceId)
    {
        try {
            $ordonnanceRef = Ordonnanceref::find($ordonnanceId);

            if (!$ordonnanceRef) {
                session()->flash('error', 'Ordonnance introuvable.');
                return;
            }

            // Vérifier les permissions
            if ($ordonnanceRef->fkidprescripteur != Auth::id() && !Auth::user()->isDocteurProprietaire()) {
                session()->flash('error', 'Vous n\'avez pas la permission de supprimer cette ordonnance.');
                return;
            }

            DB::beginTransaction();

            // Supprimer les lignes
            Ordonnance::where('fkidrefOrd', $ordonnanceId)->delete();

            // Supprimer la référence
            $ordonnanceRef->delete();

            DB::commit();

            session()->flash('message', 'Ordonnance supprimée avec succès.');
            $this->loadOrdonnancesPatient();

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Erreur lors de la suppression : ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.ordonnance-manager');
    }
}
