<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>État de caisse journalier - {{ $date->format('d/m/Y') }}</title>
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
        .recu-header, .recu-footer { width: 100%; text-align: center; }
        .recu-header img, .recu-footer img { max-width: 100%; height: auto; }
        .recu-footer { position: absolute; bottom: 0; left: 0; width: 100%; }
        @media print { .a4, .a5 { box-shadow: none; } .recu-footer { position: fixed; bottom: 0; left: 0; width: 100%; } .print-controls { display: none !important; } }
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
        .medecin-section {
            margin-top: 30px;
            page-break-inside: avoid;
        }
        .medecin-header {
            background-color: #f4f6fa;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #222;
        }
        .medecin-header h3 {
            margin: 0;
            color: #222;
            font-size: 14px;
        }
        .totals {
            background-color: #f4f6fa;
            padding: 15px;
            border: 1px solid #222;
            margin-top: 20px;
        }
        .totals h4 {
            color: #222;
            margin-top: 0;
            margin-bottom: 10px;
            font-size: 14px;
        }
        .totals table {
            margin-bottom: 0;
            width: 100%;
        }
        .totals th {
            background-color: transparent;
            color: #222;
            text-align: left;
            width: 50%;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
<div class="a4" id="documentContainer">
    <div class="print-controls">
        <select id="pageFormat" onchange="updatePageFormat()">
            <option value="A4">Format A4</option>
            <option value="A5">Format A5</option>
        </select>
        <button onclick="window.print()" class="print-btn">
            Imprimer
        </button>
    </div>
    <div class="recu-header">@include('partials.recu-header')</div>
    <div class="facture-title">ÉTAT DE CAISSE JOURNALIER</div>
    
    <div class="bloc-patient">
        <table class="bloc-patient-table">
            <tr>
                <td class="label">Cabinet :</td>
                <td class="value">{{ $cabinet->Nom ?? 'Cabinet Savwa' }}</td>
                <td class="ref-cell" colspan="2">
                    <span class="ref-label">Date :</span>
                    <span class="ref-value">{{ $date->format('d/m/Y') }}</span>
                </td>
            </tr>
            <tr>
                <td class="label">Imprimé par :</td>
                <td class="value">{{ $user->NomComplet ?? 'N/A' }}</td>
                <td class="ref-cell" colspan="2">
                    <span class="ref-label">Heure :</span>
                    <span class="ref-value">{{ now()->format('H:i') }}</span>
                </td>
            </tr>
        </table>
    </div>

    @php
        $operationsParMedecin = $operations->groupBy('fkidmedecin');
    @endphp

    @foreach($operationsParMedecin as $medecinId => $operationsMedecin)
        @php
            $medecin = \App\Models\Medecin::find($medecinId);
            $totalRecettesMedecin = $operationsMedecin->sum('entreEspece');
            $totalDepensesMedecin = $operationsMedecin->sum('retraitEspece');
            $soldeMedecin = $totalRecettesMedecin - $totalDepensesMedecin;
        @endphp

        <div class="medecin-section">
            <div class="medecin-header">
                <h3>Dr. {{ $medecin->Nom ?? 'Médecin non spécifié' }}</h3>
            </div>

            <table class="details-table">
                <thead>
                    <tr>
                        <th>Heure</th>
                        <th>Opération</th>
                        <th>Mode de paiement</th>
                        <th>Recettes</th>
                        <th>Dépenses</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($operationsMedecin as $operation)
                    <tr>
                        <td>{{ Carbon\Carbon::parse($operation->dateoper)->format('H:i') }}</td>
                        <td>{{ $operation->designation }}</td>
                        <td>{{ $operation->TypePAie }}</td>
                        <td>{{ $operation->entreEspece > 0 ? number_format($operation->entreEspece, 0, ',', ' ') : '' }}</td>
                        <td>{{ $operation->retraitEspece > 0 ? number_format($operation->retraitEspece, 0, ',', ' ') : '' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <table class="totaux-table">
                <tr>
                    <td>Total des recettes</td>
                    <td>{{ number_format($totalRecettesMedecin, 0, ',', ' ') }} MRU</td>
                </tr>
                <tr>
                    <td>Total des dépenses</td>
                    <td>{{ number_format($totalDepensesMedecin, 0, ',', ' ') }} MRU</td>
                </tr>
                <tr>
                    <td>Solde</td>
                    <td>{{ number_format($soldeMedecin, 0, ',', ' ') }} MRU</td>
                </tr>
            </table>
        </div>
    @endforeach

    <table class="totaux-table">
        <tr>
            <td>Total des recettes</td>
            <td>{{ number_format($totalRecettes, 0, ',', ' ') }} MRU</td>
        </tr>
        <tr>
            <td>Total des dépenses</td>
            <td>{{ number_format($totalDepenses, 0, ',', ' ') }} MRU</td>
        </tr>
        <tr>
            <td>Solde global</td>
            <td>{{ number_format($solde, 0, ',', ' ') }} MRU</td>
        </tr>
    </table>

    <div class="signature-block">
        <div class="signature-title">Signature</div>
        <div class="signature-name">{{ $user->NomComplet ?? 'Non spécifié' }}</div>
    </div>

    <div class="recu-footer">@include('partials.recu-footer')</div>
</div>

<script>
// Cache des éléments DOM fréquemment utilisés
const elements = {
    pageFormat: document.getElementById('pageFormat'),
    container: document.getElementById('documentContainer')
};

function updatePageFormat() {
    const isA5 = elements.pageFormat.value === 'A5';
    elements.container.classList.toggle('a4', !isA5);
    elements.container.classList.toggle('a5', isA5);
}
</script>
</body>
</html> 