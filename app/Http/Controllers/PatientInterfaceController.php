<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rendezvou;
use App\Models\Patient;
use App\Models\Consultation;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PatientInterfaceController extends Controller
{
    /**
     * Affiche les rendez-vous d'un patient via token
     */
    public function showRendezVous($token)
    {
        try {
            // Décoder le token pour obtenir l'ID du patient
            $patientId = $this->decodeToken($token);
            
            if (!$patientId) {
                return view('patient.error', ['message' => 'Token invalide ou expiré']);
            }

            // Extraire la date et l'ID du médecin du token
            $dateToken = $this->getDateFromToken($token);
            $medecinIdFromToken = $this->getMedecinIdFromToken($token);
            
            // Debug pour voir les dates
            \Log::info('Date Token: ' . $dateToken);
            \Log::info('Date Aujourd\'hui: ' . now()->format('Y-m-d'));

            $patient = Patient::find($patientId);
            
            if (!$patient) {
                return view('patient.error', ['message' => 'Patient non trouvé']);
            }

            // Récupérer tous les rendez-vous du patient
            $rendezVous = Rendezvou::with(['medecin', 'cabinet'])
                ->where('fkidPatient', $patientId)
                ->orderBy('dtPrevuRDV', 'desc')
                ->get();

            // Récupérer le rendez-vous spécifique pour la date du token et le médecin
            $query = Rendezvou::with(['medecin', 'cabinet'])
                ->where('fkidPatient', $patientId)
                ->whereDate('dtPrevuRDV', $dateToken)
                ->whereNotIn('rdvConfirmer', ['Annulé', 'annulé']);
            
            // Si l'ID du médecin est dans le token, filtrer par médecin
            if ($medecinIdFromToken) {
                $query->where('fkidMedecin', $medecinIdFromToken);
            }
            
            $prochainRdv = $query->orderBy('OrdreRDV', 'asc')->first();
            


            // Récupérer les rendez-vous du médecin pour la journée
            $rendezVousMedecinJournee = collect();
            $estAujourdhui = false;
            
            // Gestion des contraintes temporelles
            $dateAujourdhui = now()->format('Y-m-d');
            $estAujourdhui = false;
            $estFutur = false;
            $estPasse = false;
            $messageContrainte = null;
            
            if ($prochainRdv) {
                // Comparer les dates - s'assurer que les deux sont au même format
                $dateRdv = $prochainRdv->dtPrevuRDV;
                $dateRdvFormatted = is_string($dateRdv) ? $dateRdv : $dateRdv->format('Y-m-d');
                
                // Debug pour voir les valeurs
                \Log::info('Date RDV: ' . $dateRdvFormatted);
                \Log::info('Date Aujourd\'hui: ' . $dateAujourdhui);
                
                $estAujourdhui = ($dateRdvFormatted === $dateAujourdhui);
                $estFutur = ($dateRdvFormatted > $dateAujourdhui);
                $estPasse = ($dateRdvFormatted < $dateAujourdhui);
                
                // Debug pour voir les résultats
                \Log::info('Est Aujourd\'hui: ' . ($estAujourdhui ? 'true' : 'false'));
                \Log::info('Est Futur: ' . ($estFutur ? 'true' : 'false'));
                \Log::info('Est Passé: ' . ($estPasse ? 'true' : 'false'));
                
                // Définir les messages selon la date
                if ($estFutur) {
                    $messageContrainte = 'Attendre le jour de votre RDV';
                } elseif ($estPasse) {
                    $messageContrainte = 'Votre RDV a dépassé et le lien est expiré';
                }
                
                // Afficher la file d'attente seulement si le rendez-vous est aujourd'hui
                if ($estAujourdhui) {
                    // Récupérer TOUS les rendez-vous de la journée pour le médecin (y compris terminés)
                    $rendezVousMedecinJournee = Rendezvou::with(['patient', 'medecin'])
                        ->where('fkidMedecin', $prochainRdv->fkidMedecin)
                        ->whereDate('dtPrevuRDV', $dateToken)
                        ->whereNotIn('rdvConfirmer', ['Annulé', 'annulé'])
                        ->orderBy('OrdreRDV', 'asc')
                        ->get();
                }
            }

            $fileAttente = null;
            $positionPatient = null;
            $tempsAttenteEstime = null;
            $patientEnCours = null;
            $positionPatientEnCours = null;
            $patientsAvantMoi = 0;

            if ($prochainRdv && $estAujourdhui) {
                // Utiliser directement l'ordreRDV du patient comme position
                $positionPatient = $prochainRdv->OrdreRDV;
                
                // Calculer le nombre de patients avant ce patient dans la file d'attente
                // Utiliser $rendezVousMedecinJournee qui contient tous les RDV (sauf annulés)
                $patientsAvantMoi = $rendezVousMedecinJournee->filter(function($rdv) use ($prochainRdv) {
                    return $rdv->OrdreRDV < $prochainRdv->OrdreRDV && 
                           !in_array($rdv->rdvConfirmer, ['Terminé', 'terminé']);
                })->count();
                
                // Estimer le temps d'attente basé sur les patients réellement en attente
                $tempsAttenteEstime = $patientsAvantMoi * 15; // 15 minutes par patient en moyenne

                // Trouver le patient en cours de traitement
                $patientEnCours = $rendezVousMedecinJournee->first(function($rdv) {
                    return $rdv->rdvConfirmer == 'En cours';
                });

                if ($patientEnCours) {
                    $positionPatientEnCours = $patientEnCours->OrdreRDV;
                }
                
                // $fileAttente est maintenant identique à $rendezVousMedecinJournee pour la cohérence
                $fileAttente = $rendezVousMedecinJournee;
            }

            return view('patient.rendez-vous', compact(
                'patient', 
                'rendezVous', 
                'rendezVousMedecinJournee',
                'prochainRdv', 
                'fileAttente', 
                'positionPatient', 
                'tempsAttenteEstime',
                'patientEnCours',
                'positionPatientEnCours',
                'patientsAvantMoi',
                'estAujourdhui',
                'estFutur',
                'estPasse',
                'messageContrainte'
            ));
            
        } catch (\Exception $e) {
            return view('patient.error', ['message' => 'Erreur lors du chargement des données']);
        }
    }

    /**
     * Affiche les consultations d'un patient via token
     */
    public function showConsultation($token)
    {
        try {
            // Décoder le token pour obtenir l'ID du patient
            $patientId = $this->decodeToken($token);
            
            if (!$patientId) {
                return view('patient.error', ['message' => 'Token invalide ou expiré']);
            }

            $patient = Patient::find($patientId);
            
            if (!$patient) {
                return view('patient.error', ['message' => 'Patient non trouvé']);
            }

            // Récupérer toutes les consultations du patient
            $consultations = Consultation::with(['medecin', 'cabinet'])
                ->where('fkidPatient', $patientId)
                ->orderBy('DateConsultation', 'desc')
                ->get();

            return view('patient.consultation', compact('patient', 'consultations'));
            
        } catch (\Exception $e) {
            return view('patient.error', ['message' => 'Erreur lors du chargement des données']);
        }
    }

    /**
     * Génère un token sécurisé pour un patient
     * @param int $patientId ID du patient
     * @param string|null $dateRendezVous Date du rendez-vous (Y-m-d), si null utilise aujourd'hui
     * @param int|null $medecinId ID du médecin (optionnel pour compatibilité)
     */
    public static function generateToken($patientId, $dateRendezVous = null, $medecinId = null)
    {
        // Utiliser la date du rendez-vous ou la date du jour par défaut
        $dateToken = $dateRendezVous ? date('Y-m-d', strtotime($dateRendezVous)) : date('Y-m-d');
        
        if ($medecinId) {
            // Nouveau format : patientId|date|medecinId
            $data = $patientId . '|' . $dateToken . '|' . $medecinId;
        } else {
            // Ancien format : patientId|date (pour compatibilité)
            $data = $patientId . '|' . $dateToken;
        }
        
        return base64_encode($data);
    }

    /**
     * Extrait la date du token
     */
    private function getDateFromToken($token)
    {
        try {
            $decoded = base64_decode($token);
            $parts = explode('|', $decoded);
            
            if (count($parts) >= 2) {
                return $parts[1]; // Retourne la date
            }
            
            return date('Y-m-d'); // Fallback à aujourd'hui pour les anciens tokens
        } catch (\Exception $e) {
            return date('Y-m-d');
        }
    }

    /**
     * Extrait l'ID du médecin du token
     */
    private function getMedecinIdFromToken($token)
    {
        try {
            $decoded = base64_decode($token);
            $parts = explode('|', $decoded);
            
            if (count($parts) === 3) {
                return $parts[2]; // Retourne l'ID du médecin
            }
            
            return null; // Pas d'ID médecin dans l'ancien format
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Décode un token pour obtenir l'ID du patient
     */
    private function decodeToken($token)
    {
        try {
            $decoded = base64_decode($token);
            
            // Essayer d'abord le nouveau format (patientId|date|medecinId)
            $parts = explode('|', $decoded);
            
            if (count($parts) >= 2) {
                $patientId = $parts[0];
                $dateToken = $parts[1];

                // Le token est valide, on retourne l'ID du patient
                // La vérification de la date se fait dans showRendezVous pour afficher le bon contenu
                return $patientId;
            }
            
            // Si ce n'est pas le nouveau format, essayer l'ancien format (juste la date)
            // Pour la compatibilité avec les anciens QR codes
            $dateToken = $decoded;
            $dateDuJour = date('Y-m-d');
            
            if ($dateToken === $dateDuJour) {
                // Pour les anciens QR codes, on doit récupérer l'ID du patient différemment
                // On va chercher un patient qui a un rendez-vous aujourd'hui
                $patient = \App\Models\Rendezvou::where('dtPrevuRDV', $dateDuJour)
                    ->with('patient')
                    ->first();
                
                return $patient ? $patient->fkidPatient : null;
            }

            return null;
            
        } catch (\Exception $e) {
            return null;
        }
    }
} 