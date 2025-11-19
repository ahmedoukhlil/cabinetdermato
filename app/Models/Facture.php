<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Facture
 * 
 * @property int $Idfacture
 * @property string|null $Nfacture
 * @property int|null $anneeFacture
 * @property int|null $nordre
 * @property Carbon|null $DtFacture
 * @property int|null $IDPatient
 * @property int|null $ISTP
 * @property int|null $fkidEtsAssurance
 * @property float $TXPEC
 * @property float $TotFacture
 * @property float $TotalPEC
 * @property float $TotalfactPatient
 * @property float $TotReglPatient
 * @property float $ReglementPEC
 * @property string|null $ModeReglement
 * @property string|null $Areglepar
 * @property Carbon|null $DtReglement
 * @property float $fkidbordfacture
 * @property int $ispayerAssureur
 * @property string|null $user
 * @property int $estfacturer
 * @property int $FkidMedecinInitiateur
 * @property float $PartLaboratoire
 * @property float $MontantAffectation
 * @property string $Type
 * @property int $fkidCabinet
 *
 * @package App\Models
 */
class Facture extends Model
{
	protected $table = 'facture';
	protected $primaryKey = 'Idfacture';
	public $timestamps = false;

	protected $casts = [
		'anneeFacture' => 'int',
		'nordre' => 'int',
		'DtFacture' => 'datetime',
		'IDPatient' => 'int',
		'ISTP' => 'int',
		'fkidEtsAssurance' => 'int',
		'TXPEC' => 'float',
		'TotFacture' => 'float',
		'TotalPEC' => 'float',
		'TotalfactPatient' => 'float',
		'TotReglPatient' => 'float',
		'ReglementPEC' => 'float',
		'DtReglement' => 'datetime',
		'fkidbordfacture' => 'float',
		'ispayerAssureur' => 'int',
		'estfacturer' => 'int',
		'FkidMedecinInitiateur' => 'int',
		'PartLaboratoire' => 'float',
		'MontantAffectation' => 'float',
		'fkidCabinet' => 'int'
	];

	protected $fillable = [
		'Nfacture',
		'anneeFacture',
		'nordre',
		'DtFacture',
		'IDPatient',
		'ISTP',
		'fkidEtsAssurance',
		'TXPEC',
		'TotFacture',
		'TotalPEC',
		'TotalfactPatient',
		'TotReglPatient',
		'ReglementPEC',
		'ModeReglement',
		'Areglepar',
		'DtReglement',
		'fkidbordfacture',
		'ispayerAssureur',
		'user',
		'estfacturer',
		'FkidMedecinInitiateur',
		'PartLaboratoire',
		'MontantAffectation',
		'Type',
		'fkidCabinet'
	];

	// Relations
	public function patient()
	{
		return $this->belongsTo(Patient::class, 'IDPatient', 'ID');
	}

	public function details()
	{
		return $this->hasMany(Detailfacturepatient::class, 'fkidfacture', 'Idfacture');
	}

	public function medecin()
	{
		return $this->belongsTo(Medecin::class, 'FkidMedecinInitiateur', 'idMedecin');
	}

	public function rendezVous()
	{
		return $this->hasOne(Rendezvou::class, 'fkidFacture', 'Idfacture');
	}

	public function assureur()
	{
		return $this->belongsTo(Assureur::class, 'fkidEtsAssurance', 'IDAssureur');
	}

	public function reglements()
	{
		return $this->hasMany(Reglement::class, 'fkidFactBord', 'Idfacture');
	}

	/**
	 * Grouper les détails de facture par type d'acte
	 * Retourne un tableau avec les sections : Actes médicaux, Médicaments, Analyses, Radios
	 */
	public function getDetailsGroupesParType()
	{
		$details = $this->details()
			->with(['acte.typeActe', 'medicament'])
			->get();
		
		$groupes = [
			'Actes médicaux' => [],
			'Médicaments' => [],
			'Analyses' => [],
			'Radios' => [],
			'Autres' => []
		];

		foreach ($details as $detail) {
			$section = 'Autres';
			
			if ($detail->IsAct == 1 && $detail->acte) {
				// C'est un acte
				$typeActe = $detail->acte->typeActe->Type ?? null;
				if ($typeActe) {
					$typeActeLower = strtolower($typeActe);
					if (strpos($typeActeLower, 'médicament') !== false || strpos($typeActeLower, 'medicament') !== false) {
						$section = 'Actes médicaux';
					} elseif (strpos($typeActeLower, 'analyse') !== false) {
						$section = 'Analyses';
					} elseif (strpos($typeActeLower, 'radio') !== false) {
						$section = 'Radios';
					} else {
						$section = 'Actes médicaux'; // Par défaut pour les actes
					}
				} else {
					$section = 'Actes médicaux'; // Par défaut si pas de type
				}
			} elseif ($detail->IsAct == 2 && $detail->medicament) {
				// C'est un médicament
				$section = 'Médicaments';
			} elseif ($detail->IsAct == 3 && $detail->medicament) {
				// C'est une analyse
				$section = 'Analyses';
			} elseif ($detail->IsAct == 4 && $detail->medicament) {
				// C'est une radio
				$section = 'Radios';
			}

			$groupes[$section][] = $detail;
		}

		// Retirer les sections vides
		return array_filter($groupes, function($details) {
			return count($details) > 0;
		});
	}

	/**
	 * Vérifier si la facture contient des médicaments (IsAct = 2)
	 */
	public function contientMedicaments()
	{
		return $this->details()->where('IsAct', 2)->exists();
	}

	/**
	 * Vérifier si la facture contient uniquement des médicaments (pas d'actes)
	 */
	public function estFacturePharmacie()
	{
		$hasMedicaments = $this->details()->where('IsAct', 2)->exists();
		$hasActes = $this->details()->where('IsAct', 1)->exists();
		return $hasMedicaments && !$hasActes;
	}

	/**
	 * Vérifier si la facture contient des actes (IsAct = 1)
	 */
	public function contientActes()
	{
		return $this->details()->where('IsAct', 1)->exists();
	}

	/**
	 * Obtenir le type de facture basé sur IsAct
	 * Retourne: 'pharmacie', 'actes', 'mixte', 'autre'
	 */
	public function getTypeFactureAttribute()
	{
		$hasMedicaments = $this->contientMedicaments();
		$hasActes = $this->contientActes();
		
		if ($hasMedicaments && !$hasActes) {
			return 'pharmacie';
		} elseif ($hasActes && !$hasMedicaments) {
			return 'actes';
		} elseif ($hasMedicaments && $hasActes) {
			return 'mixte';
		}
		
		return 'autre';
	}

	/**
	 * Générer le prochain numéro de facture de manière cohérente
	 * Cette méthode garantit que tous les cabinets ont leur propre séquence de numérotation
	 * 
	 * @param int $cabinetId ID du cabinet
	 * @param int|null $annee Année (par défaut: année courante)
	 * @return array ['nordre' => int, 'Nfacture' => string, 'anneeFacture' => int]
	 */
	public static function genererNumeroFacture($cabinetId, $annee = null)
	{
		if ($annee === null) {
			$annee = Carbon::now()->year;
		}

		// Trouver la dernière facture pour ce cabinet et cette année
		// Utiliser nordre comme source de vérité pour éviter les problèmes de concurrence
		$derniereFacture = self::where('anneeFacture', $annee)
			->where('fkidCabinet', $cabinetId)
			->orderBy('nordre', 'desc')
			->lockForUpdate() // Verrouiller pour éviter les doublons en cas de création simultanée
			->first();

		// Calculer le prochain nordre
		$nordre = $derniereFacture ? ($derniereFacture->nordre + 1) : 1;

		// Générer le Nfacture au format: nordre-annee
		$nfacture = $nordre . '-' . $annee;

		return [
			'nordre' => $nordre,
			'Nfacture' => $nfacture,
			'anneeFacture' => $annee
		];
	}
}
