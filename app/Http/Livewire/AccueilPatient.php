<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Patient;
use App\Models\Medecin;
use App\Models\Assureur;
use App\Models\RefTypePaiement;
use App\Models\Acte;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\TypeUser;
use App\Models\User;

class AccueilPatient extends Component
{
    use WithPagination;

    // Constantes pour les clés de cache
    const CACHE_TTL = 3600; // 1 heure
    const CACHE_KEY_DOCTORS = 'active_doctors';
    const CACHE_KEY_ASSUREURS = 'assureurs';
    const CACHE_KEY_PAYMENT_TYPES = 'types_paiement';
    const CACHE_KEY_ACTES = 'active_actes';

    // États des modales
    public $showCreateModal = false;
    public $showCreateRdvModal = false;
    public $showPatientListModal = false;
    public $showGestionPatientsModal = false;
    public $showNouveauPatientModal = false;
    public $showActeModal = false;
    public $showAssureurModal = false;
    public $showCreatePatientModal = false;
    public $showPatientDetails = false;
    public $showPaymentHistory = false;
    public $showEditModal = false;
    public $showDeleteConfirmation = false;
    public $showCreateActeModal = false;
    public $showListeActesModal = false;
    public $showListeMedicamentsModal = false;
    public $showCaisseOperations = false;
    public $showDepenses = false;
    public $showUsersModal = false;
    public $showStatistiques = false;
    public $showMedecinsModal = false;
    public $showTypePaiementModal = false;
    public $showCabinetMenu = false;
    public $showPatientMenu = false;
    public $showOrdonnanceModal = false;
    public $showPharmacie = false;
    public $showVentePharmacie = false;
    public $showFormulaireNouveauPatient = false;

    // NOUVELLES PROPRIÉTÉS pour les sous-sections patient - Même logique que les sections principales
    public $showConsultation = false;
    public $showReglement = false;
    public $showRendezVous = false;
    
    // Onglet actif pour le modal RDV
    public $activeRdvTab = 'create';
    
    // Nombre de RDV à rappeler
    public $rdvRemindersCount = 0;

    // États des actions et données
    public $action = null;
    public $isDocteurProprietaire = false;
    public $isDocteur = false;
    public $isSecretaire = false;
    public $canManageRdv = false;
    public $canViewAllRdv = false;
    public $selectedPatient = null;
    public $editingPatient;
    public $patientToDelete;

    // Données mises en cache
    public $medecins;
    public $assureurs;
    public $typesPaiement;
    public $actes;

    protected $listeners = [
        'patientSelected' => 'setPatient',
        'patientCleared' => 'clearSelectedPatient',
        'patientCreated' => 'handlePatientCreated',
        'closeCreateModal' => 'closeCreateModal',
        'closeNouveauPatientModal' => 'closeNouveauPatientModal',
        'assureurCreated' => 'handleAssureurCreated',
        'acteCreated' => 'handleActeCreated',
        'refreshData' => 'refreshCachedData',
        'openModal' => 'handleOpenModal',
        'refreshReminders' => 'calculateRdvRemindersCount',
        'ordonnanceCreated' => 'handleOrdonnanceCreated',
        'fermerOrdonnanceModal' => 'fermerOrdonnanceModal',
        'fermerConsultationModal' => 'fermerConsultationModal',
        'fermerReglementModal' => 'fermerReglementModal',
        'fermerRendezVousModal' => 'fermerRendezVousModal',
        'fermerCaisseOperationsModal' => 'fermerCaisseOperationsModal',
        'fermerDepensesModal' => 'fermerDepensesModal',
        'fermerStatistiquesModal' => 'fermerStatistiquesModal',
        'fermerPharmacieModal' => 'fermerPharmacieModal',
        'fermerVentePharmacieModal' => 'fermerVentePharmacieModal',
        'patientCreated' => 'handlePatientCreated',
        'ouvrirFacturationDepuisPharmacie' => 'ouvrirFacturationDepuisPharmacie',
        'ouvrirListeFacturesPharmacieDepuisVente' => 'ouvrirListeFacturesPharmacieDepuisVente'
    ];

    public function mount()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $this->initializeRoles();
        $this->loadCachedData();
        $this->preloadActes();
        $this->calculateRdvRemindersCount();
        
        // Initialisation explicite des propriétés de menu
        $this->showPatientMenu = false;
        $this->showCabinetMenu = false;

        if (request()->has('patient_id')) {
            $patient = Patient::find(request()->patient_id);
            if ($patient) {
                $this->setPatient($patient);
            }
        }
    }

    private function initializeRoles()
    {
        $user = Auth::user();
        
        $this->isDocteurProprietaire = $user->isDocteurProprietaire();
        $this->isDocteur = $user->isDocteur();
        $this->isSecretaire = $user->isSecretaire();

        $this->canManageRdv = $this->isDocteurProprietaire || $this->isSecretaire || $this->isDocteur;
        $this->canViewAllRdv = $this->isDocteurProprietaire || $this->isSecretaire;
    }

    private function loadCachedData()
    {
        $this->medecins = Cache::remember(self::CACHE_KEY_DOCTORS, self::CACHE_TTL, function () {
            $query = User::select(['Iduser', 'NomComplet', 'fkidmedecin', 'fkidcabinet'])
                ->whereHas('typeuser', function ($query) {
                    $query->whereIn('Libelle', ['Docteur Propriétaire', 'Docteur']);
                })->where('ismasquer', 0);

            if ($this->isDocteur) {
                $query->where('Iduser', Auth::id());
            }

            return $query->get();
        });

        $this->assureurs = Cache::remember(self::CACHE_KEY_ASSUREURS, self::CACHE_TTL, function () {
            return Assureur::select(['IDAssureur', 'LibAssurance', 'TauxdePEC', 'SeuilDevis'])->get();
        });

        $this->typesPaiement = Cache::remember(self::CACHE_KEY_PAYMENT_TYPES, self::CACHE_TTL, function () {
            return RefTypePaiement::select(['idtypepaie', 'LibPaie'])->get();
        });
    }

    private function preloadActes()
    {
        return Cache::remember(self::CACHE_KEY_ACTES, self::CACHE_TTL, function () {
            return Acte::select(['ID', 'Acte', 'PrixRef', 'nordre', 'Masquer'])
                       ->where('Masquer', 0)
                       ->orderBy('nordre')
                       ->get();
        });
    }

    public function handleOpenModal($modalName)
    {
        switch ($modalName) {
            case 'liste-actes':
                $this->showListeActesModal = true;
                break;
            // Ajoutez d'autres cas si nécessaire
        }
    }

    public function ouvrirListeActesModal()
    {
        $this->showListeActesModal = true;
        $this->showCaisseOperations = false;
        $this->showStatistiques = false;
        $this->showPharmacie = false;
        $this->emit('listeActesModalOpened');
    }

    public function fermerListeActesModal()
    {
        $this->showListeActesModal = false;
        $this->showListeMedicamentsModal = false;
    }

    public function ouvrirListeMedicamentsModal()
    {
        $this->showListeMedicamentsModal = true;
        $this->showCaisseOperations = false;
        $this->showStatistiques = false;
        $this->showPharmacie = false;
        $this->emit('listeMedicamentsModalOpened');
    }

    public function fermerListeMedicamentsModal()
    {
        $this->showListeMedicamentsModal = false;
    }

    public function refreshCachedData()
    {
        Cache::forget(self::CACHE_KEY_DOCTORS);
        Cache::forget(self::CACHE_KEY_ASSUREURS);
        Cache::forget(self::CACHE_KEY_PAYMENT_TYPES);
        Cache::forget(self::CACHE_KEY_ACTES);
        $this->loadCachedData();
        $this->preloadActes();
    }

    public function resetState()
    {
        $this->reset([
            'showCreateModal',
            'showPatientListModal',
            'showGestionPatientsModal',
            'showNouveauPatientModal',
            'showActeModal',
            'showAssureurModal',
            'showCreatePatientModal',
            'showPatientDetails',
            'showPaymentHistory',
            'showEditModal',
            'showDeleteConfirmation',
            'showCreateActeModal',
            'showListeActesModal',
            'showListeMedicamentsModal',
            'showCaisseOperations',
            'showDepenses',
            'showPatientMenu',
            'showCabinetMenu',
            'action',
            'editingPatient',
            'patientToDelete'
        ]);
        
        // Réinitialisation manuelle des propriétés qui ne sont pas dans reset()
        $this->showStatistiques = false;
        $this->showUsersModal = false;
        $this->showMedecinsModal = false;
        $this->showTypePaiementModal = false;
    }

    public function closeAllSections()
    {
        $this->showCaisseOperations = false;
        $this->showDepenses = false;
        $this->showStatistiques = false;
        $this->showPatientMenu = false;
        $this->showCabinetMenu = false;
        $this->action = null;
        $this->showAssureurModal = false;
        $this->showListeActesModal = false;
        $this->showListeMedicamentsModal = false;
        $this->showUsersModal = false;
        $this->showMedecinsModal = false;
        $this->showTypePaiementModal = false;
        $this->showPharmacie = false;
        $this->showVentePharmacie = false;
        $this->showFormulaireNouveauPatient = false;
        // Ne pas fermer showOrdonnanceModal ici, il sera géré par ouvrirOrdonnanceModal

        // NOUVELLES SOUS-SECTIONS PATIENT - Même logique que les sections principales
        $this->showConsultation = false;
        $this->showReglement = false;
        $this->showRendezVous = false;
    }

    public function setAction($action)
    {
        if (!$this->selectedPatient && in_array($action, ['consultation', 'reglement', 'rendezvous', 'dossier'])) {
            return;
        }
        $this->action = $action;
        $this->showCaisseOperations = false;
        $this->showDepenses = false;
        $this->showStatistiques = false;
        $this->showCabinetMenu = false;
        // Ne pas fermer le menu patient pour les actions du sous-menu
        // $this->showPatientMenu = false;
        $this->emit('actionChanged', $action);
    }

    // NOUVELLES MÉTHODES pour les sous-sections patient - Même logique que les sections principales
    public function showConsultation()
    {
        if (!$this->selectedPatient) return;
        
        // Fermer les autres modals mais garder le menu patient ouvert
        $this->showAssureurModal = false;
        $this->showListeActesModal = false;
        $this->showListeMedicamentsModal = false;
        $this->showUsersModal = false;
        $this->showMedecinsModal = false;
        $this->showTypePaiementModal = false;
        $this->showCreateRdvModal = false;
        $this->showCreatePatientModal = false;
        $this->showCaisseOperations = false;
        $this->showDepenses = false;
        $this->showStatistiques = false;
        $this->showOrdonnanceModal = false;
        $this->showReglement = false;
        $this->showRendezVous = false;
        $this->showVentePharmacie = false;
        
        // Ouvrir le modal consultation
        $this->showConsultation = true;
        $this->showPatientMenu = true;
        
        // Forcer le re-render
        $this->dispatchBrowserEvent('consultation-modal-opened');
    }

    public function fermerConsultationModal()
    {
        $this->showConsultation = false;
    }

    public function showReglement($patientId = null)
    {
        // Si un patientId est fourni, sélectionner ce patient
        if ($patientId && (!$this->selectedPatient || (is_array($this->selectedPatient) ? ($this->selectedPatient['ID'] ?? null) : ($this->selectedPatient->ID ?? null)) != $patientId)) {
            $patient = Patient::find($patientId);
            if ($patient) {
                $this->setPatient($patient);
            }
        }
        
        // Vérifier qu'un patient est sélectionné
        if (!$this->selectedPatient) {
            session()->flash('error', 'Veuillez sélectionner un patient pour voir ses factures.');
            return;
        }
        
        // Fermer les autres modals mais garder le menu patient ouvert
        $this->showAssureurModal = false;
        $this->showListeActesModal = false;
        $this->showListeMedicamentsModal = false;
        $this->showUsersModal = false;
        $this->showMedecinsModal = false;
        $this->showTypePaiementModal = false;
        $this->showCreateRdvModal = false;
        $this->showCreatePatientModal = false;
        $this->showCaisseOperations = false;
        $this->showDepenses = false;
        $this->showStatistiques = false;
        $this->showOrdonnanceModal = false;
        $this->showConsultation = false;
        $this->showRendezVous = false;
        $this->showVentePharmacie = false;
        
        // Ouvrir le modal reglement
        $this->showReglement = true;
        $this->showPatientMenu = true;
        
        // Forcer le re-render
        $this->dispatchBrowserEvent('reglement-modal-opened');
    }

    public function fermerReglementModal()
    {
        $this->showReglement = false;
    }

    public function ouvrirFacturationDepuisPharmacie($factureId)
    {
        // Fermer le modal de vente pharmacie
        $this->showVentePharmacie = false;
        
        // Ouvrir le modal de facturation
        $this->showReglement = true;
        
        // Attendre que le composant ReglementFacture soit monté avant d'émettre l'événement
        // Le composant est lazy, donc on utilise un événement browser avec un délai
        $this->dispatchBrowserEvent('ouvrir-facture-pharmacie', ['factureId' => $factureId]);
        
        // Émettre aussi l'événement Livewire avec un délai pour que le composant lazy soit chargé
        // On utilise un dispatchBrowserEvent pour déclencher l'émission après le chargement
    }

    public function ouvrirListeFacturesPharmacieDepuisVente($patientId)
    {
        // Fermer le modal de vente pharmacie
        $this->showVentePharmacie = false;
        $this->showPharmacie = false;
        
        // Sélectionner le patient si fourni
        if ($patientId) {
            $patient = Patient::find($patientId);
            if ($patient) {
                $this->setPatient($patient);
            }
        }
        
        // Vérifier qu'un patient est sélectionné
        if (!$this->selectedPatient) {
            session()->flash('error', 'Veuillez sélectionner un patient pour voir ses factures.');
            return;
        }
        
        // Marquer dans la session qu'on doit ouvrir l'onglet pharmacie
        // Cela sera lu par ReglementFacture lors de son mount()
        session()->put('ouvrir_onglet_pharmacie', true);
        
        // Stocker aussi l'ID du patient dans la session pour que ReglementFacture puisse le récupérer
        $patientIdToStore = is_array($this->selectedPatient) ? ($this->selectedPatient['ID'] ?? $patientId) : ($this->selectedPatient->ID ?? $patientId);
        session()->put('current_patient_id', $patientIdToStore);
        
        // Ouvrir le modal de règlement
        $this->showReglement = true;
        $this->showPatientMenu = true;
        
        // Émettre un événement browser pour déclencher l'ouverture de l'onglet pharmacie dans ReglementFacture
        // Le composant est lazy, donc on utilise un délai
        $this->dispatchBrowserEvent('ouvrir-factures-pharmacie', ['patientId' => $patientIdToStore]);
    }

    public function showRendezVous()
    {
        if (!$this->selectedPatient) return;
        
        // Fermer les autres modals mais garder le menu patient ouvert
        $this->showAssureurModal = false;
        $this->showListeActesModal = false;
        $this->showListeMedicamentsModal = false;
        $this->showUsersModal = false;
        $this->showMedecinsModal = false;
        $this->showTypePaiementModal = false;
        $this->showCreateRdvModal = false;
        $this->showCreatePatientModal = false;
        $this->showCaisseOperations = false;
        $this->showDepenses = false;
        $this->showStatistiques = false;
        $this->showOrdonnanceModal = false;
        $this->showConsultation = false;
        $this->showReglement = false;
        $this->showVentePharmacie = false;
        
        // Ouvrir le modal rendez-vous
        $this->showRendezVous = true;
        $this->showPatientMenu = true;
        
        // Forcer le re-render
        $this->dispatchBrowserEvent('rendez-vous-modal-opened');
    }

    public function fermerRendezVousModal()
    {
        $this->showRendezVous = false;
    }

    public function setPatient($patient)
    {
        $this->selectedPatient = $patient;
        $this->action = null;
        $this->showPatientMenu = false;
        $this->showCabinetMenu = false;
        
        // NOUVELLES SOUS-SECTIONS PATIENT - Même logique que les sections principales
        $this->showConsultation = false;
        $this->showReglement = false;
        $this->showRendezVous = false;
        
        // Réinitialiser l'état sans affecter le modal de création de rendez-vous
        $this->resetState();
    }

    public function clearSelectedPatient()
    {
        $this->selectedPatient = null;
        $this->showPatientMenu = false;
        $this->showConsultation = false;
        $this->showReglement = false;
        $this->showRendezVous = false;
        $this->showOrdonnanceModal = false;
    }

    // Gestionnaires d'événements
    public function handlePatientCreated($patientId = null)
    {
        // Fermer le modal après création
        $this->showNouveauPatientModal = false;
        $this->showFormulaireNouveauPatient = false;
        
        // Sélectionner le patient créé si un ID est fourni
        if ($patientId) {
            $patient = Patient::find($patientId);
            if ($patient) {
                $this->setPatient($patient);
            }
            $this->emit('patientSelected', $patientId);
        }
        
        // Fermer aussi l'ancien modal si ouvert
        $this->showCreatePatientModal = false;
    }

    public function handleAssureurCreated()
    {
        $this->refreshCachedData();
        $this->showAssureurModal = false;
    }

    public function handleActeCreated()
    {
        $this->refreshCachedData();
        $this->showActeModal = false;
    }

    // Méthodes de gestion des modales
    public function openGestionPatientsModal()
    {
        $this->showGestionPatientsModal = true;
    }

    public function closeGestionPatientsModal()
    {
        $this->showGestionPatientsModal = false;
    }

    public function openNouveauPatientModal()
    {
        $this->showNouveauPatientModal = true;
        // Émettre un événement Livewire pour ouvrir le modal de création dans PatientManager
        $this->emit('openPatientCreateModal');
    }

    public function closeNouveauPatientModal()
    {
        $this->showNouveauPatientModal = false;
    }

    public function showFormulaireNouveauPatient()
    {
        // Fermer les autres modals
        $this->showNouveauPatientModal = false;
        $this->showGestionPatientsModal = false;
        $this->showCreateRdvModal = false;
        $this->showAssureurModal = false;
        $this->showListeActesModal = false;
        $this->showListeMedicamentsModal = false;
        $this->showUsersModal = false;
        $this->showMedecinsModal = false;
        $this->showTypePaiementModal = false;
        $this->showCreateRdvModal = false;
        $this->showCreatePatientModal = false;
        $this->showCaisseOperations = false;
        $this->showDepenses = false;
        $this->showStatistiques = false;
        $this->showPharmacie = false;
        $this->showVentePharmacie = false;
        $this->showOrdonnanceModal = false;
        $this->showConsultation = false;
        $this->showReglement = false;
        $this->showRendezVous = false;
        $this->showPatientMenu = false;
        $this->showCabinetMenu = false;
        
        // Afficher le formulaire directement
        $this->showFormulaireNouveauPatient = true;
        
        // Émettre un événement pour ouvrir le formulaire dans PatientManager
        $this->emit('openPatientCreateForm');
    }

    public function closeFormulaireNouveauPatient()
    {
        $this->showFormulaireNouveauPatient = false;
    }

    public function showCreateRdv()
    {
        $this->showCreateRdvModal = true;
        // Précharger les données essentielles
        $this->dispatchBrowserEvent('modal-loading', ['loading' => true]);
        
        // Émettre un événement pour informer le composant CreateRendezVous du patient sélectionné
        if ($this->selectedPatient) {
            $this->emit('patientSelected', $this->selectedPatient);
        }
    }

    public function closeCreateRdvModal()
    {
        $this->showCreateRdvModal = false;
    }

    public function showCaisseOperations()
    {
        // Fermer les autres modals
        $this->showAssureurModal = false;
        $this->showListeActesModal = false;
        $this->showListeMedicamentsModal = false;
        $this->showUsersModal = false;
        $this->showMedecinsModal = false;
        $this->showTypePaiementModal = false;
        $this->showCreateRdvModal = false;
        $this->showCreatePatientModal = false;
        $this->showDepenses = false;
        $this->showStatistiques = false;
        $this->showPharmacie = false;
        $this->showOrdonnanceModal = false;
        $this->showConsultation = false;
        $this->showReglement = false;
        $this->showRendezVous = false;
        $this->showPatientMenu = false;
        $this->showCabinetMenu = false;
        
        // Ouvrir le modal caisse operations
        $this->showCaisseOperations = true;
        
        // Forcer le re-render
        $this->dispatchBrowserEvent('caisse-operations-modal-opened');
    }

    public function fermerCaisseOperationsModal()
    {
        $this->showCaisseOperations = false;
    }

    public function openDepenses()
    {
        // Fermer les autres modals
        $this->showAssureurModal = false;
        $this->showListeActesModal = false;
        $this->showListeMedicamentsModal = false;
        $this->showUsersModal = false;
        $this->showMedecinsModal = false;
        $this->showTypePaiementModal = false;
        $this->showCreateRdvModal = false;
        $this->showCreatePatientModal = false;
        $this->showCaisseOperations = false;
        $this->showStatistiques = false;
        $this->showPharmacie = false;
        $this->showOrdonnanceModal = false;
        $this->showConsultation = false;
        $this->showReglement = false;
        $this->showRendezVous = false;
        $this->showPatientMenu = false;
        $this->showCabinetMenu = false;
        
        // Ouvrir le modal depenses
        $this->showDepenses = true;
        
        // Forcer le re-render
        $this->dispatchBrowserEvent('depenses-modal-opened');
    }

    public function fermerDepensesModal()
    {
        $this->showDepenses = false;
    }

    public function ouvrirAssureurModal()
    {
        $this->showAssureurModal = true;
        $this->showStatistiques = false;
        $this->showPharmacie = false;
    }

    public function fermerAssureurModal()
    {
        $this->showAssureurModal = false;
    }

    public function showCreatePatient()
    {
        $this->showCreatePatientModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
    }

    public function openPatientListModal()
    {
        $this->showPatientListModal = true;
    }

    public function closePatientListModal()
    {
        $this->showPatientListModal = false;
    }

    public function ouvrirActeModal()
    {
        $this->showActeModal = true;
    }

    public function fermerActeModal()
    {
        $this->showActeModal = false;
    }

    public function openUsersModal()
    {
        $this->showUsersModal = true;
        $this->showStatistiques = false;
        $this->showPharmacie = false;
    }

    public function closeUsersModal()
    {
        $this->showUsersModal = false;
    }

    public function showStatistiques()
    {
        // Fermer les autres modals
        $this->showAssureurModal = false;
        $this->showListeActesModal = false;
        $this->showListeMedicamentsModal = false;
        $this->showUsersModal = false;
        $this->showMedecinsModal = false;
        $this->showTypePaiementModal = false;
        $this->showCreateRdvModal = false;
        $this->showCreatePatientModal = false;
        $this->showCaisseOperations = false;
        $this->showDepenses = false;
        $this->showPharmacie = false;
        $this->showOrdonnanceModal = false;
        $this->showConsultation = false;
        $this->showReglement = false;
        $this->showRendezVous = false;
        $this->showPatientMenu = false;
        $this->showCabinetMenu = false;
        
        // Ouvrir le modal statistiques
        $this->showStatistiques = true;
        
        // Forcer le re-render
        $this->dispatchBrowserEvent('statistiques-modal-opened');
    }

    public function fermerStatistiquesModal()
    {
        $this->showStatistiques = false;
    }

    public function ouvrirMedecinsModal()
    {
        $this->showMedecinsModal = true;
        $this->showPharmacie = false;
    }

    public function fermerMedecinsModal()
    {
        $this->showMedecinsModal = false;
    }

    public function ouvrirTypePaiementModal()
    {
        $this->showTypePaiementModal = true;
        $this->showPharmacie = false;
    }

    public function fermerTypePaiementModal()
    {
        $this->showTypePaiementModal = false;
    }

    public function showPharmacie()
    {
        // Fermer les autres modals
        $this->showAssureurModal = false;
        $this->showListeActesModal = false;
        $this->showListeMedicamentsModal = false;
        $this->showUsersModal = false;
        $this->showMedecinsModal = false;
        $this->showTypePaiementModal = false;
        $this->showCreateRdvModal = false;
        $this->showCreatePatientModal = false;
        $this->showCaisseOperations = false;
        $this->showDepenses = false;
        $this->showStatistiques = false;
        $this->showOrdonnanceModal = false;
        $this->showConsultation = false;
        $this->showReglement = false;
        $this->showRendezVous = false;
        $this->showPatientMenu = false;
        $this->showCabinetMenu = false;
        
        // Ouvrir le modal pharmacie
        $this->showPharmacie = true;
        
        // Forcer le re-render
        $this->dispatchBrowserEvent('pharmacie-modal-opened');
    }

    public function fermerPharmacieModal()
    {
        $this->showPharmacie = false;
    }

    public function showVentePharmacie()
    {
        if (!$this->selectedPatient) {
            session()->flash('error', 'Veuillez sélectionner un patient pour effectuer une vente.');
            return;
        }
        
        // Fermer les autres modals mais garder le menu patient ouvert
        $this->showAssureurModal = false;
        $this->showListeActesModal = false;
        $this->showListeMedicamentsModal = false;
        $this->showUsersModal = false;
        $this->showMedecinsModal = false;
        $this->showTypePaiementModal = false;
        $this->showCreateRdvModal = false;
        $this->showCreatePatientModal = false;
        $this->showCaisseOperations = false;
        $this->showDepenses = false;
        $this->showStatistiques = false;
        $this->showPharmacie = false;
        $this->showOrdonnanceModal = false;
        $this->showConsultation = false;
        $this->showReglement = false;
        $this->showRendezVous = false;
        
        // Ouvrir le modal vente pharmacie
        $this->showVentePharmacie = true;
        $this->showPatientMenu = true;
        
        // Forcer le re-render
        $this->dispatchBrowserEvent('vente-pharmacie-modal-opened');
    }

    public function fermerVentePharmacieModal()
    {
        $this->showVentePharmacie = false;
    }

    public function ouvrirOrdonnanceModal()
    {
        \Log::info('ouvrirOrdonnanceModal appelé', [
            'selectedPatient' => $this->selectedPatient ? 'existe' : 'null',
            'showOrdonnanceModal_avant' => $this->showOrdonnanceModal
        ]);

        if (!$this->selectedPatient) {
            session()->flash('error', 'Veuillez sélectionner un patient.');
            \Log::warning('Aucun patient sélectionné');
            return;
        }

        // Fermer les autres modals mais garder le menu patient ouvert
        $this->showAssureurModal = false;
        $this->showListeActesModal = false;
        $this->showListeMedicamentsModal = false;
        $this->showUsersModal = false;
        $this->showMedecinsModal = false;
        $this->showTypePaiementModal = false;
        $this->showCreateRdvModal = false;
        $this->showCreatePatientModal = false;
        $this->showCaisseOperations = false;
        $this->showDepenses = false;
        $this->showStatistiques = false;
        $this->showVentePharmacie = false;
        
        // Ouvrir le modal ordonnance
        $this->showOrdonnanceModal = true;
        
        // Forcer le re-render
        $this->dispatchBrowserEvent('ordonnance-modal-opened');
        
        \Log::info('showOrdonnanceModal mis à true', [
            'showOrdonnanceModal' => $this->showOrdonnanceModal
        ]);
    }

    public function fermerOrdonnanceModal()
    {
        $this->showOrdonnanceModal = false;
    }

    public function handleOrdonnanceCreated($ordonnanceId)
    {
        session()->flash('message', 'Ordonnance créée avec succès.');
    }

    public function toggleCabinetMenu()
    {
        $this->showCabinetMenu = !$this->showCabinetMenu;
        if ($this->showCabinetMenu) {
            $this->closeAllSections();
            $this->showCabinetMenu = true;
            // Ajouter un délai pour l'animation
            $this->dispatchBrowserEvent('menu-opened', ['menu' => 'cabinet']);
        } else {
            $this->dispatchBrowserEvent('menu-closed', ['menu' => 'cabinet']);
        }
    }

    public function togglePatientMenu()
    {
        // Vérifier si un patient est sélectionné
        if (!$this->selectedPatient) {
            return;
        }
        
        $this->showPatientMenu = !$this->showPatientMenu;
        if ($this->showPatientMenu) {
            $this->closeAllSections();
            $this->showPatientMenu = true;
            // Ajouter un délai pour l'animation
            $this->dispatchBrowserEvent('menu-opened', ['menu' => 'patient']);
        } else {
            $this->dispatchBrowserEvent('menu-closed', ['menu' => 'patient']);
        }
    }

    public function calculateRdvRemindersCount()
    {
        try {
            // Utiliser le cache pour le compteur de rappels (5 minutes)
            $cacheKey = 'rdv_reminders_count_' . Auth::id() . '_' . now()->format('Y-m-d');
            
            $this->rdvRemindersCount = Cache::remember($cacheKey, 300, function () {
                $query = \App\Models\Rendezvou::select('IDRdv')
                    ->where('rdvConfirmer', '!=', 'Terminé')
                    ->where('rdvConfirmer', '!=', 'Annulé')
                    ->where('fkidcabinet', Auth::user()->fkidcabinet);

                // Si c'est un docteur simple, ne compter que ses rendez-vous
                if ($this->isDocteur && !$this->canViewAllRdv) {
                    $query->where('fkidMedecin', Auth::user()->fkidmedecin);
                }

                // Compter les RDV de demain par défaut
                $tomorrow = now()->addDay()->format('Y-m-d');
                return $query->whereDate('dtPrevuRDV', $tomorrow)->count();
            });
        } catch (\Exception $e) {
            $this->rdvRemindersCount = 0;
        }
    }

    public function render()
    {
        return view('livewire.accueil-patient');
    }
} 