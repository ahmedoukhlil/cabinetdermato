<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $facture->Type ?: 'FACTURE' }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background: #fff; font-size: 12px; }
        .a4 { width: 210mm; min-height: 297mm; margin: auto; background: #fff; padding: 0 18mm 0 10mm; position: relative; box-sizing: border-box; display: flex; flex-direction: column; min-height: 297mm; }
        .a5 { width: 148mm; min-height: 210mm; margin: auto; background: #fff; padding: 0 10mm 0 5mm; position: relative; box-sizing: border-box; display: flex; flex-direction: column; min-height: 210mm; }
        .facture-title { text-align: center; font-size: 22px; font-weight: bold; margin-top: 10px; margin-bottom: 28px; letter-spacing: 2px; }
        .a5 .facture-title { font-size: 18px; margin-bottom: 20px; }
        .bloc-patient { margin: 0 0 10px 0; }
        .bloc-patient-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .bloc-patient-table td { padding: 2px 8px; font-size: 12px; }
        .a5 .bloc-patient-table td { font-size: 10px; padding: 1px 4px; }
        .bloc-patient-table .label { font-weight: bold; color: #222; width: 80px; }
        .bloc-patient-table .value { color: #222; }
        .bloc-patient-table .ref-cell { text-align: right; padding: 2px 4px; }
        .bloc-patient-table .ref-label { font-weight: bold; padding-right: 3px; display: inline; }
        .bloc-patient-table .ref-value { display: inline; }
        .details-table { width: 100%; border-collapse: collapse; margin-bottom: 0; }
        .details-table th, .details-table td { border: 1px solid #222; font-size: 12px; padding: 6px 8px; }
        .a5 .details-table th, .a5 .details-table td { font-size: 10px; padding: 4px 6px; }
        .details-table th { background: #f4f6fa; text-align: center; }
        .details-table td { text-align: center; }
        .details-table th:first-child, .details-table td:first-child { text-align: left; }
        .details-table th:last-child, .details-table td:last-child { width: 40%; text-align: right; }
        .totaux-table { width: 40%; border-collapse: collapse; margin-top: 0; margin-bottom: 0; margin-left: auto; }
        .totaux-table td { border: 1px solid #222; font-size: 12px; padding: 6px 8px; text-align: right; }
        .a5 .totaux-table td { font-size: 10px; padding: 4px 6px; }
        .montant-lettres { margin-top: 18px; font-size: 12px; clear: both; text-align: left; }
        .a5 .montant-lettres { font-size: 10px; margin-top: 12px; }
        .recu-header, .recu-footer { width: 100%; text-align: center; margin-bottom: 20px; }
        .recu-header img, .recu-footer img { max-width: 100%; height: auto; display: block; margin: 0 auto; }
        .recu-header .header { margin-bottom: 20px !important; }
        .recu-footer { width: 100%; position: absolute; bottom: 0; left: 0; right: 0; display: block; padding: 0 18mm 0 10mm; }
        .recu-footer .footer { margin-top: 0 !important; display: block !important; }
        @media print { 
            .a4, .a5 { box-shadow: none; counter-reset: page 1; } 
            .recu-footer { position: fixed; bottom: 0; left: 0; right: 0; width: 100%; display: block !important; padding: 0 18mm 0 10mm; }
            .recu-footer .footer { display: block !important; margin-top: 0 !important; } 
            .print-controls { display: none !important; }
            
            /* Numérotation des pages */
            @page {
                margin: 20mm 15mm;
                @bottom-right {
                    content: "Page " counter(page);
                    font-size: 10px;
                    color: #666;
                }
            }
            
            .page-number {
                display: block;
                position: fixed;
                bottom: 5mm;
                right: 15mm;
                font-size: 10px;
                color: #666;
                font-weight: normal;
            }
            
            .page-number::after {
                content: "Page " counter(page);
            }
            
            /* Gestion des sauts de pages */
            .bloc-patient { page-break-inside: avoid; page-break-after: avoid; }
            .facture-title { page-break-after: avoid; }
            .section-header { page-break-after: avoid; page-break-inside: avoid; }
            .section-wrapper { page-break-inside: auto; }
            .details-container { page-break-inside: auto; }
            .details-table { page-break-inside: auto; }
            .details-table thead { display: table-header-group; }
            .details-table tbody { page-break-inside: auto; }
            .details-table tr { page-break-inside: avoid; page-break-after: auto; }
            .details-table tfoot { display: table-footer-group; }
            
            /* Regrouper les éléments de fin de facture */
            .footer-content { page-break-inside: avoid; margin-top: 20px; }
            .totaux-table { page-break-inside: avoid; margin-top: 0; }
            .montant-lettres { page-break-inside: avoid; page-break-before: avoid; }
            .signature-block { page-break-inside: avoid; page-break-before: avoid; min-height: 80px; }
            
            /* Éviter les veuves et orphelines */
            p, td { orphans: 3; widows: 3; }
            
            /* S'assurer que le header et footer ne prennent pas trop d'espace */
            .recu-header { margin-bottom: 10px; display: block !important; }
            .recu-header .header { display: block !important; }
            .recu-header img { display: block !important; }
            .recu-footer { margin-top: 20px; display: block !important; }
        }
        .print-controls { display: flex; gap: 10px; justify-content: flex-end; margin: 18px 0; }
        .print-controls select, .print-controls button { padding: 8px 12px; border: 1px solid #ccc; border-radius: 4px; font-size: 14px; }
        .print-controls button { background: #2c5282; color: #fff; border: none; cursor: pointer; }
        .bloc-patient-table .praticien-value { padding-left: 2px !important; }
        .signature-block {
            margin-top: 40px;
            margin-bottom: 40px;
            text-align: right;
            padding-right: 20px;
        }
        .signature-title {
            font-weight: bold;
            margin-bottom: 25px;
        }
        .signature-name {
            font-style: italic;
        }
        .details-container { margin-bottom: 15px; }
        .section-wrapper { margin-bottom: 20px; }
        .footer-content { margin-top: 20px; }
        .page-number { display: none; }
    </style>
</head>
<body>
<div class="a4" id="documentContainer">
    <div class="print-controls">
        <select id="documentType" onchange="updateDocumentType()">
            <option value="Facture" {{ ($facture->Type === 'Facture' || !$facture->Type || $facture->Type === '') ? 'selected' : '' }}>Facture</option>
            <option value="Devis" {{ $facture->Type === 'Devis' ? 'selected' : '' }}>Devis</option>
        </select>
        <select id="pageFormat" onchange="updatePageFormat()">
            <option value="A4">Format A4</option>
            <option value="A5">Format A5</option>
        </select>
        <button onclick="window.print()" class="print-btn">
            Imprimer
        </button>
    </div>
    <div class="recu-header" style="width: 100%; margin-bottom: 20px; display: block;">
        @include('partials.recu-header')
    </div>
    <div class="facture-title" id="documentTitle">{{ $facture->Type && $facture->Type !== '' ? strtoupper($facture->Type) : 'FACTURE' }}</div>
    <div class="bloc-patient">
        <table class="bloc-patient-table">
            <tr>
                <td class="label">N° Fiche :</td>
                <td class="value">{{ $facture->patient->IdentifiantPatient ?? 'N/A' }}</td>
                <td class="ref-cell" colspan="2">
                    <span class="ref-label">Réf :</span>
                    <span class="ref-value">{{ $facture->Nfacture ?? 'N/A' }}</span>
                </td>
            </tr>
            <tr>
                <td class="label">Nom Patient :</td>
                <td class="value">{{ $facture->patient->NomContact ?? 'N/A' }}</td>
                <td class="ref-cell" colspan="2">
                    <span class="ref-label">Date :</span>
                    <span class="ref-value">{{ $facture->DtFacture ? $facture->DtFacture->format('d/m/Y H:i') : 'N/A' }}</span>
                </td>
            </tr>
            <tr>
                <td class="label">Téléphone :</td>
                <td class="value">{{ $facture->patient->Telephone1 ?? 'N/A' }}</td>
                <td colspan="2"></td>
            </tr>
            <tr>
                <td class="label">Praticien :</td>
                <td class="value">{{ $facture->medecin->Nom ?? '' }}</td>
                <td colspan="2"></td>
            </tr>
            @if($facture->patient && $facture->patient->assureur)
            <tr>
                <td class="label">Assureur :</td>
                <td class="value">
                    {{ $facture->patient->assureur->LibAssurance ?? 'N/A' }}
                    @if($facture->patient->IdentifiantAssurance)
                        ({{ $facture->patient->IdentifiantAssurance }})
                    @endif
                </td>
                <td colspan="2"></td>
            </tr>
            @endif
        </table>
    </div>
    @php
        $detailsGroupes = $facture->getDetailsGroupesParType();
    @endphp
    
    <div class="details-container">
    @if(count($detailsGroupes) > 1)
        {{-- Affichage par sections si plusieurs types --}}
        @foreach($detailsGroupes as $section => $details)
            <div class="section-wrapper">
                <div class="section-header" style="margin-top: 15px; margin-bottom: 10px; font-weight: bold; font-size: 14px; color: #333; border-bottom: 2px solid #007bff; padding-bottom: 5px;">
                    {{ $section }}
                </div>
                <table class="details-table" style="margin-bottom: 20px;">
                    <thead>
                    <tr>
                        <th>Traitement</th>
                        <th>Qté</th>
                        <th>P.U</th>
                        <th>Sous Total (MRU)</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($details as $detail)
                        <tr>
                            <td>{{ $detail->Actes }}</td>
                            <td>{{ $detail->Quantite }}</td>
                            <td>{{ number_format($detail->PrixFacture, 2) }}</td>
                            <td>{{ number_format($detail->PrixFacture * $detail->Quantite, 2) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach
    @else
        {{-- Affichage simple si un seul type ou pas de type --}}
        <table class="details-table">
            <thead>
            <tr>
                <th>Traitement</th>
                <th>Qté</th>
                <th>P.U</th>
                <th>Sous Total (MRU)</th>
            </tr>
            </thead>
            <tbody>
            @foreach($facture->details as $detail)
                <tr>
                    <td>{{ $detail->Actes }}</td>
                    <td>{{ $detail->Quantite }}</td>
                    <td>{{ number_format($detail->PrixFacture, 2) }}</td>
                    <td>{{ number_format($detail->PrixFacture * $detail->Quantite, 2) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
    </div>
    
    <div class="footer-content">
        <table class="totaux-table" id="totauxTable">
            <tr>
                <td>Total {{ strtolower($facture->Type ?: 'facture') }}</td>
                <td>{{ number_format($facture->TotFacture, 2) }} MRU</td>
            </tr>
            <tbody id="detailsFacture" style="display: {{ ($facture->Type === 'Facture' || !$facture->Type || $facture->Type === '') ? 'table-row-group' : 'none' }}">
                @if($facture->ISTP == 1)
                    <tr>
                        <td>Part assurance</td>
                        <td>{{ number_format($facture->TotalPEC, 2) }} MRU</td>
                    </tr>
                    <tr>
                        <td>Part patient</td>
                        <td>{{ number_format($facture->TotalfactPatient, 2) }} MRU</td>
                    </tr>
                @endif
                <tr>
                    <td>Total règlements</td>
                    <td>{{ number_format($facture->TotReglPatient, 2) }} MRU</td>
                </tr>
                <tr>
                    <td>Reste à payer</td>
                    <td>{{ number_format($facture->restePatient, 2) }} MRU</td>
                </tr>
            </tbody>
        </table>
        <div class="montant-lettres">
            Arrêté le présent {{ strtolower($facture->Type ?: 'facture') }} à la somme de : <strong>{{ $facture->en_lettres ?? '' }}</strong>
        </div>

        <div class="signature-block">
            <div class="signature-title">Signature</div>
            <div class="signature-name">{{ $facture->medecin->Nom ?? 'Non spécifié' }}</div>
        </div>
    </div>

    <div class="recu-footer" style="width: 100%; position: absolute; bottom: 0; left: 0; right: 0; display: block; padding: 0 18mm 0 10mm;">
        @include('partials.recu-footer')
    </div>
    
    <div class="page-number"></div>
</div>

<script>
// Cache des éléments DOM fréquemment utilisés
const elements = {
    documentType: document.getElementById('documentType'),
    documentTitle: document.getElementById('documentTitle'),
    montantLettres: document.querySelector('.montant-lettres'),
    totalLabel: document.querySelector('.totaux-table tr:first-child td:first-child'),
    detailsFacture: document.getElementById('detailsFacture'),
    pageFormat: document.getElementById('pageFormat'),
    container: document.getElementById('documentContainer')
};

function updateDocumentType() {
    const isDevis = elements.documentType.value === 'Devis';
    
    elements.documentTitle.textContent = isDevis ? 'DEVIS' : 'FACTURE';
    elements.montantLettres.innerHTML = elements.montantLettres.innerHTML.replace(
        isDevis ? 'facture' : 'devis',
        isDevis ? 'devis' : 'facture'
    );
    elements.totalLabel.textContent = `Total ${isDevis ? 'devis' : 'facture'}`;
    elements.detailsFacture.style.display = isDevis ? 'none' : 'table-row-group';
}

function updatePageFormat() {
    const isA5 = elements.pageFormat.value === 'A5';
    elements.container.classList.toggle('a4', !isA5);
    elements.container.classList.toggle('a5', isA5);
}

// Initialisation au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    // S'assurer que "Facture" est sélectionné par défaut si aucune valeur n'est définie
    if (!elements.documentType.value || elements.documentType.value === '') {
        elements.documentType.value = 'Facture';
    }
});
</script>
</body>
</html> 