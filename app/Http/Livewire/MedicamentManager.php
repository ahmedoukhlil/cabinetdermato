<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Medicament;
use App\Models\StockMedicament;
use App\Models\LotMedicament;
use App\Models\MouvementStock;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MedicamentManager extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedType = '';
    public $perPage = 10;

    // Propriétés pour le formulaire
    public $medicamentId;
    public $libelleMedic;
    public $fkidtype;
    public $prixRef = 0;

    // Modals
    public $showModal = false;
    public $showDeleteModal = false;
    public $medicamentToDelete;
    public $showStockModal = false;
    
    // Propriétés pour l'ajout de stock
    public $stockMedicamentId;
    public $stockQuantite = 1;
    public $stockPrixAchat = 0;
    public $stockQuantiteMin = 0;
    public $stockNumeroLot = '';
    public $stockDateExpiration = null;
    public $stockFournisseur = '';
    public $stockReferenceFacture = '';

    protected function rules()
    {
        return [
            'libelleMedic' => 'required|min:3',
            'fkidtype' => 'required|integer|in:1,2,3', // 1 = Médicament, 2 = Analyse, 3 = Radio
            'prixRef' => 'nullable|numeric|min:0',
        ];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSelectedType()
    {
        $this->resetPage();
    }

    public function getTypesProperty()
    {
        // Types : 1 = Médicament, 2 = Analyse, 3 = Radio
        return [
            ['id' => 1, 'Type' => 'Médicament'],
            ['id' => 2, 'Type' => 'Analyse'],
            ['id' => 3, 'Type' => 'Radio'],
        ];
    }

    public function openModal($id = null)
    {
        $this->resetForm();
        if ($id) {
            $medicament = Medicament::find($id);
            if ($medicament) {
                $this->medicamentId = $medicament->IDMedic;
                $this->libelleMedic = $medicament->LibelleMedic;
                $this->fkidtype = $medicament->fkidtype;
                $this->prixRef = $medicament->PrixRef ?? 0;
            }
        }
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function save()
    {
        $this->validate();
        $data = [
            'LibelleMedic' => $this->libelleMedic,
            'fkidtype' => $this->fkidtype,
            'PrixRef' => $this->prixRef ?? 0,
        ];
        
        DB::beginTransaction();
        try {
            if ($this->medicamentId) {
                $medicament = Medicament::find($this->medicamentId);
                if ($medicament) {
                    $medicament->update($data);
                    session()->flash('message', 'Médicament modifié avec succès.');
                }
            } else {
                $medicament = Medicament::create($data);
                
                // Si c'est un médicament (fkidtype = 1), créer automatiquement le stock
                if ($medicament->fkidtype == 1) {
                    $cabinetId = Auth::user()->fkidcabinet ?? 1;
                    StockMedicament::firstOrCreate(
                        [
                            'fkidMedicament' => $medicament->IDMedic,
                            'fkidCabinet' => $cabinetId
                        ],
                        [
                            'quantiteStock' => 0,
                            'quantiteMin' => 0,
                            'prixAchat' => 0,
                            'prixVente' => $medicament->PrixRef ?? 0,
                            'Masquer' => 0
                        ]
                    );
                    session()->flash('message', 'Médicament créé avec succès. Le stock a été initialisé. Vous pouvez maintenant ajouter des quantités.');
                } else {
                    session()->flash('message', 'Médicament créé avec succès.');
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Erreur lors de l\'enregistrement : ' . $e->getMessage());
        }
        
        $this->closeModal();
    }

    public function confirmDelete($id)
    {
        $this->medicamentToDelete = $id;
        $this->showDeleteModal = true;
    }

    public function deleteMedicament()
    {
        $medicament = Medicament::find($this->medicamentToDelete);
        if ($medicament) {
            $medicament->delete();
            session()->flash('message', 'Médicament supprimé avec succès.');
        }
        $this->showDeleteModal = false;
        $this->medicamentToDelete = null;
    }

    public function resetForm()
    {
        $this->medicamentId = null;
        $this->libelleMedic = '';
        $this->fkidtype = '';
        $this->prixRef = 0;
    }

    public function openStockModal($medicamentId)
    {
        $medicament = Medicament::find($medicamentId);
        if (!$medicament || $medicament->fkidtype != 1) {
            session()->flash('error', 'Seuls les médicaments peuvent avoir un stock.');
            return;
        }
        
        $this->stockMedicamentId = $medicamentId;
        $cabinetId = Auth::user()->fkidcabinet ?? 1;
        $stock = StockMedicament::where('fkidMedicament', $medicamentId)
            ->where('fkidCabinet', $cabinetId)
            ->first();
        
        if ($stock) {
            $this->stockQuantiteMin = $stock->quantiteMin;
            $this->stockPrixAchat = $stock->prixAchat;
        } else {
            $this->stockQuantiteMin = 0;
            $this->stockPrixAchat = 0;
        }
        
        $this->stockQuantite = 1;
        $this->stockNumeroLot = '';
        $this->stockDateExpiration = null;
        $this->stockFournisseur = '';
        $this->stockReferenceFacture = '';
        $this->showStockModal = true;
    }

    public function closeStockModal()
    {
        $this->showStockModal = false;
        $this->resetStockFormComplete();
    }

    public function resetStockForm()
    {
        // Réinitialiser tous les champs sauf le médicament (les vider)
        $this->stockQuantite = null;
        $this->stockPrixAchat = null;
        $this->stockQuantiteMin = null;
        $this->stockNumeroLot = '';
        $this->stockDateExpiration = null;
        $this->stockFournisseur = '';
        $this->stockReferenceFacture = '';
    }

    public function resetStockFormComplete()
    {
        // Réinitialiser tous les champs y compris le médicament (pour la fermeture du modal)
        $this->stockMedicamentId = null;
        $this->stockQuantite = 1;
        $this->stockPrixAchat = 0;
        $this->stockQuantiteMin = 0;
        $this->stockNumeroLot = '';
        $this->stockDateExpiration = null;
        $this->stockFournisseur = '';
        $this->stockReferenceFacture = '';
    }

    public function saveStock()
    {
        $this->validate([
            'stockMedicamentId' => 'required|exists:medicaments,IDMedic',
            'stockQuantite' => 'required|integer|min:1',
            'stockPrixAchat' => 'nullable|numeric|min:0',
            'stockQuantiteMin' => 'required|integer|min:0',
        ], [
            'stockMedicamentId.required' => 'Veuillez sélectionner un médicament',
            'stockQuantite.required' => 'La quantité est requise',
            'stockPrixAchat.numeric' => 'Le prix d\'achat doit être un nombre',
            'stockPrixAchat.min' => 'Le prix d\'achat ne peut pas être négatif',
            'stockQuantiteMin.required' => 'Le seuil minimum est requis',
        ]);

        DB::transaction(function () {
            $cabinetId = Auth::user()->fkidcabinet ?? 1;
            $userId = Auth::id();

            // Récupérer ou créer le stock
            $stock = StockMedicament::firstOrCreate(
                [
                    'fkidMedicament' => $this->stockMedicamentId,
                    'fkidCabinet' => $cabinetId
                ],
                [
                    'quantiteStock' => 0,
                    'quantiteMin' => $this->stockQuantiteMin,
                    'prixAchat' => $this->stockPrixAchat,
                    'prixVente' => Medicament::find($this->stockMedicamentId)->PrixRef ?? 0,
                    'Masquer' => 0
                ]
            );

            // Mettre à jour le prix d'achat moyen (seulement si un prix est fourni)
            $prixAchatEntree = $this->stockPrixAchat ?? 0;
            if ($prixAchatEntree > 0 && $stock->quantiteStock > 0) {
                $nouveauPrixAchat = (($stock->prixAchat * $stock->quantiteStock) + ($prixAchatEntree * $this->stockQuantite)) 
                                    / ($stock->quantiteStock + $this->stockQuantite);
            } elseif ($prixAchatEntree > 0) {
                // Si c'est la première entrée avec un prix
                $nouveauPrixAchat = $prixAchatEntree;
            } else {
                // Si aucun prix n'est fourni, garder le prix actuel
                $nouveauPrixAchat = $stock->prixAchat;
            }

            // Créer le lot si date d'expiration renseignée
            $lotId = null;
            if ($this->stockDateExpiration) {
                $lot = LotMedicament::create([
                    'fkidStock' => $stock->idStock,
                    'fkidMedicament' => $this->stockMedicamentId,
                    'numeroLot' => $this->stockNumeroLot ?: null,
                    'quantiteInitiale' => $this->stockQuantite,
                    'quantiteRestante' => $this->stockQuantite,
                    'dateExpiration' => $this->stockDateExpiration,
                    'dateEntree' => Carbon::now(),
                    'prixAchatUnitaire' => $prixAchatEntree,
                    'fournisseur' => $this->stockFournisseur ?: null,
                    'referenceFacture' => $this->stockReferenceFacture ?: null,
                    'fkidUser' => $userId,
                    'Masquer' => 0
                ]);
                $lotId = $lot->idLot;
            }

            // Mettre à jour le stock
            $stock->update([
                'quantiteStock' => $stock->quantiteStock + $this->stockQuantite,
                'prixAchat' => $nouveauPrixAchat,
                'quantiteMin' => $this->stockQuantiteMin,
                'dateDerniereEntree' => Carbon::now()
            ]);

            // Créer le mouvement
            MouvementStock::create([
                'fkidStock' => $stock->idStock,
                'fkidMedicament' => $this->stockMedicamentId,
                'fkidLot' => $lotId,
                'typeMouvement' => 'ENTREE',
                'quantite' => $this->stockQuantite,
                'prixUnitaire' => $prixAchatEntree,
                'montantTotal' => $prixAchatEntree * $this->stockQuantite,
                'motif' => 'Entrée de stock',
                'fkidUser' => $userId,
                'dateMouvement' => Carbon::now(),
                'reference' => $this->stockReferenceFacture ?: null,
                'notes' => 'Ajout depuis la liste des médicaments'
            ]);
        });

        session()->flash('message', 'Stock ajouté avec succès.');
        // Réinitialiser les champs sauf le médicament pour permettre d'ajouter plus de stock
        $this->resetStockForm();
    }

    public function render()
    {
        $query = Medicament::orderBy('LibelleMedic');

        if ($this->search) {
            $query->where('LibelleMedic', 'like', '%' . $this->search . '%');
        }
        if ($this->selectedType) {
            $query->where('fkidtype', $this->selectedType);
        }

        $medicaments = $query->paginate($this->perPage);

        // Charger les stocks pour chaque médicament (uniquement pour les médicaments, fkidtype = 1)
        $cabinetId = Auth::user()->fkidcabinet ?? 1;
        $stocks = StockMedicament::where('fkidCabinet', $cabinetId)
            ->where('Masquer', 0)
            ->whereHas('medicament', function($q) {
                $q->where('fkidtype', 1); // Uniquement les médicaments
            })
            ->with('medicament')
            ->get()
            ->keyBy('fkidMedicament');

        // Ajouter la quantité en stock à chaque médicament
        $medicaments->getCollection()->transform(function($medicament) use ($stocks) {
            if ($medicament->fkidtype == 1) { // Uniquement pour les médicaments
                $stock = $stocks->get($medicament->IDMedic);
                $medicament->quantiteStock = $stock ? $stock->quantiteStock : 0;
                $medicament->quantiteMin = $stock ? $stock->quantiteMin : 0;
                $medicament->stockFaible = $stock ? ($stock->quantiteStock <= $stock->quantiteMin) : false;
            } else {
                $medicament->quantiteStock = null; // Pas de stock pour analyses/radios
                $medicament->quantiteMin = null;
                $medicament->stockFaible = false;
            }
            return $medicament;
        });

        return view('livewire.medicament-manager', [
            'medicaments' => $medicaments,
            'types' => $this->getTypesProperty(),
        ]);
    }
}
