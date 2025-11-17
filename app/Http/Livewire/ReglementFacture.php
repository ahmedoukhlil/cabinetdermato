<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Facture;
use App\Models\Patient;
use App\Models\CaisseOperation;
use App\Models\Medecin;
use App\Models\RefTypePaiement;
use Illuminate\Support\Facades\DB;
use App\Models\Detailfacturepatient;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Cache;
use App\Models\Facturepatient;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\StockMedicament;
use App\Models\LotMedicament;
use App\Models\MouvementStock;

class ReglementFacture extends Component
{
    use WithPagination;

    public $selectedPatient = null;
    protected $factures;
    public $factureSelectionnee;
    public $montantReglement;
    public $modePaiement;
    public $modesPaiement;
    public $dernierReglement = null;
    public $pourQui;
    public $showAddActeForm = false;
    public $selectedActeId = '';
    public $prixReference;
    public $prixFacture;
    public $quantite = 1;
    public $seance;
    public $factureIdForActe = null;
    public $actes = [];
    public $searchActe = '';
    public $filteredActes = [];
    public $acteSelectionne = false;
    public $showReglementModal = false;
    protected $facturesEnAttente;
    protected $currentPage = 1;
    public $showMedecinModal = false;
    public $selectedMedecinId = null;
    public $medecins = [];
    public $searchMedecin = '';
    public $showDossierMedicalModal = false;
    public $factureIdForDossier = null;
    public $factureDossier = null;
    public $patientDossier = null;
    
    // Propriétés pour médicaments/analyses/radios
    public $showAddMedicamentForm = false;
    public $selectedMedicamentId = '';
    public $selectedMedicamentType = ''; // 'medicament', 'analyse', 'radio'
    public $prixReferenceMedicament;
    public $prixFactureMedicament;
    public $quantiteMedicament = 1;
    public $seanceMedicament;

    protected $listeners = [
        'patientSelected' => 'handlePatientSelected',
        'acteSelected' => 'handleActeSelected',
        'medicamentSelected' => 'handleMedicamentSelected',
        'closeModal' => 'closeAddActeForm',
        'reglementDepuisPharmacie' => 'filtrerFacturesPharmacie',
        'ouvrirFacturePharmacie' => 'ouvrirFacturePharmacie'
    ];

    // Flag pour indiquer que le règlement vient de l'onglet Pharmacie
    public $depuisPharmacie = false;
    
    // Onglet actif dans l'interface (actes ou pharmacie)
    public $activeTabInterface = 'actes'; // 'actes' ou 'pharmacie'

    public function getFacturesProperty()
    {
        if ($this->selectedPatient) {
            $patientId = is_array($this->selectedPatient) ? ($this->selectedPatient['ID'] ?? null) : ($this->selectedPatient->ID ?? null);
            if (!$patientId) {
                return null;
            }
            
            // Utiliser un cache court (5 minutes) pour les factures car elles peuvent changer
            $cacheKey = 'factures_patient_' . $patientId . '_page_' . $this->currentPage . '_tab_' . $this->activeTabInterface;
            return Cache::remember($cacheKey, 300, function() use ($patientId) {
                $query = Facture::where('IDPatient', $patientId);
                
                // Filtrer selon l'onglet actif
                if ($this->activeTabInterface === 'pharmacie') {
                    // Uniquement les factures de médicaments (IsAct = 2)
                    $query->whereHas('details', function($q) {
                        $q->where('IsAct', 2);
                    })->whereDoesntHave('details', function($q) {
                        $q->where('IsAct', '!=', 2);
                    });
                } else {
                    // Uniquement les factures d'actes (IsAct = 1)
                    $query->whereHas('details', function($q) {
                        $q->where('IsAct', 1);
                    })->whereDoesntHave('details', function($q) {
                        $q->where('IsAct', '!=', 1);
                    });
                }
                
                return $query->with([
                        'medecin' => function($query) {
                            $query->select('idMedecin', 'Nom');
                        }
                        // Ne pas charger les détails ici - ils seront chargés seulement quand une facture est sélectionnée
                    ])
                    ->select([
                        'Idfacture', 'Nfacture', 'FkidMedecinInitiateur', 'DtFacture',
                        'TotFacture', 'ISTP', 'TXPEC', 'TotalPEC', 'ReglementPEC',
                        'TotalfactPatient', 'TotReglPatient'
                    ])
                    ->orderBy('DtFacture', 'desc')
                    ->paginate(10, ['*'], 'page', $this->currentPage);
            });
        }
        return null;
    }

    public function filtrerFacturesPharmacie()
    {
        $this->depuisPharmacie = true;
        $this->activeTabInterface = 'pharmacie';
        $this->showAddActeForm = false; // Masquer le formulaire d'ajout d'actes
        $this->showAddMedicamentForm = false; // Masquer le formulaire d'ajout de médicaments
        $this->loadFactures();
    }

    /**
     * Ouvrir une facture spécifique de pharmacie
     * @param int|null $factureId ID de la facture à ouvrir (optionnel)
     */
    public function ouvrirFacturePharmacie($factureId = null)
    {
        $this->depuisPharmacie = true;
        $this->activeTabInterface = 'pharmacie';
        $this->showAddActeForm = false;
        $this->showAddMedicamentForm = false;
        
        // Charger les factures
        $this->loadFactures();
        
        // Si une facture ID est fournie, la sélectionner directement et ouvrir le modal
        if ($factureId) {
            // Sélectionner la facture directement
            $this->selectionnerFacture($factureId);
            
            // Ouvrir le modal de règlement (identique pour actes et pharmacie)
            $this->showReglementModal = true;
        }
    }
    
    public function switchTabInterface($tab)
    {
        $this->activeTabInterface = $tab;
        if ($tab === 'pharmacie') {
            $this->depuisPharmacie = true;
        } else {
            $this->depuisPharmacie = false;
        }
        $this->loadFactures();
    }

    public function mount($selectedPatient = null)
    {
        $this->showMedecinModal = false;
        
        // Utiliser le cache pour les modes de paiement (rarement modifiés)
        $cacheKeyModesPaiement = 'modes_paiement_' . Auth::user()->fkidcabinet;
        $this->modesPaiement = Cache::remember($cacheKeyModesPaiement, 3600, function() {
            return RefTypePaiement::all();
        });
        
        // Utiliser le cache pour les actes (rarement modifiés)
        $cacheKeyActes = 'actes_list_' . Auth::user()->fkidcabinet;
        $this->actes = Cache::remember($cacheKeyActes, 3600, function() {
            return \App\Models\Acte::where('Masquer', 0)->get();
        });
        
        $this->seance = 'Dent';
        if ($selectedPatient) {
            if (is_object($selectedPatient)) {
                $selectedPatient = (array) $selectedPatient;
            }
            $this->selectedPatient = $selectedPatient;
            // Ne pas charger les factures immédiatement, elles seront chargées lors du render
            // $this->loadFactures();
        }
        // Ne pas charger les factures en attente au mount (non utilisé dans le modal)
        // $this->loadFacturesEnAttente();
    }

    public function handlePatientSelected($patient)
    {
        $this->showMedecinModal = false;
        $this->selectedPatient = $patient;
        $this->loadFactures();
    }

    public function handleActeSelected($id, $prixReference)
    {
        $this->selectedActeId = $id;
        $this->prixReference = $prixReference;
        $this->prixFacture = $prixReference;
    }

    public function loadFactures()
    {
        if ($this->selectedPatient) {
            $patientId = is_array($this->selectedPatient) ? ($this->selectedPatient['ID'] ?? null) : ($this->selectedPatient->ID ?? null);
            // Invalider le cache pour toutes les pages de ce patient
            if ($patientId) {
                // Invalider toutes les pages possibles (on peut optimiser en gardant trace des pages)
                for ($page = 1; $page <= 10; $page++) {
                    Cache::forget('factures_patient_' . $patientId . '_page_' . $page);
                }
            }
            $this->factures = $this->getFacturesProperty();
        } else {
            $this->factures = null;
        }
    }

    public function selectionnerFacture($factureId)
    {
        // S'assurer que les factures sont chargées
        if (!$this->factures && $this->selectedPatient) {
            $this->factures = $this->getFacturesProperty();
        }

        // Utiliser les données déjà chargées dans la pagination
        $facture = null;
        if ($this->factures) {
            $facture = $this->factures->firstWhere('Idfacture', $factureId);
        }
        
        // Toujours charger la facture avec ses détails depuis la base pour être sûr d'avoir les bonnes données
        if (!$facture || !$facture->relationLoaded('details')) {
            // Charger la facture avec tous ses détails depuis la base
            $facture = Facture::with(['medecin', 'details'])->find($factureId);
        } else {
            // Si la facture est dans la pagination mais les détails ne sont pas chargés, les charger
            if (!$facture->relationLoaded('details')) {
                $facture->load('details');
            }
        }
        
        if ($facture) {
            // S'assurer que les détails sont bien chargés
            if (!$facture->relationLoaded('details')) {
                $facture->load('details');
            }
            
            // Vérifier si c'est une facture de pharmacie (uniquement des médicaments)
            // Utiliser la collection chargée plutôt que la requête
            $details = $facture->details;
            $hasMedicaments = $details->where('IsAct', 2)->isNotEmpty();
            $hasActes = $details->where('IsAct', 1)->isNotEmpty();
            $estFacturePharmacie = $hasMedicaments && !$hasActes;

            $this->factureSelectionnee = [
                'id' => $facture->Idfacture,
                'numero' => $facture->Nfacture,
                'medecin' => $facture->medecin ? ['Nom' => $facture->medecin->Nom] : ['Nom' => Auth::user()->NomComplet ?? Auth::user()->name],
                'montant_total' => $facture->TotFacture ?? 0,
                'montant_pec' => floatval($facture->TotalPEC ?? 0),
                'part_patient' => $facture->TotalfactPatient ?? 0,
                'montant_reglements_patient' => $facture->TotReglPatient ?? 0,
                'montant_reglements_pec' => $facture->ReglementPEC ?? 0,
                'reste_a_payer' => (($facture->ISTP > 0 ? ($facture->TotalfactPatient ?? 0) : ($facture->TotFacture ?? 0)) - ($facture->TotReglPatient ?? 0)),
                'reste_a_payer_pec' => $facture->ISTP > 0 ? (($facture->TotalPEC ?? 0) - ($facture->ReglementPEC ?? 0)) : 0,
                'TXPEC' => $facture->TXPEC ?? 0,
                'ISTP' => $facture->ISTP ?? 0,
                'est_reglee' => ((($facture->ISTP > 0 ? ($facture->TotalfactPatient ?? 0) : ($facture->TotFacture ?? 0)) - ($facture->TotReglPatient ?? 0)) <= 0) && ($facture->ISTP > 0 ? (($facture->TotalPEC ?? 0) - ($facture->ReglementPEC ?? 0)) <= 0 : true),
                'est_facture_pharmacie' => $estFacturePharmacie
            ];
            
            // Pour les factures de pharmacie, pré-remplir avec le montant restant complet
            if ($estFacturePharmacie) {
                $this->montantReglement = $this->factureSelectionnee['reste_a_payer'];
            } else {
                // Si la facture est déjà réglée, on permet d'ajouter un montant positif
                if ($this->factureSelectionnee['reste_a_payer'] >= $this->factureSelectionnee['part_patient']) {
                    $this->montantReglement = 0;
                } else {
                    $this->montantReglement = $this->factureSelectionnee['part_patient'] - $this->factureSelectionnee['reste_a_payer'];
                }
            }

            // Détection assuré ou non
            if ($facture->ISTP == 1) {
                $this->pourQui = 'patient'; // valeur par défaut
            } else {
                $this->pourQui = null;
            }
        }
    }

    public function enregistrerReglement()
    {
        $this->validate([
            'montantReglement' => 'required|numeric',
            'modePaiement' => 'required|exists:ref_type_paiement,idtypepaie',
        ]);
        // Si assuré, on doit avoir pourQui
        if ($this->pourQui === null && $this->factureSelectionnee && ($facture = Facture::find($this->factureSelectionnee['id'])) && $facture->ISTP == 1) {
            throw new \Exception('Veuillez préciser pour qui est le règlement (Patient ou PEC).');
        }
        
        try {
            DB::beginTransaction();

            $facture = Facture::find($this->factureSelectionnee['id']);
            if (!$facture) {
                throw new \Exception('Facture non trouvée');
            }

            $medecin = Medecin::find($facture->FkidMedecinInitiateur);
            if (!$medecin) {
                throw new \Exception('Médecin non trouvé');
            }

            $typePaiement = RefTypePaiement::find($this->modePaiement);
            if (!$typePaiement) {
                throw new \Exception('Mode de paiement non trouvé');
            }

            // Vérifier le stock avant le règlement si la facture contient des médicaments
            $resteAPayerAvant = ($facture->ISTP > 0 ? ($facture->TotalfactPatient ?? 0) : ($facture->TotFacture ?? 0)) - ($facture->TotReglPatient ?? 0);
            $resteAPayerPECAvant = $facture->ISTP > 0 ? (($facture->TotalPEC ?? 0) - ($facture->ReglementPEC ?? 0)) : 0;
            $seraCompletementPayee = ($resteAPayerAvant - $this->montantReglement <= 0) && ($resteAPayerPECAvant <= 0);
            
            // Si la facture sera complètement payée après ce règlement, vérifier le stock
            if ($seraCompletementPayee) {
                $detailsMedicaments = Detailfacturepatient::where('fkidfacture', $facture->Idfacture)
                    ->where('IsAct', 2)
                    ->whereNotNull('fkidmedicament')
                    ->get();
                
                // Vérifier si le stock a déjà été déduit
                $mouvementsExistants = MouvementStock::where('fkidFacture', $facture->Idfacture)
                    ->where('typeMouvement', 'SORTIE')
                    ->exists();
                
                if (!$mouvementsExistants && $detailsMedicaments->isNotEmpty()) {
                    // Vérifier le stock disponible pour tous les médicaments en utilisant la méthode centralisée
                    foreach ($detailsMedicaments as $detail) {
                        try {
                            $infoStock = $this->verifierStockDisponible(
                                $detail->fkidmedicament,
                                $detail->Quantite,
                                $facture->IDPatient,
                                $facture->Idfacture
                            );
                            
                            // Vérifier que le stock disponible est suffisant
                            if ($infoStock['disponible'] < $detail->Quantite) {
                                throw new \Exception('Stock insuffisant pour le médicament "' . $detail->Actes . '". Stock disponible: ' . number_format($infoStock['disponible'], 0) . ' (Stock total: ' . number_format($infoStock['stock']->quantiteStock, 0) . ', Déjà facturé non payé: ' . number_format($infoStock['quantiteDejaFacturee'], 0) . '). Le stock ne sera déduit qu\'après le paiement complet de la facture.');
                            }
                        } catch (\Exception $e) {
                            // Si c'est une erreur de stock, la propager
                            if (strpos($e->getMessage(), 'Stock') !== false) {
                                throw $e;
                            }
                            // Sinon, relancer l'exception
                            throw new \Exception('Erreur lors de la vérification du stock pour "' . $detail->Actes . '": ' . $e->getMessage());
                        }
                    }
                }
            }

            // Vérifier si c'est une facture de pharmacie (uniquement des médicaments)
            $estFacturePharmacie = $facture->details()
                ->where('IsAct', 2)
                ->exists() && !$facture->details()
                ->where('IsAct', 1)
                ->exists();

            // Pour les factures de pharmacie, le paiement doit être complet
            if ($estFacturePharmacie) {
                $resteAPayer = $this->factureSelectionnee['reste_a_payer'];
                if ($this->montantReglement < $resteAPayer) {
                    throw new \Exception('Les factures de pharmacie doivent être payées en totalité. Montant restant: ' . number_format($resteAPayer, 0) . ' MRU');
                }
                if ($this->montantReglement > $resteAPayer) {
                    throw new \Exception('Le montant du règlement ne peut pas dépasser le montant restant. Montant restant: ' . number_format($resteAPayer, 0) . ' MRU');
                }
            }

            $isRemboursement = $this->montantReglement < 0;
            $isAcompte = $this->montantReglement > $this->factureSelectionnee['reste_a_payer'];
            $montantOperation = $this->montantReglement;
            $montantAbsolu = abs($montantOperation);

            $operation = CaisseOperation::create([
                'dateoper' => now(),
                'MontantOperation' => $montantOperation,
                'designation' => ($isRemboursement ? 'Remboursement' : ($isAcompte ? 'Acompte' : 'Règlement')) . ' facture N°' . $facture->Nfacture,
                'fkidTiers' => $this->selectedPatient['ID'],
                'entreEspece' => $isRemboursement ? 0 : $montantAbsolu,
                'retraitEspece' => $isRemboursement ? $montantAbsolu : 0,
                'pourPatFournisseur' => 0,
                'pourCabinet' => 1,
                'fkiduser' => auth()->id(),
                'exercice' => now()->year,
                'fkIdTypeTiers' => 1,
                'fkidfacturebord' => $facture->Idfacture,
                'DtCr' => now(),
                'fkidcabinet' => auth()->user()->fkidcabinet,
                'fkidtypePaie' => $this->modePaiement,
                'TypePAie' => $typePaiement->LibPaie,
                'fkidmedecin' => $facture->FkidMedecinInitiateur,
                'medecin' => $medecin->Nom
            ]);

            // Mise à jour de la facture selon le type de règlement
            if ($facture->ISTP == 1 && $this->pourQui === 'pec') {
                $facture->ReglementPEC = ($facture->ReglementPEC ?? 0) + $montantOperation;
            } else {
                $facture->TotReglPatient = ($facture->TotReglPatient ?? 0) + $montantOperation;
            }
            $facture->save();
            
            // Recharger la facture pour avoir les valeurs à jour
            $facture->refresh();

            // Vérifier si la facture est maintenant complètement payée
            $resteAPayerPatient = ($facture->ISTP > 0 ? ($facture->TotalfactPatient ?? 0) : ($facture->TotFacture ?? 0)) - ($facture->TotReglPatient ?? 0);
            $resteAPayerPEC = $facture->ISTP > 0 ? (($facture->TotalPEC ?? 0) - ($facture->ReglementPEC ?? 0)) : 0;
            $estCompletementPayee = $resteAPayerPatient <= 0 && $resteAPayerPEC <= 0;

            // Si la facture est complètement payée, déduire le stock des médicaments
            if ($estCompletementPayee) {
                $this->deduireStockFacture($facture);
            }

            DB::commit();

            $this->dernierReglement = [
                'facture' => $facture,
                'patient' => $this->selectedPatient,
                'montant' => $montantOperation,
                'mode' => $typePaiement->LibPaie,
                'date' => now()->format('d/m/Y H:i'),
                'medecin' => $medecin->Nom,
                'operation' => $operation,
                'isRemboursement' => $isRemboursement,
                'isAcompte' => $isAcompte
            ];

            $this->reset(['montantReglement', 'modePaiement', 'factureSelectionnee', 'pourQui']);
            $this->loadFactures();
            session()->flash('message', ($isRemboursement ? 'Remboursement' : ($isAcompte ? 'Acompte' : 'Règlement')) . ' enregistré avec succès.');

            $receiptUrl = route('reglement-facture.receipt', $operation->getKey());
            $this->dispatchBrowserEvent('open-receipt', ['url' => $receiptUrl]);
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Une erreur est survenue lors de l\'enregistrement : ' . $e->getMessage());
        }
    }

    public function resetAddActeForm()
    {
        $this->selectedActeId = '';
        $this->prixReference = null;
        $this->prixFacture = null;
        $this->quantite = 1;
        $this->seance = 'Dent';
        $this->acteSelectionne = false;
    }

    public function resetAddMedicamentForm()
    {
        $this->selectedMedicamentId = '';
        $this->selectedMedicamentType = '';
        $this->prixReferenceMedicament = null;
        $this->prixFactureMedicament = null;
        $this->quantiteMedicament = 1;
        $this->seanceMedicament = '';
    }

    public function updatedSelectedMedicamentType($value)
    {
        // Réinitialiser la sélection du médicament quand on change de type
        $this->selectedMedicamentId = '';
        $this->prixReferenceMedicament = null;
        $this->prixFactureMedicament = null;
    }

    public function handleMedicamentSelected($id, $prixRef, $fkidtype)
    {
        $this->selectedMedicamentId = $id;
        $this->selectedMedicamentType = (string)$fkidtype;
        $this->prixReferenceMedicament = $prixRef;
        $this->prixFactureMedicament = $prixRef;
    }

    public function updatedSelectedMedicamentId($value)
    {
        if (empty($value)) {
            $this->prixReferenceMedicament = null;
            $this->prixFactureMedicament = null;
            return;
        }

        $medicamentId = (int) $value;
        $medicament = \App\Models\Medicament::find($medicamentId);
        
        if ($medicament) {
            // Prix de référence (toujours depuis la table medicaments)
            $prixRef = $medicament->PrixRef ?? 0;
            
            $this->prixReferenceMedicament = $prixRef;
            $this->prixFactureMedicament = $prixRef; // Par défaut égal au prix de référence, mais peut être modifié
        } else {
            $this->prixReferenceMedicament = null;
            $this->prixFactureMedicament = null;
        }
    }

    public function selectMedicament($id, $type)
    {
        if (!$id) {
            $this->resetAddMedicamentForm();
            return;
        }

        $medicament = \App\Models\Medicament::find($id);
        if ($medicament && $medicament->fkidtype == $type) {
            $this->selectedMedicamentId = $medicament->IDMedic;
            $this->selectedMedicamentType = $type;
            $this->prixReferenceMedicament = 0;
            $this->prixFactureMedicament = 0;
        } else {
            $this->resetAddMedicamentForm();
        }
    }

    public function saveMedicamentToFacture()
    {
        $this->validate([
            'selectedMedicamentId' => 'required|exists:medicaments,IDMedic',
            'selectedMedicamentType' => 'required|in:1,2,3',
            'prixFactureMedicament' => 'required|numeric|min:0',
            'quantiteMedicament' => 'required|integer|min:1',
        ], [
            'selectedMedicamentType.required' => 'Veuillez sélectionner un type d\'opération',
            'selectedMedicamentId.required' => 'Veuillez sélectionner un item',
        ]);

        try {
            DB::beginTransaction();

            $medicament = \App\Models\Medicament::find($this->selectedMedicamentId);
            
            // Convertir selectedMedicamentType en integer pour la comparaison
            $typeInt = (int) $this->selectedMedicamentType;
            
            if (!$medicament || $medicament->fkidtype != $typeInt) {
                throw new \Exception('Médicament non trouvé ou type incorrect');
            }

            $isAct = $medicament->fkidtype + 1; // 2=Médicament, 3=Analyse, 4=Radio

            // Vérifier le stock avant d'ajouter à la facture (uniquement pour les médicaments)
            if ($isAct == 2) {
                $patientId = is_array($this->selectedPatient) ? ($this->selectedPatient['ID'] ?? null) : ($this->selectedPatient->ID ?? null);
                $infoStock = $this->verifierStockDisponible(
                    $this->selectedMedicamentId,
                    $this->quantiteMedicament,
                    $patientId,
                    $this->factureIdForActe
                );

                if ($infoStock['disponible'] < $this->quantiteMedicament) {
                    throw new \Exception('Stock insuffisant pour ce médicament. Stock disponible: ' . number_format($infoStock['disponible'], 0) . ' (Stock total: ' . number_format($infoStock['stock']->quantiteStock, 0) . ', Déjà facturé non payé: ' . number_format($infoStock['quantiteDejaFacturee'], 0) . ')');
                }
            }

            $detail = \App\Models\Detailfacturepatient::create([
                'fkidfacture' => $this->factureIdForActe,
                'DtAjout' => now(),
                'Actes' => $medicament->LibelleMedic,
                'PrixRef' => $this->prixReferenceMedicament ?? 0,
                'PrixFacture' => $this->prixFactureMedicament,
                'Quantite' => $this->quantiteMedicament,
                'fkidmedicament' => $this->selectedMedicamentId,
                'IsAct' => $isAct,
                'fkidMedecin' => $this->factureSelectionnee->FkidMedecinInitiateur ?? 1,
                'fkidcabinet' => Auth::user()->fkidcabinet ?? 1,
                'ActesArab' => 'NR',
                'Dents' => $this->seanceMedicament ?: 'Med',
            ]);

            // Le stock ne sera pas déduit ici, mais uniquement lors du paiement de la facture

            $facture = \App\Models\Facture::find($this->factureIdForActe);
            $prixFactureItem = $this->prixFactureMedicament * $this->quantiteMedicament;
            $txpec = $facture->TXPEC ?? 0;
            $nouveauTotFacture = ($facture->TotFacture ?? 0) + $prixFactureItem;
            $montantPEC = $prixFactureItem * $txpec;
            $totalPEC = ($facture->TotalPEC ?? 0) + $montantPEC;
            $totalfactPatient = $nouveauTotFacture - $totalPEC;
            $facture->TotFacture = $nouveauTotFacture;
            $facture->TotalPEC = $totalPEC;
            $facture->TotalfactPatient = $totalfactPatient;
            $facture->save();

            DB::commit();
            $this->showAddMedicamentForm = false;
            $typeLabel = match($medicament->fkidtype) {
                1 => 'Médicament',
                2 => 'Analyse',
                3 => 'Radio',
                default => 'Item'
            };
            session()->flash('message', $typeLabel . ' ajouté avec succès.');
            $this->loadFactures();

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Une erreur est survenue : ' . $e->getMessage());
        }
    }

    public function openAddMedicamentForm($factureId)
    {
        $this->factureIdForActe = $factureId;
        $this->resetAddMedicamentForm();
        $this->showAddMedicamentForm = true;
    }

    public function closeAddMedicamentForm()
    {
        $this->showAddMedicamentForm = false;
        $this->resetAddMedicamentForm();
    }

    public function updatedSelectedActeId($value)
    {
        if (empty($value)) {
            $this->prixReference = null;
            $this->prixFacture = null;
            return;
        }

        $acteId = (int) $value;
        $acte = \App\Models\Acte::find($acteId);

        if ($acte) {
            $this->prixReference = $acte->PrixRef;
            $this->prixFacture = $acte->PrixRef;
        } else {
            $this->prixReference = null;
            $this->prixFacture = null;
        }
    }

    public function updatedSearchActe($value)
    {
        if (!$this->acteSelectionne) {
            $this->filteredActes = \App\Models\Acte::where('Acte', 'like', '%' . $value . '%')->get();
        }
    }

    public function selectActe($id = null)
    {
        if (!$id) {
            $this->resetAddActeForm();
            return;
        }

        $acte = \App\Models\Acte::find($id);

        if ($acte) {
            $this->selectedActeId = $acte->ID;
            $this->prixReference = $acte->PrixRef;
            $this->prixFacture = $acte->PrixRef;
            $this->acteSelectionne = true;
        } else {
            $this->resetAddActeForm();
        }
    }

    public function saveActeToFacture()
    {
        $this->validate([
            'selectedActeId' => 'required|exists:actes,ID',
            'prixFacture' => 'required|numeric|min:0',
            'quantite' => 'required|integer|min:1',
            'seance' => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            $acte = \App\Models\Acte::find($this->selectedActeId);

            \App\Models\Detailfacturepatient::create([
                'fkidfacture' => $this->factureIdForActe,
                'DtAjout' => now(),
                'Actes' => $acte ? $acte->Acte : null,
                'PrixRef' => $this->prixReference,
                'PrixFacture' => $this->prixFacture,
                'Quantite' => $this->quantite,
                'fkidacte' => $this->selectedActeId,
                'IsAct' => 1, // Acte médical
                'Dents' => $this->seance ?: 'Dent',
                'fkidMedecin' => $this->factureSelectionnee->FkidMedecinInitiateur ?? 1,
                'fkidcabinet' => Auth::user()->fkidcabinet ?? 1,
            ]);

            // Mise à jour de la facture uniquement pour l'acte sélectionné
            $facture = \App\Models\Facture::find($this->factureIdForActe);
            $prixFactureActe = $this->prixFacture * $this->quantite;
            $txpec = $facture->TXPEC ?? 0;
            $nouveauTotFacture = ($facture->TotFacture ?? 0) + $prixFactureActe;
            $montantPEC = $prixFactureActe * $txpec;
            $totalPEC = ($facture->TotalPEC ?? 0) + $montantPEC;
            $totalfactPatient = $nouveauTotFacture - $totalPEC;
            $facture->TotFacture = $nouveauTotFacture;
            $facture->TotalPEC = $totalPEC;
            $facture->TotalfactPatient = $totalfactPatient;
            // Ne pas toucher à TotReglPatient
            $facture->save();

            DB::commit();
            $this->showAddActeForm = false;
            session()->flash('message', 'Acte ajouté avec succès.');
            $this->loadFactures(); // Recharger les factures pour voir les changements

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Une erreur est survenue lors de l\'ajout de l\'acte : ' . $e->getMessage());
        }
    }

    public function openAddActeForm($factureId)
    {
        $this->factureIdForActe = $factureId;
        $this->resetAddActeForm();
        $this->showAddActeForm = true;
    }

    public function closeAddActeForm()
    {
        $this->showAddActeForm = false;
        $this->resetAddActeForm();
    }

    public function closeReglementForm()
    {
        $this->factureSelectionnee = null;
        $this->montantReglement = null;
        $this->modePaiement = null;
        $this->pourQui = null;
        $this->showReglementModal = false;
        $this->depuisPharmacie = false; // Réinitialiser le flag
        $this->loadFactures(); // Recharger les factures sans filtre
    }

    public function setConsultationActe()
    {
        $acte = \App\Models\Acte::where('Acte', 'like', '%consultation%')
            ->orWhere('Acte', 'like', '%CONSULTATION%')
            ->first();

        if ($acte) {
            $this->selectedActeId = $acte->ID;
            $this->prixReference = $acte->PrixRef;
            $this->prixFacture = $acte->PrixRef;
        }
    }

    public function ouvrirReglementFacture($factureId)
    {
        $this->selectionnerFacture($factureId);
        
        // Utiliser le même modal pour actes et pharmacie
        $this->showReglementModal = true;
    }

    public function removeActe($detailId)
    {
        try {
            DB::beginTransaction();

            // Récupérer le détail de l'acte
            $detail = Detailfacturepatient::find($detailId);
            if (!$detail) {
                throw new \Exception('Acte non trouvé');
            }

            // Récupérer la facture
            $facture = Facture::find($detail->fkidfacture);
            if (!$facture) {
                throw new \Exception('Facture non trouvée');
            }

            // Sauvegarder le type avant suppression
            $isMedicament = $detail->IsAct == 2;
            $medicamentId = $detail->fkidmedicament;
            $quantiteMedicament = $detail->Quantite;

            // Si c'est un médicament (IsAct = 2) et que la facture a été payée, vérifier si le stock a été déduit
            if ($isMedicament && $medicamentId) {
                $resteAPayerPatient = ($facture->ISTP > 0 ? ($facture->TotalfactPatient ?? 0) : ($facture->TotFacture ?? 0)) - ($facture->TotReglPatient ?? 0);
                $resteAPayerPEC = $facture->ISTP > 0 ? (($facture->TotalPEC ?? 0) - ($facture->ReglementPEC ?? 0)) : 0;
                $estPayee = $resteAPayerPatient <= 0 && $resteAPayerPEC <= 0;
                
                // Vérifier si le stock a déjà été déduit pour ce détail
                $mouvementExistant = MouvementStock::where('fkidDetailFacture', $detailId)
                    ->where('typeMouvement', 'SORTIE')
                    ->exists();
                
                if ($estPayee && $mouvementExistant) {
                    // La facture est payée et le stock a été déduit, remettre le stock
                    $this->remettreStockMedicament($medicamentId, $quantiteMedicament, $facture->Idfacture, $detailId);
                }
            }

            // Calculer le montant à soustraire
            $montantActe = $detail->PrixFacture * $detail->Quantite;
            $txpec = $facture->TXPEC ?? 0;
            $montantPEC = $montantActe * $txpec;

            // Mettre à jour les montants de la facture
            $facture->TotFacture = max(0, ($facture->TotFacture ?? 0) - $montantActe);
            $facture->TotalPEC = max(0, ($facture->TotalPEC ?? 0) - $montantPEC);
            $facture->TotalfactPatient = max(0, $facture->TotFacture - $facture->TotalPEC);
            $facture->save();

            // Supprimer le détail de l'acte
            $detail->delete();

            DB::commit();
            $typeLabel = $isMedicament ? 'Médicament' : 'Acte';
            session()->flash('message', $typeLabel . ' supprimé avec succès.');
            $this->loadFactures(); // Recharger les factures pour voir les changements

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Une erreur est survenue lors de la suppression : ' . $e->getMessage());
        }
    }

    public function loadFacturesEnAttente()
    {
        $cacheKey = 'factures_en_attente_' . Auth::user()->fkidcabinet;
        $this->facturesEnAttente = Cache::remember($cacheKey, 300, function() {
            return Facture::with(['patient:id,ID,Nom,Prenom', 'medecin:idMedecin,Nom'])
                ->where('estfacturer', 0)
                ->where('fkidCabinet', Auth::user()->fkidcabinet)
                ->select(['Idfacture', 'Nfacture', 'IDPatient', 'FkidMedecinInitiateur', 'DtFacture'])
                ->orderBy('DtFacture', 'desc')
                ->limit(50) // Limiter le nombre de factures chargées
                ->get();
        });
    }

    public function openMedecinModal()
    {
        $user = auth()->user();
        $isMedecin = !empty($user->fkidmedecin);

        if ($isMedecin) {
            // Si l'utilisateur est un médecin, créer directement la facture
            $this->createFactureVide($user->fkidmedecin);
        } else {
            // Si l'utilisateur n'est pas un médecin, afficher le modal de sélection
            $this->medecins = \App\Models\Medecin::where('fkidcabinet', auth()->user()->fkidcabinet)
                ->orderBy('Nom')
                ->get();
            $this->showMedecinModal = true;
        }
    }

    public function selectMedecin($medecinId)
    {
        $this->selectedMedecinId = $medecinId;
        $this->createFactureVide($medecinId);
        $this->showMedecinModal = false;
    }

    public function updatedSearchMedecin()
    {
        $this->medecins = \App\Models\Medecin::where('fkidcabinet', auth()->user()->fkidcabinet)
            ->where('Nom', 'like', '%' . $this->searchMedecin . '%')
            ->orderBy('Nom')
            ->get();
    }

    public function openDossierMedicalModal($factureId)
    {
        $this->factureIdForDossier = $factureId;
        $this->factureDossier = Facture::with(['patient', 'medecin'])->find($factureId);
        if ($this->factureDossier && $this->factureDossier->patient) {
            $this->patientDossier = $this->factureDossier->patient;
        }
        $this->showDossierMedicalModal = true;
    }

    public function closeDossierMedicalModal()
    {
        $this->showDossierMedicalModal = false;
        $this->factureIdForDossier = null;
        $this->factureDossier = null;
        $this->patientDossier = null;
    }

    public function createFactureVide($medecinId = null)
    {
        try {
            DB::beginTransaction();

            $user = auth()->user();
            
            // Si aucun médecin n'est spécifié, on vérifie si l'utilisateur est un médecin
            if (!$medecinId) {
                if (empty($user->fkidmedecin)) {
                    throw new \Exception('Vous devez sélectionner un médecin pour créer la facture');
                }
                $medecinId = $user->fkidmedecin;
            }

            $annee = Carbon::now()->year;
            $derniereFacture = Facture::where('anneeFacture', $annee)
                                ->orderBy('Nfacture', 'desc')
                                ->first();
            
            $numero = $derniereFacture ? intval(explode('-', $derniereFacture->Nfacture)[0]) + 1 : 1;
            $nfacture = $numero . '-' . $annee;
            $nordre = (Facture::where('anneeFacture', $annee)->max('nordre') ?? 0) + 1;

            $facture = Facture::create([
                'Nfacture' => $nfacture,
                'anneeFacture' => $annee,
                'nordre' => $nordre,
                'DtFacture' => Carbon::now(),
                'IDPatient' => $this->selectedPatient['ID'],
                'ISTP' => 0,
                'TXPEC' => 0,
                'TotFacture' => 0,
                'TotalPEC' => 0,
                'TotalfactPatient' => 0,
                'FkidMedecinInitiateur' => $medecinId,
                'fkidCabinet' => $user->fkidcabinet,
                'user' => $user->NomComplet ?? $user->name,
                'TotReglPatient' => 0,
                'ReglementPEC' => 0,
                'PartLaboratoire' => 0,
                'MontantAffectation' => 0
            ]);

            DB::commit();
            $this->loadFactures();
            session()->flash('message', 'Facture créée avec succès');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Erreur lors de la création de la facture: ' . $e->getMessage());
        }
    }

    /**
     * Créer une facture de médicaments depuis un panier
     * Cette méthode centralise la création de factures de médicaments
     * 
     * @param array $panierVente Tableau d'items du panier avec les clés: medicamentId, libelle, quantite, prixRef, prixFacture
     * @param int $patientId ID du patient
     * @param int|null $medecinId ID du médecin (optionnel)
     * @return Facture|null La facture créée ou null en cas d'erreur
     */
    public function creerFactureMedicamentsDepuisPanier($panierVente, $patientId, $medecinId = null)
    {
        if (empty($panierVente)) {
            throw new \Exception('Le panier est vide.');
        }

        if (!$patientId) {
            throw new \Exception('Veuillez sélectionner un patient.');
        }

        try {
            DB::beginTransaction();

            $user = Auth::user();
            $patient = Patient::find($patientId);
            
            if (!$patient) {
                throw new \Exception('Patient non trouvé.');
            }

            // Déterminer le médecin
            if (!$medecinId) {
                $medecinId = $user->fkidmedecin ?? 1;
            }
            $medecin = Medecin::find($medecinId);
            if (!$medecin) {
                $medecinId = 1; // Médecin par défaut
                $medecin = Medecin::find($medecinId);
            }

            // Créer la facture vide
            $annee = Carbon::now()->year;
            $derniereFacture = Facture::where('anneeFacture', $annee)
                ->where('fkidCabinet', $user->fkidcabinet)
                ->orderBy('nordre', 'desc')
                ->first();
            
            $nordre = $derniereFacture ? $derniereFacture->nordre + 1 : 1;
            $numero = $derniereFacture ? intval(explode('-', $derniereFacture->Nfacture)[0]) + 1 : 1;
            $nfacture = $numero . '-' . $annee;

            $facture = Facture::create([
                'Nfacture' => $nfacture,
                'anneeFacture' => $annee,
                'nordre' => $nordre,
                'DtFacture' => Carbon::now(),
                'IDPatient' => $patientId,
                'ISTP' => 0,
                'fkidEtsAssurance' => null,
                'TXPEC' => 0,
                'TotFacture' => 0,
                'TotalPEC' => 0,
                'TotalfactPatient' => 0,
                'TotReglPatient' => 0,
                'ReglementPEC' => 0,
                'ModeReglement' => null,
                'Areglepar' => null,
                'DtReglement' => null,
                'fkidbordfacture' => 0,
                'ispayerAssureur' => 0,
                'user' => $user->NomComplet ?? 'System',
                'estfacturer' => 1,
                'FkidMedecinInitiateur' => $medecinId,
                'PartLaboratoire' => 0,
                'MontantAffectation' => 0,
                'Type' => 'Facture',
                'fkidCabinet' => $user->fkidcabinet
            ]);

            $totalFacture = 0;

            // Vérifier le stock pour tous les médicaments avant de créer la facture
            foreach ($panierVente as $item) {
                $medicament = \App\Models\Medicament::find($item['medicamentId']);
                if (!$medicament) {
                    throw new \Exception('Médicament non trouvé: ' . ($item['libelle'] ?? 'Inconnu'));
                }

                // Vérifier que c'est bien un médicament (fkidtype = 1)
                if ($medicament->fkidtype != 1) {
                    throw new \Exception('Seuls les médicaments peuvent être facturés depuis l\'onglet Pharmacie.');
                }

                // Utiliser la méthode centralisée pour vérifier le stock
                $infoStock = $this->verifierStockDisponible(
                    $item['medicamentId'],
                    $item['quantite'],
                    $patientId,
                    null // Pas encore de facture créée
                );

                if ($infoStock['disponible'] < $item['quantite']) {
                    throw new \Exception('Stock insuffisant pour le médicament "' . ($item['libelle'] ?? 'Inconnu') . '". Stock disponible: ' . number_format($infoStock['disponible'], 0) . ' (Stock total: ' . number_format($infoStock['stock']->quantiteStock, 0) . ', Déjà facturé non payé: ' . number_format($infoStock['quantiteDejaFacturee'], 0) . ')');
                }
            }

            // Si toutes les vérifications passent, créer la facture et ajouter les détails
            foreach ($panierVente as $item) {
                $medicament = \App\Models\Medicament::find($item['medicamentId']);
                if (!$medicament || $medicament->fkidtype != 1) {
                    continue; // Déjà vérifié plus haut
                }

                // IsAct = 2 pour les médicaments uniquement
                $isAct = 2;
                $prixItem = ($item['prixFacture'] ?? $item['prixRef'] ?? 0) * $item['quantite'];
                $totalFacture += $prixItem;

                Detailfacturepatient::create([
                    'fkidfacture' => $facture->Idfacture,
                    'DtAjout' => Carbon::now(),
                    'Actes' => $item['libelle'],
                    'PrixRef' => $item['prixRef'] ?? $item['prixUnitaire'] ?? 0,
                    'PrixFacture' => $item['prixFacture'] ?? $item['prixRef'] ?? $item['prixUnitaire'] ?? 0,
                    'Quantite' => $item['quantite'],
                    'fkidmedicament' => $item['medicamentId'],
                    'IsAct' => $isAct,
                    'fkidMedecin' => $medecinId,
                    'fkidcabinet' => $user->fkidcabinet,
                    'ActesArab' => 'NR',
                    'Dents' => 'Med',
                    'DTajout2' => Carbon::now(),
                    'user' => $user->NomComplet ?? 'System',
                    'DtActe' => Carbon::now()
                ]);
            }

            // Mettre à jour le total de la facture
            $facture->TotFacture = $totalFacture;
            $facture->TotalfactPatient = $totalFacture;
            $facture->save();

            DB::commit();
            return $facture;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Vérifier le stock disponible pour un médicament
     * Prend en compte le stock total moins les quantités déjà facturées mais non payées
     * 
     * @param int $medicamentId ID du médicament
     * @param int $quantiteDemandee Quantité demandée
     * @param int|null $patientId ID du patient (optionnel, pour exclure ses factures non payées du calcul)
     * @param int|null $factureId ID de la facture actuelle (optionnel, pour exclure cette facture du calcul)
     * @return array ['disponible' => float, 'stock' => StockMedicament|null, 'quantiteDejaFacturee' => float]
     * @throws \Exception Si le médicament n'est pas en stock
     */
    private function verifierStockDisponible($medicamentId, $quantiteDemandee, $patientId = null, $factureId = null)
    {
        $stock = StockMedicament::where('fkidMedicament', $medicamentId)
            ->where('fkidCabinet', Auth::user()->fkidcabinet)
            ->first();

        if (!$stock) {
            throw new \Exception('Ce médicament n\'est pas en stock.');
        }

        // Calculer la quantité déjà facturée mais non payée pour ce médicament
        $query = Detailfacturepatient::join('facture', 'detailfacturepatient.fkidfacture', '=', 'facture.Idfacture')
            ->where('detailfacturepatient.fkidmedicament', $medicamentId)
            ->where('detailfacturepatient.IsAct', 2)
            ->whereRaw('(CASE WHEN facture.ISTP > 0 THEN facture.TotalfactPatient ELSE facture.TotFacture END) > (facture.TotReglPatient + COALESCE(facture.ReglementPEC, 0))');

        // Exclure la facture actuelle si elle est fournie (pour permettre la modification)
        if ($factureId) {
            $query->where('facture.Idfacture', '!=', $factureId);
        }

        // Si un patient est spécifié, on peut inclure ou exclure ses factures selon le besoin
        // Par défaut, on inclut toutes les factures non payées de tous les patients
        $quantiteDejaFacturee = $query->sum('detailfacturepatient.Quantite');

        $stockDisponible = $stock->quantiteStock - $quantiteDejaFacturee;

        return [
            'disponible' => $stockDisponible,
            'stock' => $stock,
            'quantiteDejaFacturee' => $quantiteDejaFacturee
        ];
    }

    /**
     * Déduire le stock de tous les médicaments d'une facture lors du paiement
     */
    private function deduireStockFacture($facture)
    {
        // Récupérer tous les détails de la facture qui sont des médicaments (IsAct = 2)
        $detailsMedicaments = Detailfacturepatient::where('fkidfacture', $facture->Idfacture)
            ->where('IsAct', 2)
            ->whereNotNull('fkidmedicament')
            ->get();

        // Vérifier si le stock a déjà été déduit pour cette facture
        $mouvementsExistants = MouvementStock::where('fkidFacture', $facture->Idfacture)
            ->where('typeMouvement', 'SORTIE')
            ->exists();

        if ($mouvementsExistants) {
            // Le stock a déjà été déduit, ne pas déduire à nouveau
            return;
        }

        // Vérifier le stock disponible pour tous les médicaments avant de déduire
        // Utiliser la méthode centralisée pour la cohérence
        foreach ($detailsMedicaments as $detail) {
            try {
                $infoStock = $this->verifierStockDisponible(
                    $detail->fkidmedicament,
                    $detail->Quantite,
                    $facture->IDPatient,
                    $facture->Idfacture
                );
                
                // Vérifier que le stock disponible est suffisant
                if ($infoStock['disponible'] < $detail->Quantite) {
                    throw new \Exception('Stock insuffisant pour le médicament "' . $detail->Actes . '". Stock disponible: ' . number_format($infoStock['disponible'], 0) . ' (Stock total: ' . number_format($infoStock['stock']->quantiteStock, 0) . ', Déjà facturé non payé: ' . number_format($infoStock['quantiteDejaFacturee'], 0) . ')');
                }
            } catch (\Exception $e) {
                // Si c'est une erreur de stock, la propager
                if (strpos($e->getMessage(), 'Stock') !== false || strpos($e->getMessage(), 'stock') !== false) {
                    throw $e;
                }
                // Sinon, relancer l'exception
                throw new \Exception('Erreur lors de la vérification du stock pour "' . $detail->Actes . '": ' . $e->getMessage());
            }
        }

        // Si tous les stocks sont suffisants, déduire pour chaque médicament
        foreach ($detailsMedicaments as $detail) {
            $this->deduireStockMedicament($detail->fkidmedicament, $detail->Quantite, $facture->Idfacture, $detail->idDetfacture);
        }
    }

    /**
     * Déduire le stock d'un médicament lors d'une vente via facture
     */
    private function deduireStockMedicament($medicamentId, $quantite, $factureId, $detailFactureId)
    {
        $stock = StockMedicament::where('fkidMedicament', $medicamentId)
            ->where('fkidCabinet', Auth::user()->fkidcabinet)
            ->first();

        if (!$stock || $stock->quantiteStock < $quantite) {
            throw new \Exception('Stock insuffisant pour ce médicament. Stock disponible: ' . ($stock ? $stock->quantiteStock : 0));
        }

        // Récupérer le prix depuis la table medicaments
        $medicament = \App\Models\Medicament::find($medicamentId);
        $prixVente = $medicament ? ($medicament->PrixRef ?? 0) : 0;

        $quantiteRestante = $quantite;
        $lots = LotMedicament::where('fkidStock', $stock->idStock)
            ->where('quantiteRestante', '>', 0)
            ->where('Masquer', 0)
            ->orderBy('dateExpiration', 'asc')
            ->orderBy('dateEntree', 'asc')
            ->get();

        $facture = Facture::find($factureId);
        $patientId = $facture->IDPatient ?? null;

        foreach ($lots as $lot) {
            if ($quantiteRestante <= 0) {
                break;
            }

            $quantiteAPrendre = min($quantiteRestante, $lot->quantiteRestante);

            // Mettre à jour le lot
            $lot->quantiteRestante -= $quantiteAPrendre;
            $lot->save();

            // Créer le mouvement
            MouvementStock::create([
                'fkidStock' => $stock->idStock,
                'fkidMedicament' => $medicamentId,
                'fkidLot' => $lot->idLot,
                'typeMouvement' => 'SORTIE',
                'quantite' => -$quantiteAPrendre,
                'prixUnitaire' => $prixVente,
                'montantTotal' => $prixVente * $quantiteAPrendre,
                'motif' => 'Vente via facture',
                'fkidFacture' => $factureId,
                'fkidDetailFacture' => $detailFactureId,
                'fkidPatient' => $patientId,
                'fkidUser' => Auth::id(),
                'dateMouvement' => Carbon::now(),
                'reference' => $facture->Nfacture ?? null,
                'notes' => null
            ]);

            $quantiteRestante -= $quantiteAPrendre;
        }

        // Si pas de lots ou quantité restante après épuisement des lots, déduire directement du stock
        if ($quantiteRestante > 0) {
            // Vérifier que le stock global est suffisant
            if ($stock->quantiteStock < $quantite) {
                throw new \Exception('Stock insuffisant pour ce médicament. Stock disponible: ' . number_format($stock->quantiteStock, 0) . ', Quantité demandée: ' . number_format($quantite, 0));
            }
            
            MouvementStock::create([
                'fkidStock' => $stock->idStock,
                'fkidMedicament' => $medicamentId,
                'fkidLot' => null,
                'typeMouvement' => 'SORTIE',
                'quantite' => -$quantiteRestante,
                'prixUnitaire' => $prixVente,
                'montantTotal' => $prixVente * $quantiteRestante,
                'motif' => 'Vente via facture' . ($lots->isEmpty() ? ' (sans lot)' : ' (stock sans lot)'),
                'fkidFacture' => $factureId,
                'fkidDetailFacture' => $detailFactureId,
                'fkidPatient' => $patientId,
                'fkidUser' => Auth::id(),
                'dateMouvement' => Carbon::now(),
                'reference' => $facture->Nfacture ?? null,
                'notes' => $lots->isEmpty() ? 'Aucun lot disponible' : 'Quantité restante après épuisement des lots'
            ]);
        }

        // Mettre à jour le stock global (déduction de la quantité totale)
        $stock->quantiteStock -= $quantite;
        $stock->dateDerniereSortie = Carbon::now();
        $stock->save();
    }

    /**
     * Remettre le stock d'un médicament lors de la suppression d'un détail d'une facture payée
     */
    private function remettreStockMedicament($medicamentId, $quantite, $factureId, $detailFactureId)
    {
        $stock = StockMedicament::where('fkidMedicament', $medicamentId)
            ->where('fkidCabinet', Auth::user()->fkidcabinet)
            ->first();

        if (!$stock) {
            return; // Pas de stock à remettre
        }

        $facture = Facture::find($factureId);
        $patientId = $facture->IDPatient ?? null;

        // Récupérer les mouvements de sortie pour ce détail pour savoir quels lots ont été utilisés
        $mouvementsSortie = MouvementStock::where('fkidDetailFacture', $detailFactureId)
            ->where('fkidFacture', $factureId)
            ->where('typeMouvement', 'SORTIE')
            ->where('fkidMedicament', $medicamentId)
            ->get();

        // Remettre le stock dans les lots si possible, sinon créer un ajustement
        foreach ($mouvementsSortie as $mouvement) {
            if ($mouvement->fkidLot) {
                // Remettre dans le lot d'origine si possible
                $lot = LotMedicament::find($mouvement->fkidLot);
                if ($lot) {
                    $quantiteARemettre = abs($mouvement->quantite);
                    $lot->quantiteRestante += $quantiteARemettre;
                    $lot->save();
                }
            }

            // Créer un mouvement d'ajustement pour annuler la sortie
            MouvementStock::create([
                'fkidStock' => $stock->idStock,
                'fkidMedicament' => $medicamentId,
                'fkidLot' => $mouvement->fkidLot,
                'typeMouvement' => 'AJUSTEMENT',
                'quantite' => abs($mouvement->quantite), // Quantité positive pour remettre
                'prixUnitaire' => $mouvement->prixUnitaire,
                'montantTotal' => $mouvement->montantTotal,
                'motif' => 'Annulation vente - Suppression détail facture',
                'fkidFacture' => $factureId,
                'fkidDetailFacture' => $detailFactureId,
                'fkidPatient' => $patientId,
                'fkidUser' => Auth::id(),
                'dateMouvement' => Carbon::now(),
                'reference' => $facture->Nfacture ?? null,
                'notes' => 'Remise de stock suite à suppression du détail'
            ]);
        }

        // Mettre à jour le stock global
        $stock->quantiteStock += $quantite;
        $stock->dateDerniereEntree = Carbon::now();
        $stock->save();
    }

    public function render()
    {
        $isDocteur = Auth::user()->isDocteur();
        $isDocteurProprietaire = Auth::user()->isDocteurProprietaire();

        if ($this->selectedPatient) {
            if (!$this->factures) {
                $this->factures = $this->getFacturesProperty();
            }
            $factures = $this->factures;
        }

        return view('livewire.reglement-facture', [
            'isDocteur' => $isDocteur,
            'isDocteurProprietaire' => $isDocteurProprietaire,
            'facturesEnAttente' => $this->facturesEnAttente ?? collect(),
            'factures' => $factures
        ]);
    }
} 