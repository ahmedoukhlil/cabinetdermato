<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Rendezvou;
use App\Models\Patient;
use App\Models\Medecin;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Helpers\QrCodeHelper;
use Illuminate\Support\Facades\Cache;

class RdvReminders extends Component
{
    use WithPagination;

    // Propriétés pour les filtres
    public $dateFilter = '';
    public $medecinFilter = '';
    public $searchPatient = '';
    
    // Propriétés pour les permissions
    public $isDocteurProprietaire = false;
    public $isSecretaire = false;
    public $isDocteur = false;
    public $canViewAllRdv = false;

    // Propriétés pour les médecins
    public $medecins = [];

    // Propriété pour suivre les rappels envoyés
    public $sentReminders = [];
    
    // Propriétés pour WhatsApp
    public $whatsappUrl = '';
    public $showWhatsAppModal = false;

    protected $listeners = [
        'refreshReminders' => '$refresh'
    ];

    public function mount()
    {
        $this->initializePermissions();
        $this->loadMedecins();
        $this->dateFilter = now()->addDay()->format('Y-m-d'); // Demain par défaut
    }

    protected function initializePermissions()
    {
        $user = Auth::user();
        
        $this->isDocteurProprietaire = $user->isDocteurProprietaire();
        $this->isSecretaire = $user->isSecretaire();
        $this->isDocteur = $user->isDocteur() && !$user->isDocteurProprietaire();
        $this->canViewAllRdv = $this->isDocteurProprietaire || $this->isSecretaire;
    }

    protected function loadMedecins()
    {
        $this->medecins = Cache::remember('medecins_for_reminders_' . Auth::user()->fkidcabinet, 1800, function () {
            $query = Medecin::select('idMedecin', 'Nom')
                ->orderBy('Nom');
            
            // Filtrer par cabinet si l'utilisateur n'est pas admin
            if (!Auth::user()->isDocteurProprietaire()) {
                $query->where('fkidcabinet', Auth::user()->fkidcabinet);
            }
            
            return $query->get();
        });
    }

    public function sendReminder($rdvId)
    {
        try {
            $rdv = Rendezvou::with(['patient', 'medecin'])->find($rdvId);
            
            if (!$rdv) {
                session()->flash('error', 'Rendez-vous non trouvé.');
                return;
            }

            if (!$rdv->patient) {
                session()->flash('error', 'Patient non trouvé pour ce rendez-vous.');
                return;
            }

            // Vérifier que le patient a un numéro de téléphone
            if (empty($rdv->patient->Telephone1)) {
                session()->flash('error', 'Le patient n\'a pas de numéro de téléphone enregistré.');
                return;
            }

            // Vérifier si c'est un relancement (le statut était déjà "Rappel envoyé")
            $wasAlreadySent = $rdv->rdvConfirmer === 'Rappel envoyé';
            
            // Marquer le rappel comme envoyé
            $rdv->update([
                'rdvConfirmer' => 'Rappel envoyé'
            ]);
            
            // Ajouter à la liste des rappels envoyés dans cette session
            $this->sentReminders[$rdvId] = true;

            // Déterminer le message de succès selon si c'est un premier rappel ou un relancement
            $successMessage = $wasAlreadySent ? 
                'Relance WhatsApp envoyée pour ' . $rdv->patient->Nom :
                'Rappel WhatsApp envoyé pour ' . $rdv->patient->Nom;
            
            session()->flash('success', $successMessage);
            
            // Log pour débogage
            \Log::info('WhatsApp reminder sent', [
                'rdvId' => $rdvId,
                'patientName' => $rdv->patient->Nom,
                'isRelance' => $wasAlreadySent
            ]);
            
            // Forcer le rafraîchissement du composant pour mettre à jour l'interface
            $this->emit('$refresh');

        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de l\'envoi du rappel: ' . $e->getMessage());
        }
    }

    // Méthode pour vérifier si un rappel a été envoyé
    public function isReminderSent($rdvId)
    {
        return isset($this->sentReminders[$rdvId]) || 
               Rendezvou::where('IDRdv', $rdvId)
                        ->where('rdvConfirmer', 'Rappel envoyé')
                        ->exists();
    }

    protected function generateReminderMessage($rdv)
    {
        $patientName = $rdv->patient->Nom;
        $rdvDate = Carbon::parse($rdv->dtPrevuRDV)->format('d/m/Y');
        $rdvTime = Carbon::parse($rdv->HeureRdv)->format('H:i');
        $medecinName = $rdv->medecin ? 'Dr. ' . $rdv->medecin->Nom : 'le médecin';
        $acte = $rdv->ActePrevu ?: 'Consultation';

        // Générer le lien de suivi de la file d'attente
        $token = \App\Http\Controllers\PatientInterfaceController::generateToken(
            $rdv->patient->ID, 
            $rdv->dtPrevuRDV, 
            $rdv->fkidMedecin
        );
        $queueLink = url("/patient/rendez-vous/{$token}");

        // Message en arabe d'abord, puis français
        $message = "🔔 *تذكير بالموعد* 🔔\n\n";
        $message .= "مرحباً *{$patientName}*،\n\n";
        $message .= "نذكركم بموعدكم :\n";
        $message .= "📅 *التاريخ :* {$rdvDate}\n";
        $message .= "🕐 *الوقت :* {$rdvTime}\n";
        $message .= "👨‍⚕️ *الطبيب :* {$medecinName}\n";
        $message .= "🦷 *العملية :* {$acte}\n\n";
        $message .= "⚠️ *يرجى تأكيد حضوركم بالرد على هذه الرسالة.*\n\n";
        $message .= "*رابط متابعة طابور الانتظار:*\n";
        $message .= "هذا الرابط يسمح لك بمتابعة موقعك في طابور الانتظار يوم الموعد\n";
        $message .= "{$queueLink}\n\n";
        $message .= "───────────────────\n\n";
        $message .= "🔔 *RAPPEL RENDEZ-VOUS* 🔔\n\n";
        $message .= "Bonjour *{$patientName}*,\n\n";
        $message .= "Nous vous rappelons votre rendez-vous :\n";
        $message .= "📅 *Date :* {$rdvDate}\n";
        $message .= "🕐 *Heure :* {$rdvTime}\n";
        $message .= "👨‍⚕️ *Médecin :* {$medecinName}\n";
        $message .= "🦷 *Acte :* {$acte}\n\n";
        $message .= "⚠️ *Veuillez confirmer votre présence en répondant à ce message.*\n\n";
        $message .= "*Lien de suivi de la file d'attente:*\n";
        $message .= "Ce lien vous permet de suivre votre position dans la file d'attente le jour du rendez-vous\n";
        $message .= "{$queueLink}\n\n";
        $message .= "شكراً / Merci";

        return $message;
    }

    public function updatedDateFilter()
    {
        $this->resetPage();
    }

    public function updatedMedecinFilter()
    {
        $this->resetPage();
    }

    public function updatedSearchPatient()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Rendezvou::with([
            'patient:id,ID,Nom,Prenom,Telephone1,Telephone2',
            'medecin:idMedecin,Nom'
        ])
        ->select([
            'IDRdv', 'dtPrevuRDV', 'HeureRdv', 'OrdreRDV', 
            'ActePrevu', 'rdvConfirmer', 'fkidPatient', 'fkidMedecin', 'fkidcabinet'
        ])
            ->where('rdvConfirmer', '!=', 'Terminé')
        ->where('rdvConfirmer', '!=', 'Annulé');

        // Filtrer par date
        if ($this->dateFilter) {
            $query->whereDate('dtPrevuRDV', $this->dateFilter);
        }

        // Filtrer par médecin
        if ($this->medecinFilter) {
            $query->where('fkidMedecin', $this->medecinFilter);
        }

        // Si c'est un docteur simple, ne montrer que ses rendez-vous
        if ($this->isDocteur && !$this->canViewAllRdv) {
            $query->where('fkidMedecin', Auth::user()->fkidmedecin);
        }

        // Filtrer par cabinet
        $query->where('fkidcabinet', Auth::user()->fkidcabinet);

        // Recherche par patient - optimisée avec index
        if ($this->searchPatient) {
            $searchTerm = '%' . $this->searchPatient . '%';
            $query->whereHas('patient', function($q) use ($searchTerm) {
                $q->where(function($subQuery) use ($searchTerm) {
                    $subQuery->where('Nom', 'like', $searchTerm)
                             ->orWhere('Prenom', 'like', $searchTerm)
                             ->orWhere('Telephone1', 'like', $searchTerm);
                });
            });
        }

        // Trier par date et heure avec index
        $query->orderBy('dtPrevuRDV', 'asc')
              ->orderBy('HeureRdv', 'asc');

        $rendezVous = $query->paginate(15); // Augmenter légèrement pour réduire les requêtes

        return view('livewire.rdv-reminders', [
            'rendezVous' => $rendezVous
        ]);
    }
}
