<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Fichetraitement;
use App\Models\Acte;
use App\Models\Patient;
use App\Models\Medecin;
use App\Models\Facture;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class DossierMedicalManager extends Component
{
    // Propriétés du patient
    public $patient;
    public $patientId;

    // Lignes de traitement (peut contenir plusieurs lignes)
    public $lignesTraitement = [];

    // Recherche pour chaque ligne (index => search term)
    public $searchTerms = [];

    // Résultats de recherche pour chaque ligne (index => [results])
    public $searchResults = [];

    // Affichage des résultats pour chaque ligne
    public $showSearchResults = [];

    // Liste des fiches de traitement du patient
    public $fichesPatient = [];

    // Accordéons pour l'affichage
    public $accordeonOuvert = null;

    // Référentiel des actes (chargés)
    public $actes = [];

    // Médecin sélectionné
    public $medecinId;

    // Indique si le médecin est verrouillé (ne peut pas être changé)
    public $medecinLocked = false;

    // Informations du médecin verrouillé
    public $medecinLockedData = null;

    // Facture sélectionnée
    public $factureId;

    // Indique si la facture est verrouillée (ne peut pas être changée)
    public $factureLocked = false;

    // Informations de la facture verrouillée
    public $factureLockedData = null;

    // Liste des factures du patient
    public $facturesPatient = [];

    protected $listeners = [
        'refreshFiches' => 'loadFichesPatient',
        'patientSelected' => 'updatePatient'
    ];

    protected $rules = [
        'lignesTraitement.*.acte_id' => 'required|exists:actes,ID',
        'lignesTraitement.*.traitement' => 'nullable|string|max:245',
        'lignesTraitement.*.prix' => 'nullable|numeric|min:0',
        'medecinId' => 'required|exists:medecins,idMedecin',
        'factureId' => 'required|exists:facture,Idfacture',
    ];

    protected $messages = [
        'lignesTraitement.*.acte_id.required' => 'Veuillez sélectionner un acte',
        'lignesTraitement.*.acte_id.exists' => 'L\'acte sélectionné n\'existe pas',
        'medecinId.required' => 'Veuillez sélectionner un médecin',
        'factureId.required' => 'Veuillez sélectionner une facture',
        'factureId.exists' => 'La facture sélectionnée n\'existe pas',
    ];

    public function mount($patient = null, $facture = null, $medecin = null)
    {
        try {
            Log::info('DossierMedicalManager: mount appelé', ['patient' => $patient ? 'existe' : 'null', 'facture' => $facture ? 'existe' : 'null', 'medecin' => $medecin ? 'existe' : 'null']);
            
            if ($patient) {
                $this->patient = $patient;
                if (is_array($patient)) {
                    $this->patientId = $patient['ID'] ?? $patient['id'] ?? null;
                } elseif (is_object($patient)) {
                    $this->patientId = $patient->ID ?? $patient->id ?? null;
                }
                
                if ($this->patientId) {
                    $this->loadFacturesPatient();
                    
                    // Si une facture est fournie, la pré-sélectionner et la verrouiller
                    if ($facture) {
                        if (is_array($facture)) {
                            $this->factureId = $facture['Idfacture'] ?? $facture['id'] ?? null;
                            $this->factureLockedData = $facture;
                        } elseif (is_object($facture)) {
                            $this->factureId = $facture->Idfacture ?? $facture->id ?? null;
                            $this->factureLockedData = $facture->toArray();
                        }
                        // Verrouiller la facture si elle est fournie en paramètre
                        $this->factureLocked = true;
                    }
                    
                    $this->loadFichesPatient();
                }
            }

            // Initialiser une ligne vide
            $this->ajouterLigneVide();

            // Charger le référentiel avec cache
            $this->loadReferentielActes();

            // Si un médecin est fourni, le pré-sélectionner et le verrouiller
            if ($medecin) {
                if (is_array($medecin)) {
                    $this->medecinId = $medecin['idMedecin'] ?? $medecin['id'] ?? null;
                    $this->medecinLockedData = $medecin;
                } elseif (is_object($medecin)) {
                    $this->medecinId = $medecin->idMedecin ?? $medecin->id ?? null;
                    $this->medecinLockedData = $medecin->toArray();
                }
                // Verrouiller le médecin si il est fourni en paramètre
                $this->medecinLocked = true;
            } else {
                // Définir le médecin par défaut (utilisateur connecté si c'est un médecin)
                $user = Auth::user();
                if ($user && isset($user->fkidmedecin) && $user->fkidmedecin) {
                    $this->medecinId = $user->fkidmedecin;
                }
            }
            
            Log::info('DossierMedicalManager: mount terminé avec succès');
        } catch (\Exception $e) {
            Log::error('DossierMedicalManager: Erreur dans mount', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'Erreur lors du chargement du formulaire de dossier médical.');
        }
    }

    public function loadReferentielActes()
    {
        $cacheKey = 'referentiel_actes_' . Auth::user()->fkidcabinet;
        
        $this->actes = Cache::remember($cacheKey, 3600, function () {
            return Acte::where('Masquer', 0)
                ->orderBy('Acte')
                ->get()
                ->toArray();
        });
    }

    public function loadFichesPatient()
    {
        if (!$this->patientId) return;

        // Charger les fiches liées aux factures du patient
        // Note: Les fiches sont liées au patient via les factures (fkidfacture)
        $query = Fichetraitement::where('fkidCabinet', Auth::user()->fkidcabinet)
            ->where('IsSupprimer', 0)
            ->whereHas('facture', function($query) {
                $query->where('IDPatient', $this->patientId);
            });
        
        // Si une facture spécifique est sélectionnée, filtrer par cette facture
        if ($this->factureId) {
            $query->where('fkidfacture', $this->factureId);
        }
        
        $this->fichesPatient = $query
            ->with(['facture.patient', 'acte', 'medecin'])
            ->orderBy('dateTraite', 'desc')
            ->orderBy('Ordre', 'asc')
            ->get()
            ->toArray();
    }

    public function loadFacturesPatient()
    {
        if (!$this->patientId) return;

        // Charger les factures du patient
        $this->facturesPatient = Facture::where('IDPatient', $this->patientId)
            ->where('fkidCabinet', Auth::user()->fkidcabinet)
            ->orderBy('DtFacture', 'desc')
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
        $this->loadFichesPatient();
        $this->loadFacturesPatient();
    }

    public function updatedFactureId()
    {
        // Recharger les fiches quand la facture change
        $this->loadFichesPatient();
    }

    public function ajouterLigneVide()
    {
        $index = count($this->lignesTraitement);
        $this->lignesTraitement[] = [
            'acte_id' => '',
            'acte_libelle' => '',
            'traitement' => '',
            'prix' => ''
        ];
        $this->searchTerms[$index] = '';
        $this->searchResults[$index] = [];
        $this->showSearchResults[$index] = false;
    }

    public function supprimerLigne($index)
    {
        if (count($this->lignesTraitement) > 1) {
            unset($this->lignesTraitement[$index]);
            unset($this->searchTerms[$index]);
            unset($this->searchResults[$index]);
            unset($this->showSearchResults[$index]);
            
            // Réindexer les tableaux
            $this->lignesTraitement = array_values($this->lignesTraitement);
            $newSearchTerms = [];
            $newSearchResults = [];
            $newShowSearchResults = [];
            foreach ($this->lignesTraitement as $newIndex => $ligne) {
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
            $this->searchActe($index, $value);
        } else {
            $this->searchResults[$index] = [];
            $this->showSearchResults[$index] = false;
        }
    }

    public function searchActe($index, $searchTerm = null)
    {
        $search = trim($searchTerm ?? $this->searchTerms[$index] ?? '');
        
        if (empty($search)) {
            $this->searchResults[$index] = [];
            $this->showSearchResults[$index] = false;
            return;
        }

        // Filtrer les résultats
        $this->searchResults[$index] = array_filter($this->actes, function($item) use ($search) {
            return stripos($item['Acte'], $search) !== false;
        });

        // Limiter à 10 résultats
        $this->searchResults[$index] = array_slice($this->searchResults[$index], 0, 10);
        $this->showSearchResults[$index] = true;
    }

    public function selectActe($index, $acteId)
    {
        $acte = collect($this->actes)->firstWhere('ID', $acteId);

        if ($acte) {
            $this->lignesTraitement[$index]['acte_id'] = $acte['ID'];
            $this->lignesTraitement[$index]['acte_libelle'] = $acte['Acte'];
            $this->lignesTraitement[$index]['prix'] = $acte['PrixRef'] ?? 0;
            $this->searchTerms[$index] = $acte['Acte'];
            $this->searchResults[$index] = [];
            $this->showSearchResults[$index] = false;
        }
    }

    public function clearActeSearch($index)
    {
        $this->lignesTraitement[$index]['acte_id'] = '';
        $this->lignesTraitement[$index]['acte_libelle'] = '';
        $this->lignesTraitement[$index]['prix'] = '';
        $this->searchTerms[$index] = '';
        $this->searchResults[$index] = [];
        $this->showSearchResults[$index] = false;
    }

    public function sauvegarderFiche()
    {
        // Filtrer les lignes vides
        $lignesValides = array_filter($this->lignesTraitement, function($ligne) {
            return !empty($ligne['acte_id']);
        });

        if (empty($lignesValides)) {
            session()->flash('error', 'Veuillez ajouter au moins une ligne de traitement.');
            return;
        }

        // Réindexer le tableau
        $lignesValides = array_values($lignesValides);

        $this->validate();

        try {
            DB::beginTransaction();

            $medecin = Medecin::find($this->medecinId);
            if (!$medecin) {
                throw new \Exception('Médecin introuvable.');
            }

            // Vérifier que la facture existe et appartient au patient
            $facture = Facture::where('Idfacture', $this->factureId)
                ->where('IDPatient', $this->patientId)
                ->where('fkidCabinet', Auth::user()->fkidcabinet)
                ->first();

            if (!$facture) {
                throw new \Exception('Facture introuvable ou n\'appartient pas au patient.');
            }

            // Créer les fiches de traitement (une par ligne) liées à la facture
            $ordre = 1;
            foreach ($lignesValides as $ligne) {
                $acte = Acte::find($ligne['acte_id']);
                
                if ($acte) {
                    Fichetraitement::create([
                        'fkidfacture' => $this->factureId,
                        'fkidacte' => $acte->ID,
                        'fkidmedecin' => $this->medecinId,
                        'Acte' => $acte->Acte,
                        'Traitement' => $ligne['traitement'] ?? '',
                        'Prix' => $ligne['prix'] ?? $acte->PrixRef ?? 0,
                        'dateTraite' => now(),
                        'NomMedecin' => $medecin->Nom ?? 'Médecin',
                        'Ordre' => $ordre++,
                        'IsImprimer' => 0,
                        'IsSupprimer' => 0,
                        'fkidCabinet' => Auth::user()->fkidcabinet
                    ]);
                }
            }

            DB::commit();

            session()->flash('message', 'Fiche de traitement créée avec succès.');
            $this->loadFichesPatient();
            $this->resetForm();
            
            // Émettre un événement pour rafraîchir la liste
            $this->emit('ficheCreated');
            
            // Émettre un événement pour fermer le modal parent si nécessaire
            $this->emit('fermerDossierMedicalModal');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Erreur lors de la création de la fiche : ' . $e->getMessage());
            Log::error('Erreur création fiche traitement', [
                'error' => $e->getMessage(),
                'patient_id' => $this->patientId,
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    public function resetForm()
    {
        $this->lignesTraitement = [];
        $this->searchTerms = [];
        $this->searchResults = [];
        $this->showSearchResults = [];
        $this->factureId = null;
        $this->ajouterLigneVide();
    }

    public function toggleAccordeon()
    {
        if ($this->accordeonOuvert === 'fiches') {
            $this->accordeonOuvert = null;
        } else {
            $this->accordeonOuvert = 'fiches';
        }
    }

    public function imprimerFiche($factureId)
    {
        // Ouvrir l'URL d'impression dans un nouvel onglet (identique à la logique du reçu de consultation)
        $url = route('dossier-medical.print', ['factureId' => $factureId]);
        \Log::info('Émission de l\'événement open-receipt pour fiche médicale', ['url' => $url, 'facture_id' => $factureId]);
        $this->dispatchBrowserEvent('open-receipt', ['url' => $url]);
    }

    public function supprimerFiche($ficheId)
    {
        try {
            $fiche = Fichetraitement::find($ficheId);

            if (!$fiche) {
                session()->flash('error', 'Fiche introuvable.');
                return;
            }

            // Vérifier les permissions
            if ($fiche->fkidCabinet != Auth::user()->fkidcabinet) {
                session()->flash('error', 'Vous n\'avez pas la permission de supprimer cette fiche.');
                return;
            }

            DB::beginTransaction();

            // Marquer comme supprimé plutôt que supprimer physiquement
            $fiche->IsSupprimer = 1;
            $fiche->save();

            DB::commit();

            session()->flash('message', 'Fiche supprimée avec succès.');
            $this->loadFichesPatient();

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Erreur lors de la suppression : ' . $e->getMessage());
        }
    }

    public function render()
    {
        // Charger les médecins pour le select
        $medecins = Cache::remember('medecins_' . Auth::user()->fkidcabinet, 3600, function() {
            return Medecin::where('fkidCabinet', Auth::user()->fkidcabinet)
                ->orderBy('Nom')
                ->get();
        });

        return view('livewire.dossier-medical-manager', [
            'medecins' => $medecins
        ]);
    }
}

