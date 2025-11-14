<?php

namespace App\Http\Controllers;

use App\Models\Facture;
use App\Models\Fichetraitement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PDF;

class DossierMedicalController extends Controller
{
    /**
     * Afficher/Imprimer la fiche médicale d'un patient pour une facture
     */
    public function print($factureId)
    {
        try {
            \Log::info('Tentative d\'accès à la fiche médicale', ['facture_id' => $factureId]);
            
            // Utiliser le cache pour les données du cabinet
            $cabinet = cache()->remember('cabinet_info_' . Auth::id(), 3600, function() {
                $user = Auth::user();
                return [
                    'NomCabinet' => $user->cabinet->NomCabinet ?? null,
                    'Adresse' => $user->cabinet->Adresse ?? null,
                    'Telephone' => $user->cabinet->Telephone ?? null,
                    'Email' => $user->cabinet->Email ?? null
                ];
            });

            // Charger la facture avec ses relations
            $facture = Facture::with([
                'patient',
                'medecin',
                'reglements'
            ])->where('fkidCabinet', Auth::user()->fkidcabinet)
              ->findOrFail($factureId);

            // Charger toutes les fiches de traitement de cette facture
            $fichesTraitement = Fichetraitement::where('fkidfacture', $factureId)
                ->where('IsSupprimer', 0)
                ->orderBy('Ordre', 'asc')
                ->orderBy('dateTraite', 'asc')
                ->get();

            // Calculer le montant total payé pour cette facture
            $montantPaye = $facture->TotReglPatient ?? 0;

            \Log::info('Fiche médicale chargée', [
                'facture_id' => $facture->Idfacture,
                'patient_id' => $facture->IDPatient,
                'nb_fiches' => $fichesTraitement->count()
            ]);

            return view('dossier-medical.print', compact('facture', 'fichesTraitement', 'cabinet', 'montantPaye'));

        } catch (\Exception $e) {
            \Log::error('Erreur lors de l\'affichage de la fiche médicale', [
                'facture_id' => $factureId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Télécharger la fiche médicale en PDF
     */
    public function download($factureId)
    {
        try {
            // Utiliser le cache pour les données du cabinet
            $cabinet = cache()->remember('cabinet_info_' . Auth::id(), 3600, function() {
                $user = Auth::user();
                return [
                    'NomCabinet' => $user->cabinet->NomCabinet ?? null,
                    'Adresse' => $user->cabinet->Adresse ?? null,
                    'Telephone' => $user->cabinet->Telephone ?? null,
                    'Email' => $user->cabinet->Email ?? null
                ];
            });

            $facture = Facture::with([
                'patient',
                'medecin',
                'reglements'
            ])->where('fkidCabinet', Auth::user()->fkidcabinet)
              ->findOrFail($factureId);

            $fichesTraitement = Fichetraitement::where('fkidfacture', $factureId)
                ->where('IsSupprimer', 0)
                ->orderBy('Ordre', 'asc')
                ->orderBy('dateTraite', 'asc')
                ->get();

            $montantPaye = $facture->TotReglPatient ?? 0;

            $pdf = PDF::loadView('dossier-medical.print', compact('facture', 'fichesTraitement', 'cabinet', 'montantPaye'));

            return $pdf->download('fiche-medicale-' . $facture->Nfacture . '.pdf');

        } catch (\Exception $e) {
            \Log::error('Erreur téléchargement fiche médicale', [
                'facture_id' => $factureId,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Erreur lors du téléchargement de la fiche médicale.');
        }
    }
}

