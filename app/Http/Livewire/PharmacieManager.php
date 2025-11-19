<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\StockMedicament;
use App\Models\Medicament;
use App\Models\LotMedicament;
use App\Models\MouvementStock;
use App\Models\Patient;
use App\Models\Facture;
use App\Models\Detailfacturepatient;
use App\Models\Medecin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PharmacieManager extends Component
{
    use WithPagination;

    // Onglet actif
    public $activeTab = 'dashboard';
    
    // Mode vente uniquement (depuis gestion patient)
    public $venteOnly = false;

    // Propriétés pour le stock
    public $searchStock = '';
    public $filterStock = 'tous'; // tous, faible, expires, expire_bientot
    public $selectedStock = null;

    // Propriétés pour l'entrée de stock
    public $showEntreeModal = false;
    public $entreeMedicamentId = null;
    public $entreeLibelleMedic = '';
    public $entreeQuantite = 1;
    public $entreePrixAchat = 0;
    public $entreePrixVente = 0;
    public $entreeQuantiteMin = 0; // Seuil minimum
    public $entreeNumeroLot = '';
    public $entreeDateExpiration = null;
    public $entreeFournisseur = '';
    public $entreeReferenceFacture = '';
    public $entreeNotes = '';
    
    // Propriétés pour l'autocomplete de médicament
    public $entreeSearchMedicament = '';
    public $entreeMedicamentsResults = [];
    public $entreeShowMedicamentResults = false;
    public $entreeIsSearchingMedicament = false;

    // Patient sélectionné depuis le composant parent
    public $patientId = null;

    // Propriétés pour la vente
    public $panierVente = []; // Tableau des médicaments sélectionnés pour la vente
    public $factureVenteId = null; // ID de la facture créée pour la vente
    public $showFactureModal = false; // Modal pour voir/créer la facture
    public $quantiteVente = []; // Quantités par médicament pour l'input

    // Propriétés pour l'historique
    public $searchHistorique = '';
    public $filterTypeMouvement = '';
    public $filterDateDebut = '';
    public $filterDateFin = '';

    // Alertes
    public $alertesStockFaible = 0;
    public $alertesExpires = 0;
    public $alertesExpireBientot = 0;
    
    // Modals pour les détails des alertes
    public $showModalRupture = false;
    public $showModalStockFaible = false;
    public $showModalLotsExpires = false;
    public $showModalExpireBientot = false;

    protected $paginationTheme = 'bootstrap';
    
    protected $listeners = [
        'patientUpdated' => 'updatePatient'
    ];

    public function mount($patientId = null, $venteOnly = false)
    {
        $this->patientId = $patientId;
        $this->venteOnly = $venteOnly;
        
        // Initialiser toutes les propriétés pour éviter les erreurs de corruption
        if (!isset($this->entreePrixVente)) {
            $this->entreePrixVente = 0;
        }
        
        // Si mode vente uniquement, forcer l'onglet vente
        if ($venteOnly) {
            $this->activeTab = 'vente';
        } else {
            // Si on n'est pas en mode vente uniquement et que l'onglet actif est vente, rediriger vers dashboard
            if ($this->activeTab === 'vente') {
                $this->activeTab = 'dashboard';
            }
        }
        
        $this->calculerAlertes();
    }
    
    public function updatePatient($patientId)
    {
        $this->patientId = $patientId;
        
        // Si le modal de vente est ouvert, mettre à jour le patient
        if ($this->showVenteModal && $patientId) {
            $patient = Patient::find($patientId);
            if ($patient) {
                $this->ventePatientId = $patient->ID;
                $this->ventePatientNom = trim($patient->Nom . ' ' . $patient->Prenom);
            }
        }
    }

    public function updatingSearchStock()
    {
        $this->resetPage();
    }

    public function updatingFilterStock()
    {
        $this->resetPage();
    }

    // ========== ONGLET STOCK ACTUEL ==========

    /**
     * Calculer le stock disponible réel pour un médicament
     * (stock total - quantités déjà facturées non payées dont le stock n'a pas été déduit)
     */
    private function calculerStockDisponible($medicamentId)
    {
        $stock = StockMedicament::where('fkidMedicament', $medicamentId)
            ->where('fkidCabinet', Auth::user()->fkidcabinet)
            ->first();

        if (!$stock) {
            return 0;
        }

        // Calculer la quantité déjà facturée mais non payée ET dont le stock n'a pas encore été déduit
        $quantiteDejaFacturee = Detailfacturepatient::join('facture', 'detailfacturepatient.fkidfacture', '=', 'facture.Idfacture')
            ->where('detailfacturepatient.fkidmedicament', $medicamentId)
            ->where('detailfacturepatient.IsAct', 2)
            ->whereRaw('(CASE WHEN facture.ISTP > 0 THEN facture.TotalfactPatient ELSE facture.TotFacture END) > (facture.TotReglPatient + COALESCE(facture.ReglementPEC, 0))')
            ->whereNotExists(function($query) {
                $query->select(DB::raw(1))
                    ->from('mouvements_stock')
                    ->whereColumn('mouvements_stock.fkidFacture', 'facture.Idfacture')
                    ->whereColumn('mouvements_stock.fkidMedicament', 'detailfacturepatient.fkidmedicament')
                    ->where('mouvements_stock.typeMouvement', 'SORTIE');
            })
            ->sum('detailfacturepatient.Quantite');

        return max(0, $stock->quantiteStock - $quantiteDejaFacturee);
    }

    public function getStocksProperty()
    {
        $query = StockMedicament::with(['medicament'])
            ->where('fkidCabinet', Auth::user()->fkidcabinet)
            ->where('Masquer', 0)
            // Filtrer uniquement les médicaments (fkidtype = 1)
            ->whereHas('medicament', function($q) {
                $q->where('fkidtype', 1);
            });

        // Filtre par recherche
        if ($this->searchStock) {
            $query->whereHas('medicament', function($q) {
                $q->where('LibelleMedic', 'like', '%' . $this->searchStock . '%')
                  ->where('fkidtype', 1); // S'assurer que ce sont des médicaments
            });
        }

        // Filtres de statut
        switch ($this->filterStock) {
            case 'faible':
                $query->whereColumn('quantiteStock', '<=', 'quantiteMin');
                break;
            case 'expires':
                $query->whereHas('lots', function($q) {
                    $q->whereNotNull('dateExpiration')
                      ->where('dateExpiration', '<', Carbon::now())
                      ->where('quantiteRestante', '>', 0);
                });
                break;
            case 'expire_bientot':
                $dateLimite = Carbon::now()->addDays(30);
                $query->whereHas('lots', function($q) use ($dateLimite) {
                    $q->whereNotNull('dateExpiration')
                      ->where('dateExpiration', '>=', Carbon::now())
                      ->where('dateExpiration', '<=', $dateLimite)
                      ->where('quantiteRestante', '>', 0);
                });
                break;
        }

        return $query->orderBy('quantiteStock', 'asc')->paginate(15);
    }

    // ========== ONGLET ENTRÉES DE STOCK ==========

    public function openEntreeModal()
    {
        $this->resetEntreeForm();
        $this->showEntreeModal = true;
    }

    public function closeEntreeModal()
    {
        $this->showEntreeModal = false;
        $this->resetEntreeForm();
    }

    public function resetEntreeForm()
    {
        $this->entreeMedicamentId = null;
        $this->entreeLibelleMedic = '';
        $this->entreeQuantite = 1;
        $this->entreePrixAchat = 0;
        $this->entreePrixVente = 0;
        $this->entreeQuantiteMin = 0;
        $this->entreeNumeroLot = '';
        $this->entreeDateExpiration = null;
        $this->entreeFournisseur = '';
        $this->entreeReferenceFacture = '';
        $this->entreeNotes = '';
        $this->entreeSearchMedicament = '';
        $this->entreeMedicamentsResults = [];
        $this->entreeShowMedicamentResults = false;
        $this->entreeIsSearchingMedicament = false;
    }

    public function updatedEntreeSearchMedicament()
    {
        $search = trim($this->entreeSearchMedicament);
        
        if (strlen($search) >= 1) {
            $this->searchEntreeMedicaments();
        } else {
            $this->entreeMedicamentsResults = [];
            $this->entreeShowMedicamentResults = false;
            $this->entreeMedicamentId = null;
            $this->entreeLibelleMedic = '';
            $this->entreePrixVente = 0;
        }
    }
    
    public function updatedEntreeMedicamentId()
    {
        // Mettre à jour le prix de vente quand un médicament est sélectionné
        if ($this->entreeMedicamentId) {
            $medicament = Medicament::find($this->entreeMedicamentId);
            if ($medicament) {
                $this->entreePrixVente = $medicament->PrixRef ?? 0;
            }
        }
    }

    public function searchEntreeMedicaments()
    {
        $this->entreeIsSearchingMedicament = true;
        
        try {
            $search = trim($this->entreeSearchMedicament);
            
            if (empty($search)) {
                $this->entreeMedicamentsResults = [];
                $this->entreeShowMedicamentResults = false;
                $this->entreeIsSearchingMedicament = false;
                return;
            }
            
            $query = Medicament::where('fkidtype', 1); // Seulement les médicaments
            
            // Si la recherche est numérique, chercher aussi par ID
            if (is_numeric($search)) {
                $query->where(function($q) use ($search) {
                    $q->where('LibelleMedic', 'like', '%' . $search . '%')
                      ->orWhere('IDMedic', '=', (int)$search);
                });
            } else {
                $query->where('LibelleMedic', 'like', '%' . $search . '%');
            }

            $this->entreeMedicamentsResults = $query
                ->select('IDMedic', 'LibelleMedic', 'PrixRef', 'fkidtype')
                ->orderBy('LibelleMedic')
                ->limit(20)
                ->get();

            $this->entreeShowMedicamentResults = true;
            
        } catch (\Exception $e) {
            \Log::error('Erreur recherche médicament', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'Erreur lors de la recherche : ' . $e->getMessage());
            $this->entreeMedicamentsResults = [];
            $this->entreeShowMedicamentResults = false;
        } finally {
            $this->entreeIsSearchingMedicament = false;
        }
    }

    public function selectEntreeMedicament($medicamentId)
    {
        try {
            $medicament = Medicament::find($medicamentId);

            if ($medicament && $medicament->fkidtype == 1) {
                $this->entreeMedicamentId = $medicament->IDMedic;
                $this->entreeLibelleMedic = $medicament->LibelleMedic;
                $this->entreeSearchMedicament = $medicament->LibelleMedic;
                $this->entreeMedicamentsResults = [];
                $this->entreeShowMedicamentResults = false;
                
                // Pré-remplir le prix de vente avec le PrixRef du médicament (toujours, même si 0)
                $this->entreePrixVente = $medicament->PrixRef ?? 0;
            } else {
                session()->flash('error', 'Médicament non trouvé ou invalide.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Une erreur est survenue lors de la sélection du médicament.');
        }
    }

    public function closeEntreeMedicamentResults()
    {
        $this->entreeShowMedicamentResults = false;
    }

    public function enregistrerEntree()
    {
        $this->validate([
            'entreeMedicamentId' => 'required|integer|exists:medicaments,IDMedic',
            'entreeQuantite' => 'required|integer|min:1',
            'entreePrixAchat' => 'required|integer|min:0',
            'entreePrixVente' => 'required|integer|min:0',
            'entreeQuantiteMin' => 'required|integer|min:0',
        ], [
            'entreeMedicamentId.required' => 'Veuillez sélectionner un médicament',
            'entreeQuantite.required' => 'La quantité est requise',
            'entreePrixAchat.required' => 'Le prix d\'achat est requis',
            'entreePrixVente.required' => 'Le prix de vente est requis',
            'entreeQuantiteMin.required' => 'Le seuil minimum est requis',
        ]);

        DB::transaction(function () {
            $cabinetId = Auth::user()->fkidcabinet;
            $userId = Auth::id();

            // Récupérer ou créer le stock
            $stock = StockMedicament::firstOrCreate(
                [
                    'fkidMedicament' => $this->entreeMedicamentId,
                    'fkidCabinet' => $cabinetId
                ],
                [
                    'quantiteStock' => 0,
                    'quantiteMin' => $this->entreeQuantiteMin,
                    'prixAchat' => $this->entreePrixAchat,
                    'prixVente' => $this->entreePrixVente,
                    'Masquer' => 0
                ]
            );

            // Mettre à jour le prix d'achat moyen
            $nouveauPrixAchat = (($stock->prixAchat * $stock->quantiteStock) + ($this->entreePrixAchat * $this->entreeQuantite)) 
                                / ($stock->quantiteStock + $this->entreeQuantite);

            // Créer le lot si date d'expiration renseignée
            $lotId = null;
            if ($this->entreeDateExpiration) {
                $lot = LotMedicament::create([
                    'fkidStock' => $stock->idStock,
                    'fkidMedicament' => $this->entreeMedicamentId,
                    'numeroLot' => $this->entreeNumeroLot ?: null,
                    'quantiteInitiale' => $this->entreeQuantite,
                    'quantiteRestante' => $this->entreeQuantite,
                    'dateExpiration' => $this->entreeDateExpiration,
                    'dateEntree' => Carbon::now(),
                    'prixAchatUnitaire' => $this->entreePrixAchat,
                    'fournisseur' => $this->entreeFournisseur ?: null,
                    'referenceFacture' => $this->entreeReferenceFacture ?: null,
                    'fkidUser' => $userId,
                    'Masquer' => 0
                ]);
                $lotId = $lot->idLot;
            }

            // Mettre à jour le stock
            $stock->update([
                'quantiteStock' => $stock->quantiteStock + $this->entreeQuantite,
                'prixAchat' => $nouveauPrixAchat,
                'prixVente' => $this->entreePrixVente, // Mettre à jour le prix de vente
                'quantiteMin' => $this->entreeQuantiteMin, // Mettre à jour le seuil minimum
                'dateDerniereEntree' => Carbon::now()
            ]);

            // Créer le mouvement
            MouvementStock::create([
                'fkidStock' => $stock->idStock,
                'fkidMedicament' => $this->entreeMedicamentId,
                'fkidLot' => $lotId,
                'typeMouvement' => 'ENTREE',
                'quantite' => $this->entreeQuantite,
                'prixUnitaire' => $this->entreePrixAchat,
                'montantTotal' => $this->entreePrixAchat * $this->entreeQuantite,
                'motif' => 'Entrée de stock',
                'fkidUser' => $userId,
                'dateMouvement' => Carbon::now(),
                'reference' => $this->entreeReferenceFacture ?: null,
                'notes' => $this->entreeNotes ?: null
            ]);
        });

        session()->flash('message', 'Entrée de stock enregistrée avec succès.');
        $this->closeEntreeModal();
        $this->calculerAlertes();
    }

    // ========== ONGLET VENTE ==========

    public function ajouterAuPanierVente($medicamentId)
    {
        if (!$this->patientId) {
            session()->flash('error', 'Veuillez sélectionner un patient pour effectuer une vente.');
            return;
        }

        // Lire la quantité depuis la propriété quantiteVente
        $quantiteFinale = isset($this->quantiteVente[$medicamentId]) && $this->quantiteVente[$medicamentId] > 0 
            ? (int)$this->quantiteVente[$medicamentId] 
            : 1;
        
        if ($quantiteFinale <= 0) {
            session()->flash('error', 'La quantité doit être supérieure à 0.');
            return;
        }

        $stock = StockMedicament::where('fkidMedicament', $medicamentId)
            ->where('fkidCabinet', Auth::user()->fkidcabinet)
            ->first();

        if (!$stock) {
            session()->flash('error', 'Médicament non trouvé en stock.');
            return;
        }

        // Calculer le stock disponible réel
        $stockDisponible = $this->calculerStockDisponible($medicamentId);
        
        if ($stockDisponible < $quantiteFinale) {
            session()->flash('error', 'Stock insuffisant. Stock disponible: ' . number_format($stockDisponible, 0) . ' (Stock total: ' . number_format($stock->quantiteStock, 0) . ')');
            return;
        }

        $medicament = Medicament::find($medicamentId);
        if (!$medicament) {
            session()->flash('error', 'Médicament non trouvé.');
            return;
        }

        // Vérifier que c'est bien un médicament (fkidtype = 1)
        if ($medicament->fkidtype != 1) {
            session()->flash('error', 'Seuls les médicaments peuvent être vendus depuis l\'onglet Pharmacie.');
            return;
        }

        // Prix de référence (toujours depuis la table medicaments)
        $prixRef = $medicament->PrixRef ?? 0;
        // Prix facturé (par défaut égal au prix de référence, mais peut être modifié)
        $prixFacture = $prixRef;

        // Vérifier si le médicament est déjà dans le panier
        $index = array_search($medicamentId, array_column($this->panierVente, 'medicamentId'));
        
        if ($index !== false) {
            $nouvelleQuantite = $this->panierVente[$index]['quantite'] + $quantiteFinale;
            $stockDisponible = $this->calculerStockDisponible($medicamentId);
            if ($nouvelleQuantite > $stockDisponible) {
                session()->flash('error', 'Quantité totale dépasse le stock disponible (' . number_format($stockDisponible, 0) . ').');
                return;
            }
            $this->panierVente[$index]['quantite'] = $nouvelleQuantite;
            $this->panierVente[$index]['montant'] = $this->panierVente[$index]['quantite'] * $this->panierVente[$index]['prixFacture'];
        } else {
            $this->panierVente[] = [
                'medicamentId' => $medicamentId,
                'libelle' => $medicament->LibelleMedic,
                'quantite' => $quantiteFinale,
                'prixRef' => $prixRef,
                'prixFacture' => $prixFacture,
                'montant' => $prixFacture * $quantiteFinale,
                'stockDisponible' => $this->calculerStockDisponible($medicamentId)
            ];
        }

        // Réinitialiser la quantité dans l'input
        $this->quantiteVente[$medicamentId] = 1;
        session()->flash('message', 'Médicament ajouté au panier.');
    }

    public function retirerDuPanierVente($index)
    {
        unset($this->panierVente[$index]);
        $this->panierVente = array_values($this->panierVente);
        session()->flash('message', 'Médicament retiré du panier.');
    }

    public function modifierQuantitePanier($index, $quantite)
    {
        if ($quantite <= 0) {
            $this->retirerDuPanierVente($index);
            return;
        }

        if (isset($this->panierVente[$index])) {
            if ($quantite > $this->panierVente[$index]['stockDisponible']) {
                session()->flash('error', 'La quantité ne peut pas dépasser le stock disponible.');
                return;
            }
            $this->panierVente[$index]['quantite'] = $quantite;
            $this->panierVente[$index]['montant'] = $this->panierVente[$index]['quantite'] * $this->panierVente[$index]['prixFacture'];
        }
    }

    public function modifierPrixFacturePanier($index, $prixFacture)
    {
        if (isset($this->panierVente[$index])) {
            if ($prixFacture < 0) {
                session()->flash('error', 'Le prix ne peut pas être négatif.');
                return;
            }
            $this->panierVente[$index]['prixFacture'] = $prixFacture;
            $this->panierVente[$index]['montant'] = $this->panierVente[$index]['quantite'] * $prixFacture;
        }
    }

    public function getTotalPanierProperty()
    {
        return array_sum(array_column($this->panierVente, 'montant'));
    }

    public function creerFacture()
    {
        if (empty($this->panierVente)) {
            session()->flash('error', 'Le panier est vide.');
            return;
        }

        if (!$this->patientId) {
            session()->flash('error', 'Veuillez sélectionner un patient.');
            return;
        }

        try {
            DB::beginTransaction();

            $user = Auth::user();
            $patient = Patient::find($this->patientId);
            
            if (!$patient) {
                throw new \Exception('Patient non trouvé.');
            }

            // Utiliser la méthode centralisée pour générer le numéro de facture
            $numeroFacture = Facture::genererNumeroFacture($user->fkidcabinet);

            $medecinId = $user->fkidmedecin ?? 1;
            $medecin = Medecin::find($medecinId);
            if (!$medecin) {
                $medecinId = 1; // Médecin par défaut
            }

            $facture = Facture::create([
                'Nfacture' => $numeroFacture['Nfacture'],
                'anneeFacture' => $numeroFacture['anneeFacture'],
                'nordre' => $numeroFacture['nordre'],
                'DtFacture' => Carbon::now(),
                'IDPatient' => $this->patientId,
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
            // Utiliser la même logique que ReglementFacture pour la cohérence
            foreach ($this->panierVente as $item) {
                $medicament = Medicament::find($item['medicamentId']);
                if (!$medicament) {
                    throw new \Exception('Médicament non trouvé: ' . $item['libelle']);
                }

                // Vérifier que c'est bien un médicament (fkidtype = 1)
                if ($medicament->fkidtype != 1) {
                    throw new \Exception('Seuls les médicaments peuvent être facturés depuis l\'onglet Pharmacie.');
                }

                // Vérifier le stock disponible en utilisant la même logique centralisée
                $stock = StockMedicament::where('fkidMedicament', $item['medicamentId'])
                    ->where('fkidCabinet', $user->fkidcabinet)
                    ->first();

                if (!$stock) {
                    throw new \Exception('Le médicament "' . $item['libelle'] . '" n\'est pas en stock.');
                }

                // Calculer la quantité déjà facturée mais non payée ET dont le stock n'a pas encore été déduit
                // Pour les factures de pharmacie, le stock est déduit immédiatement, donc on ne doit pas
                // tenir compte des factures qui ont déjà des mouvements de sortie
                $quantiteDejaFacturee = Detailfacturepatient::join('facture', 'detailfacturepatient.fkidfacture', '=', 'facture.Idfacture')
                    ->where('detailfacturepatient.fkidmedicament', $item['medicamentId'])
                    ->where('detailfacturepatient.IsAct', 2)
                    ->whereRaw('(CASE WHEN facture.ISTP > 0 THEN facture.TotalfactPatient ELSE facture.TotFacture END) > (facture.TotReglPatient + COALESCE(facture.ReglementPEC, 0))')
                    ->whereNotExists(function($query) {
                        $query->select(DB::raw(1))
                            ->from('mouvements_stock')
                            ->whereColumn('mouvements_stock.fkidFacture', 'facture.Idfacture')
                            ->whereColumn('mouvements_stock.fkidMedicament', 'detailfacturepatient.fkidmedicament')
                            ->where('mouvements_stock.typeMouvement', 'SORTIE');
                    })
                    ->sum('detailfacturepatient.Quantite');

                $stockDisponible = $stock->quantiteStock - $quantiteDejaFacturee;

                if ($stockDisponible < $item['quantite']) {
                    throw new \Exception('Stock insuffisant pour le médicament "' . $item['libelle'] . '". Stock disponible: ' . number_format($stockDisponible, 0) . ' (Stock total: ' . number_format($stock->quantiteStock, 0) . ', Déjà facturé non payé: ' . number_format($quantiteDejaFacturee, 0) . ')');
                }
            }

            // Si toutes les vérifications passent, créer la facture et ajouter les détails
            foreach ($this->panierVente as $item) {
                $medicament = Medicament::find($item['medicamentId']);
                if (!$medicament || $medicament->fkidtype != 1) {
                    continue; // Déjà vérifié plus haut
                }

                // IsAct = 2 pour les médicaments uniquement (fkidtype = 1, donc IsAct = 1 + 1 = 2)
                $isAct = 2; // Médicament uniquement
                $prixItem = ($item['prixFacture'] ?? $item['prixRef']) * $item['quantite'];
                $totalFacture += $prixItem;

                $detail = Detailfacturepatient::create([
                    'fkidfacture' => $facture->Idfacture,
                    'DtAjout' => Carbon::now(),
                    'Actes' => $item['libelle'],
                    'PrixRef' => $item['prixRef'] ?? $item['prixUnitaire'] ?? 0,
                    'PrixFacture' => $item['prixFacture'] ?? $item['prixRef'] ?? $item['prixUnitaire'] ?? 0,
                    'Quantite' => $item['quantite'],
                    'fkidmedicament' => $item['medicamentId'],
                    'IsAct' => $isAct, // Toujours 2 pour les médicaments
                    'fkidMedecin' => $medecinId,
                    'fkidcabinet' => $user->fkidcabinet,
                    'ActesArab' => 'NR',
                    'Dents' => 'Med',
                    'DTajout2' => Carbon::now(),
                    'user' => $user->NomComplet ?? 'System',
                    'DtActe' => Carbon::now()
                ]);
                
                // Déduire le stock immédiatement lors de la création de la facture
                $this->deduireStockMedicament($item['medicamentId'], $item['quantite'], $facture->Idfacture, $detail->idDetfacture);
            }

            // Mettre à jour le total de la facture
            $facture->TotFacture = $totalFacture;
            $facture->TotalfactPatient = $totalFacture;
            $facture->save();

            $this->factureVenteId = $facture->Idfacture;
            $this->panierVente = []; // Vider le panier
            $this->showFactureModal = true;

            DB::commit();
            session()->flash('message', 'Facture créée avec succès. Le stock a été déduit immédiatement.');
            $this->calculerAlertes();

        } catch (\Exception $e) {
            DB::rollBack();
            $errorMessage = 'Erreur lors de la création de la facture : ' . $e->getMessage();
            session()->flash('error', $errorMessage);
            
            // Émettre un événement browser pour forcer l'affichage de la notification
            $this->dispatchBrowserEvent('pharmacie-error', [
                'message' => $errorMessage
            ]);
        }
    }

    public function fermerFactureModal()
    {
        $this->showFactureModal = false;
        $this->factureVenteId = null;
        $this->panierVente = [];
    }

    // ========== ONGLET HISTORIQUE ==========

    public function getMouvementsProperty()
    {
        $query = MouvementStock::with(['medicament', 'user', 'patient', 'lot'])
            ->whereHas('stock', function($q) {
                $q->where('fkidCabinet', Auth::user()->fkidcabinet);
            });

        if ($this->searchHistorique) {
            $query->whereHas('medicament', function($q) {
                $q->where('LibelleMedic', 'like', '%' . $this->searchHistorique . '%');
            });
        }

        if ($this->filterTypeMouvement) {
            $query->where('typeMouvement', $this->filterTypeMouvement);
        }

        if ($this->filterDateDebut) {
            $query->where('dateMouvement', '>=', $this->filterDateDebut);
        }

        if ($this->filterDateFin) {
            $query->where('dateMouvement', '<=', $this->filterDateFin . ' 23:59:59');
        }

        return $query->orderBy('dateMouvement', 'desc')->paginate(20);
    }

    // ========== ALERTES ==========

    public function calculerAlertes()
    {
        $cabinetId = Auth::user()->fkidcabinet;

        $this->alertesStockFaible = StockMedicament::where('fkidCabinet', $cabinetId)
            ->whereColumn('quantiteStock', '<=', 'quantiteMin')
            ->where('Masquer', 0)
            ->whereHas('medicament', function($q) {
                $q->where('fkidtype', 1);
            })
            ->count();

        $this->alertesExpires = LotMedicament::whereHas('stock', function($q) use ($cabinetId) {
                $q->where('fkidCabinet', $cabinetId);
            })
            ->whereNotNull('dateExpiration')
            ->where('dateExpiration', '<', Carbon::now())
            ->where('quantiteRestante', '>', 0)
            ->where('Masquer', 0)
            ->count();

        $dateLimite = Carbon::now()->addDays(30);
        $this->alertesExpireBientot = LotMedicament::whereHas('stock', function($q) use ($cabinetId) {
                $q->where('fkidCabinet', $cabinetId);
            })
            ->whereNotNull('dateExpiration')
            ->where('dateExpiration', '>=', Carbon::now())
            ->where('dateExpiration', '<=', $dateLimite)
            ->where('quantiteRestante', '>', 0)
            ->where('Masquer', 0)
            ->count();
    }

    // ========== TABLEAU DE BORD ==========

    public function getStatistiquesDashboardProperty()
    {
        $cabinetId = Auth::user()->fkidcabinet;
        
        // Total des médicaments en stock
        $totalMedicaments = StockMedicament::where('fkidCabinet', $cabinetId)
            ->where('Masquer', 0)
            ->whereHas('medicament', function($q) {
                $q->where('fkidtype', 1);
            })
            ->count();

        // Médicaments en rupture de stock
        $medicamentsRupture = StockMedicament::where('fkidCabinet', $cabinetId)
            ->where('quantiteStock', '<=', 0)
            ->where('Masquer', 0)
            ->whereHas('medicament', function($q) {
                $q->where('fkidtype', 1);
            })
            ->count();

        // Médicaments avec stock faible
        $medicamentsStockFaible = StockMedicament::where('fkidCabinet', $cabinetId)
            ->whereColumn('quantiteStock', '<=', 'quantiteMin')
            ->where('quantiteStock', '>', 0)
            ->where('Masquer', 0)
            ->whereHas('medicament', function($q) {
                $q->where('fkidtype', 1);
            })
            ->count();

        // Valeur totale du stock (quantité * prix d'achat moyen)
        $valeurStock = StockMedicament::where('fkidCabinet', $cabinetId)
            ->where('Masquer', 0)
            ->whereHas('medicament', function($q) {
                $q->where('fkidtype', 1);
            })
            ->selectRaw('SUM(quantiteStock * prixAchat) as total')
            ->value('total') ?? 0;

        // Total des quantités en stock
        $totalQuantiteStock = StockMedicament::where('fkidCabinet', $cabinetId)
            ->where('Masquer', 0)
            ->whereHas('medicament', function($q) {
                $q->where('fkidtype', 1);
            })
            ->sum('quantiteStock');

        // Entrées ce mois
        $entreesCeMois = MouvementStock::whereHas('stock', function($q) use ($cabinetId) {
                $q->where('fkidCabinet', $cabinetId);
            })
            ->where('typeMouvement', 'ENTREE')
            ->whereMonth('dateMouvement', Carbon::now()->month)
            ->whereYear('dateMouvement', Carbon::now()->year)
            ->count();

        // Sorties ce mois
        $sortiesCeMois = MouvementStock::whereHas('stock', function($q) use ($cabinetId) {
                $q->where('fkidCabinet', $cabinetId);
            })
            ->where('typeMouvement', 'SORTIE')
            ->whereMonth('dateMouvement', Carbon::now()->month)
            ->whereYear('dateMouvement', Carbon::now()->year)
            ->count();

        // Lots expirés
        $lotsExpires = LotMedicament::whereHas('stock', function($q) use ($cabinetId) {
                $q->where('fkidCabinet', $cabinetId);
            })
            ->whereNotNull('dateExpiration')
            ->where('dateExpiration', '<', Carbon::now())
            ->where('quantiteRestante', '>', 0)
            ->where('Masquer', 0)
            ->count();

        // Lots expirant dans 30 jours
        $dateLimite = Carbon::now()->addDays(30);
        $lotsExpireBientot = LotMedicament::whereHas('stock', function($q) use ($cabinetId) {
                $q->where('fkidCabinet', $cabinetId);
            })
            ->whereNotNull('dateExpiration')
            ->where('dateExpiration', '>=', Carbon::now())
            ->where('dateExpiration', '<=', $dateLimite)
            ->where('quantiteRestante', '>', 0)
            ->where('Masquer', 0)
            ->count();

        return [
            'totalMedicaments' => $totalMedicaments,
            'medicamentsRupture' => $medicamentsRupture,
            'medicamentsStockFaible' => $medicamentsStockFaible,
            'valeurStock' => $valeurStock,
            'totalQuantiteStock' => $totalQuantiteStock,
            'entreesCeMois' => $entreesCeMois,
            'sortiesCeMois' => $sortiesCeMois,
            'lotsExpires' => $lotsExpires,
            'lotsExpireBientot' => $lotsExpireBientot,
        ];
    }

    public function getMedicamentsProperty()
    {
        return Medicament::where('fkidtype', 1) // Uniquement les médicaments
            ->orderBy('LibelleMedic', 'asc')
            ->get();
    }
    
    // ========== MÉTHODES POUR LES MODALS D'ALERTES ==========
    
    public function ouvrirModalRupture()
    {
        $this->showModalRupture = true;
    }
    
    public function fermerModalRupture()
    {
        $this->showModalRupture = false;
    }
    
    public function ouvrirModalStockFaible()
    {
        $this->showModalStockFaible = true;
    }
    
    public function fermerModalStockFaible()
    {
        $this->showModalStockFaible = false;
    }
    
    public function ouvrirModalLotsExpires()
    {
        $this->showModalLotsExpires = true;
    }
    
    public function fermerModalLotsExpires()
    {
        $this->showModalLotsExpires = false;
    }
    
    public function ouvrirModalExpireBientot()
    {
        $this->showModalExpireBientot = true;
    }
    
    public function fermerModalExpireBientot()
    {
        $this->showModalExpireBientot = false;
    }
    
    // Propriétés calculées pour les détails
    public function getMedicamentsRuptureProperty()
    {
        $cabinetId = Auth::user()->fkidcabinet;
        return StockMedicament::with(['medicament'])
            ->where('fkidCabinet', $cabinetId)
            ->where('quantiteStock', '<=', 0)
            ->where('Masquer', 0)
            ->whereHas('medicament', function($q) {
                $q->where('fkidtype', 1);
            })
            ->orderBy('quantiteStock', 'asc')
            ->get();
    }
    
    public function getMedicamentsStockFaibleProperty()
    {
        $cabinetId = Auth::user()->fkidcabinet;
        return StockMedicament::with(['medicament'])
            ->where('fkidCabinet', $cabinetId)
            ->whereColumn('quantiteStock', '<=', 'quantiteMin')
            ->where('quantiteStock', '>', 0)
            ->where('Masquer', 0)
            ->whereHas('medicament', function($q) {
                $q->where('fkidtype', 1);
            })
            ->orderBy('quantiteStock', 'asc')
            ->get();
    }
    
    public function getLotsExpiresProperty()
    {
        $cabinetId = Auth::user()->fkidcabinet;
        return LotMedicament::with(['stock.medicament'])
            ->whereHas('stock', function($q) use ($cabinetId) {
                $q->where('fkidCabinet', $cabinetId);
            })
            ->whereNotNull('dateExpiration')
            ->where('dateExpiration', '<', Carbon::now())
            ->where('quantiteRestante', '>', 0)
            ->where('Masquer', 0)
            ->orderBy('dateExpiration', 'asc')
            ->get();
    }
    
    public function getLotsExpireBientotProperty()
    {
        $cabinetId = Auth::user()->fkidcabinet;
        $dateLimite = Carbon::now()->addDays(30);
        return LotMedicament::with(['stock.medicament'])
            ->whereHas('stock', function($q) use ($cabinetId) {
                $q->where('fkidCabinet', $cabinetId);
            })
            ->whereNotNull('dateExpiration')
            ->where('dateExpiration', '>=', Carbon::now())
            ->where('dateExpiration', '<=', $dateLimite)
            ->where('quantiteRestante', '>', 0)
            ->where('Masquer', 0)
            ->orderBy('dateExpiration', 'asc')
            ->get();
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
        $medicament = Medicament::find($medicamentId);
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
                'motif' => 'Vente via facture pharmacie',
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
                'motif' => 'Vente via facture pharmacie' . ($lots->isEmpty() ? ' (sans lot)' : ' (stock sans lot)'),
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
     * Annuler une facture de pharmacie et remettre les articles au stock
     */
    public function annulerFacture($factureId)
    {
        try {
            DB::beginTransaction();

            $facture = Facture::find($factureId);
            if (!$facture) {
                throw new \Exception('Facture non trouvée.');
            }

            // Vérifier que la facture n'est pas payée (ou partiellement payée)
            $montantTotal = $facture->ISTP > 0 ? $facture->TotalfactPatient : $facture->TotFacture;
            $montantPaye = $facture->TotReglPatient + ($facture->ReglementPEC ?? 0);
            
            if ($montantPaye > 0) {
                throw new \Exception('Impossible d\'annuler une facture qui a déjà été payée. Montant payé: ' . number_format($montantPaye, 0) . ' MRU');
            }

            // Vérifier que c'est une facture de pharmacie (uniquement des médicaments)
            $detailsMedicaments = Detailfacturepatient::where('fkidfacture', $factureId)
                ->where('IsAct', 2)
                ->whereNotNull('fkidmedicament')
                ->get();

            if ($detailsMedicaments->isEmpty()) {
                throw new \Exception('Cette facture ne contient pas de médicaments.');
            }

            // Remettre le stock pour chaque médicament
            foreach ($detailsMedicaments as $detail) {
                $this->remettreStockMedicament($detail->fkidmedicament, $detail->Quantite, $factureId, $detail->idDetfacture);
            }

            // Supprimer tous les détails de la facture (tous les types, pas seulement les médicaments)
            $detailsSupprimes = Detailfacturepatient::where('fkidfacture', $factureId)->delete();

            // Supprimer ou mettre à jour les mouvements de stock liés à cette facture
            // On met à jour les références plutôt que de supprimer pour garder l'historique
            \App\Models\MouvementStock::where('fkidFacture', $factureId)
                ->update(['fkidFacture' => null, 'fkidDetailFacture' => null]);

            // Supprimer la facture
            $facture->delete();

            DB::commit();
            session()->flash('message', 'Facture annulée avec succès. Les articles ont été remis au stock.');
            $this->calculerAlertes();

        } catch (\Exception $e) {
            DB::rollBack();
            $errorMessage = 'Erreur lors de l\'annulation de la facture : ' . $e->getMessage();
            session()->flash('error', $errorMessage);
            
            $this->dispatchBrowserEvent('pharmacie-error', [
                'message' => $errorMessage
            ]);
        }
    }

    /**
     * Remettre le stock d'un médicament lors de l'annulation d'une facture
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

        // Remettre le stock dans les lots si possible
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
                'motif' => 'Annulation facture pharmacie - Remise de stock',
                'fkidFacture' => $factureId,
                'fkidDetailFacture' => $detailFactureId,
                'fkidPatient' => $patientId,
                'fkidUser' => Auth::id(),
                'dateMouvement' => Carbon::now(),
                'reference' => $facture->Nfacture ?? null,
                'notes' => 'Remise de stock suite à annulation de la facture'
            ]);
        }

        // Si pas de mouvements trouvés (cas rare), créer un ajustement direct
        if ($mouvementsSortie->isEmpty()) {
            $medicament = Medicament::find($medicamentId);
            $prixVente = $medicament ? ($medicament->PrixRef ?? 0) : 0;

            MouvementStock::create([
                'fkidStock' => $stock->idStock,
                'fkidMedicament' => $medicamentId,
                'fkidLot' => null,
                'typeMouvement' => 'AJUSTEMENT',
                'quantite' => $quantite,
                'prixUnitaire' => $prixVente,
                'montantTotal' => $prixVente * $quantite,
                'motif' => 'Annulation facture pharmacie - Remise de stock (sans mouvement de sortie)',
                'fkidFacture' => $factureId,
                'fkidDetailFacture' => $detailFactureId,
                'fkidPatient' => $patientId,
                'fkidUser' => Auth::id(),
                'dateMouvement' => Carbon::now(),
                'reference' => $facture->Nfacture ?? null,
                'notes' => 'Remise de stock suite à annulation de la facture (aucun mouvement de sortie trouvé)'
            ]);
        }

        // Mettre à jour le stock global
        $stock->quantiteStock += $quantite;
        $stock->dateDerniereEntree = Carbon::now();
        $stock->save();
    }

    public function render()
    {
        return view('livewire.pharmacie-manager', [
            'stocks' => $this->stocks,
            'mouvements' => $this->mouvements,
            'medicaments' => $this->medicaments
        ]);
    }
}
