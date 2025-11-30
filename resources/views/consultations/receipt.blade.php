<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>REÇU DE CONSULTATION</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background: #fff; font-size: 12px; overflow-x: hidden; }
        .a4 { width: 210mm; min-height: 297mm; margin: auto; background: #fff; padding: 0 18mm 0 10mm; position: relative; box-sizing: border-box; display: flex; flex-direction: column; min-height: 297mm; overflow-x: hidden; max-width: 100%; }
        .a5 { width: 148mm; min-height: 210mm; margin: auto; background: #fff; padding: 3mm 8mm 5mm 5mm; position: relative; box-sizing: border-box; display: flex; flex-direction: column; min-height: 210mm; overflow-x: hidden; max-width: 100%; }
        /* Conteneur pour le contenu avant le tableau */
        .content-before-table { 
            overflow: visible;
            page-break-inside: avoid; /* Évite de couper le contenu en deux */
        }
        .a5 .content-before-table { 
            /* Pas de limitation de hauteur pour A5 non plus */
        }
        .facture-title { text-align: center; font-size: 22px; font-weight: bold; margin-top: 10px; margin-bottom: 28px; letter-spacing: 2px; }
        .a5 .facture-title { font-size: 16px; margin-top: 5px; margin-bottom: 12px; letter-spacing: 1px; }
        .bloc-patient { margin: 0 0 10px 0; }
        .bloc-patient-table { width: 100%; max-width: 100%; border-collapse: collapse; margin-bottom: 10px; table-layout: fixed; word-wrap: break-word; }
        .bloc-patient-table td { padding: 2px 8px; font-size: 12px; }
        .a5 .bloc-patient-table td { font-size: 10px; padding: 1px 4px; }
        .bloc-patient-table .label { font-weight: bold; color: #222; width: 80px; }
        .bloc-patient-table .value { color: #222; }
        .bloc-patient-table .ref-cell { text-align: right; padding: 2px 4px; }
        .bloc-patient-table .ref-label { font-weight: bold; padding-right: 3px; display: inline; }
        .bloc-patient-table .ref-value { display: inline; }
        .details-table { width: 100%; max-width: 100%; border-collapse: collapse; margin-bottom: 0; page-break-inside: auto; table-layout: fixed; word-wrap: break-word; }
        .details-table th, .details-table td { border: 1px solid #222; font-size: 12px; padding: 6px 8px; }
        .a5 .details-table th, .a5 .details-table td { font-size: 10px; padding: 4px 6px; }
        .details-table th { background: #f4f6fa; text-align: center; }
        .details-table td { text-align: center; }
        .details-table th:first-child, .details-table td:first-child { text-align: left; }
        .details-table th:last-child, .details-table td:last-child { width: 40%; text-align: left; }
        /* Gestion des sauts de page pour les lignes de tableau */
        .details-table tbody tr { 
            page-break-inside: avoid; 
            page-break-after: auto;
        }
        .details-table thead { 
            display: table-header-group; 
        }
        .details-table tfoot { 
            display: table-footer-group; 
        }
        /* Répéter les en-têtes de tableau sur chaque page */
        .details-table thead tr {
            page-break-inside: avoid;
            page-break-after: avoid;
        }
        .totaux-table { width: 40%; border-collapse: collapse; margin-top: 0; margin-bottom: 0; margin-left: auto; }
        .totaux-table td { border: 1px solid #222; font-size: 12px; padding: 6px 8px; text-align: right; }
        .a5 .totaux-table td { font-size: 10px; padding: 4px 6px; }
        .montant-lettres { margin-top: 18px; font-size: 12px; clear: both; text-align: left; }
        .a5 .montant-lettres { font-size: 10px; margin-top: 12px; }
        .recu-header, .recu-footer { width: 100%; text-align: center; }
        .recu-header img, .recu-footer img { max-width: 100%; height: auto; }
        .recu-footer { position: absolute; bottom: 0; left: 0; right: 0; width: 100%; display: block; padding: 0 18mm 0 10mm; }
        .recu-footer .footer { margin-top: 0 !important; display: block !important; }
        /* Styles spécifiques pour l'en-tête en format A5 */
        .a5 .recu-header { margin-bottom: 5px !important; }
        .a5 .recu-header .header { margin-bottom: 5px !important; }
        .a5 .recu-header .header > div { margin-bottom: 5px !important; }
        .a5 .recu-header img { max-height: 50px !important; max-width: 100% !important; object-fit: contain !important; }
        .a5 .recu-header h1 { font-size: 12px !important; margin: 2px 0 !important; line-height: 1.2 !important; }
        .a5 .recu-header p { font-size: 9px !important; margin: 1px 0 !important; line-height: 1.2 !important; }
        .a5 .recu-header .text-center { margin-bottom: 3px !important; }
        @media print { 
            .a4, .a5 { box-shadow: none; } 
            .recu-footer { position: fixed; bottom: 0; left: 0; right: 0; width: 100%; display: block !important; padding: 0 18mm 0 10mm; }
            .recu-footer .footer { display: block !important; margin-top: 0 !important; } 
            .print-controls { display: none !important; }
            .qr-code-container:hover { transform: none; }
            .qr-code-link { color: #000 !important; }
            .qr-code-container { background: #fff !important; }
            a { color: #000 !important; text-decoration: none !important; }
            
            /* Gestion des sauts de page pour les tableaux */
            .details-table { 
                page-break-inside: auto; 
                border-collapse: collapse;
                page-break-before: auto; /* Permet le saut avant le tableau si nécessaire */
            }
            .details-table tbody tr { 
                page-break-inside: avoid; 
                page-break-after: auto;
            }
            .details-table thead { 
                display: table-header-group; 
                page-break-after: avoid;
            }
            .details-table thead tr {
                page-break-inside: avoid;
                page-break-after: avoid;
            }
            .details-table tfoot { 
                display: table-footer-group; 
                page-break-before: avoid;
            }
            
            /* Répéter les en-têtes sur chaque page */
            .details-table thead {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            /* Éviter les sauts de page inutiles */
            .details-table:first-of-type {
                page-break-before: auto; /* Ne force plus le saut, laisse le navigateur décider */
            }
            
            /* Ajuster la hauteur du contenu avant le tableau pour éviter le débordement */
            .content-before-table {
                max-height: none !important; /* Supprime la limitation de hauteur */
                page-break-after: auto;
                page-break-inside: avoid; /* Évite de couper le contenu */
            }
            .a5 .content-before-table {
                max-height: none !important; /* Supprime la limitation de hauteur */
            }
            
            /* Éviter de couper les éléments importants */
            .bloc-patient { page-break-inside: avoid; }
            .facture-title { page-break-after: avoid; }
            .ordre-rdv { 
                page-break-inside: avoid; 
                page-break-after: avoid; 
                margin: 8px auto !important;
                background: #000000 !important; /* Fond noir pour meilleur contraste à l'impression */
                color: #ffffff !important;
                border: 2px solid #000000 !important;
                font-size: 16px !important;
                font-weight: 700 !important;
                padding: 8px 16px !important;
                letter-spacing: 1px !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                box-shadow: none !important;
            }
            .ordre-rdv > div:first-child {
                font-size: 0.5em !important;
                font-weight: 600 !important;
                letter-spacing: 0.5px !important;
                margin-bottom: 3px !important;
                opacity: 1 !important;
            }
            .ordre-rdv > div:last-child {
                font-size: 1.15em !important;
                font-weight: 700 !important;
                letter-spacing: 1.5px !important;
            }
            .a5 .ordre-rdv {
                font-size: 14px !important;
                padding: 6px 14px !important;
                letter-spacing: 1px !important;
            }
            .a5 .ordre-rdv > div:first-child {
                font-size: 0.5em !important;
            }
            .a5 .ordre-rdv > div:last-child {
                font-size: 1.1em !important;
                letter-spacing: 1.5px !important;
            }
            .totaux-table { page-break-inside: avoid; }
            .montant-lettres { page-break-inside: avoid; }
            .signature-block { page-break-inside: avoid; }
            
            /* Permettre les sauts de page pour les sections */
            .section-header { 
                page-break-after: avoid;
                page-break-inside: avoid;
            }
            
            /* Gestion des sauts de page pour les conteneurs */
            .a4, .a5 {
                page-break-after: auto;
                orphans: 3;
                widows: 3;
                min-height: auto !important; /* Permet au contenu de s'adapter à la page */
                height: auto !important; /* Évite les hauteurs fixes qui causent des débordements */
            }
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
        .qr-code-link {
            text-decoration: none;
            color: inherit;
            display: inline-block;
        }
        .qr-code-link:hover {
            transform: scale(1.02);
        }
        .qr-code-container {
            display: inline-block;
            padding: 4px;
            background: #ffffff;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .qr-code-container:hover {
            transform: scale(1.02);
        }
        .qr-code-accessibility {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }
        .ordre-rdv {
            display: block;
            background: #1a365d;
            color: white;
            font-weight: bold;
            font-size: 14px;
            padding: 6px 15px;
            border-radius: 6px;
            border: 2px solid #2c5282;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
            margin: 10px auto;
            text-align: center;
            width: fit-content;
            min-width: 100px;
            max-width: 100%;
            box-sizing: border-box;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .a5 .ordre-rdv {
            font-size: 12px;
            padding: 5px 12px;
            min-width: 90px;
            letter-spacing: 0.5px;
        }
        
        .print-controls {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin: 18px 0;
            flex-wrap: wrap;
            align-items: center;
        }
        
        .print-controls select,
        .print-controls button,
        .whatsapp-btn {
            padding: 8px 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .print-btn {
            background: #007bff;
            color: white;
            border: 1px solid #007bff;
        }
        
        .print-btn:hover {
            background: #0056b3;
            border-color: #0056b3;
        }
        
        .whatsapp-btn {
            background: #25D366;
            color: white;
            border: 1px solid #25D366;
            display: flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
        }
        
        .whatsapp-btn:hover {
            background: #128C7E;
            border-color: #128C7E;
        }
        
        .whatsapp-logo {
            width: 16px;
            height: 16px;
            fill: currentColor;
        }
        
        /* Masquer les boutons WhatsApp à l'impression */
        @media print {
            .whatsapp-btn {
                display: none !important;
            }
        }
        
        /* Responsive Design Complet */
        @media (max-width: 1200px) {
            .a4 { width: 95%; margin: 10px auto; overflow-x: hidden; max-width: 100%; }
            .a5 { width: 90%; margin: 10px auto; overflow-x: hidden; max-width: 100%; }
        }
        
        @media (max-width: 768px) {
            .a4, .a5 { 
                width: 100%; 
                margin: 5px; 
                padding: 10px; 
                min-height: auto;
                overflow-x: hidden;
                max-width: 100%;
            }
            
            .facture-title { 
                font-size: 18px; 
                margin-bottom: 20px; 
                letter-spacing: 1px;
            }
            
            .bloc-patient-table td { 
                font-size: 11px; 
                padding: 1px 4px; 
            }
            
            .details-table th, 
            .details-table td { 
                font-size: 10px; 
                padding: 4px 6px; 
            }
            
            .totaux-table td { 
                font-size: 10px; 
                padding: 4px 6px; 
            }
            
            .montant-lettres { 
                font-size: 10px; 
                margin-top: 12px; 
            }
            
            .ordre-rdv {
                font-size: 13px;
                padding: 6px 15px;
                min-width: 95px;
                margin: 10px auto;
            }
            .ordre-rdv > div:first-child {
                font-size: 0.5em !important;
            }
            .ordre-rdv > div:last-child {
                font-size: 1.1em !important;
            }
            
            .print-controls {
                flex-direction: column;
                align-items: stretch;
                margin: 10px 0;
                gap: 8px;
            }
            
            .print-controls select,
            .print-controls button,
            .whatsapp-btn {
                width: 100%;
                margin-bottom: 5px;
                padding: 10px 12px;
                font-size: 16px;
            }
            
            .signature-block {
                margin-top: 30px;
                margin-bottom: 30px;
                padding-right: 10px;
            }
            
            .signature-title {
                margin-bottom: 20px;
                font-size: 11px;
            }
            
            .signature-name {
                font-size: 11px;
            }
        }
        
        @media (max-width: 480px) {
            .a4, .a5 { 
                padding: 5px; 
                margin: 2px;
                overflow-x: hidden;
                max-width: 100%;
            }
            
            .facture-title { 
                font-size: 16px; 
                margin-bottom: 15px; 
                letter-spacing: 0.5px;
            }
            
            .bloc-patient-table td { 
                font-size: 10px; 
                padding: 1px 2px; 
            }
            
            .bloc-patient-table .label { 
                width: 70px; 
            }
            
            .details-table th, 
            .details-table td { 
                font-size: 9px; 
                padding: 3px 4px; 
            }
            
            .totaux-table { 
                width: 100%; 
            }
            
            .totaux-table td { 
                font-size: 9px; 
                padding: 3px 4px; 
            }
            
            .montant-lettres { 
                font-size: 9px; 
                margin-top: 10px; 
            }
            
            .ordre-rdv {
                font-size: 12px;
                padding: 6px 14px;
                min-width: 90px;
                margin: 8px auto;
            }
            .ordre-rdv > div:first-child {
                font-size: 0.48em !important;
            }
            .ordre-rdv > div:last-child {
                font-size: 1.1em !important;
            }
            
            .print-controls {
                margin: 8px 0;
                gap: 6px;
            }
            
            .print-controls select,
            .print-controls button,
            .whatsapp-btn {
                padding: 12px 8px;
                font-size: 14px;
            }
            
            .signature-block {
                margin-top: 25px;
                margin-bottom: 25px;
                padding-right: 5px;
            }
            
            .signature-title {
                margin-bottom: 15px;
                font-size: 10px;
            }
            
            .signature-name {
                font-size: 10px;
            }
            
            /* QR Code responsive */
            .qr-code-container {
                transform: scale(0.8);
                transform-origin: bottom left;
            }
            
            .qr-code-container div {
                max-width: 80px !important;
            }
        }
        
        @media (max-width: 360px) {
            .facture-title { 
                font-size: 14px; 
                margin-bottom: 12px; 
            }
            
            .bloc-patient-table td { 
                font-size: 9px; 
                padding: 1px; 
            }
            
            .details-table th, 
            .details-table td { 
                font-size: 8px; 
                padding: 2px 3px; 
            }
            
            .totaux-table td { 
                font-size: 8px; 
                padding: 2px 3px; 
            }
            
            .montant-lettres { 
                font-size: 8px; 
                margin-top: 8px; 
            }
            
            .ordre-rdv {
                font-size: 11px;
                padding: 5px 12px;
                min-width: 85px;
                margin: 6px auto;
            }
            .ordre-rdv > div:first-child {
                font-size: 0.45em !important;
            }
            .ordre-rdv > div:last-child {
                font-size: 1.05em !important;
            }
            
            .print-controls select,
            .print-controls button,
            .whatsapp-btn {
                padding: 10px 6px;
                font-size: 12px;
            }
            
            .signature-block {
                margin-top: 20px;
                margin-bottom: 20px;
            }
            
            .signature-title {
                margin-bottom: 12px;
                font-size: 9px;
            }
            
            .signature-name {
                font-size: 9px;
            }
            
            /* QR Code très petit */
            .qr-code-container {
                transform: scale(0.7);
            }
            
            .qr-code-container div {
                max-width: 70px !important;
            }
        }
        
        /* Orientation paysage sur mobile */
        @media (max-width: 768px) and (orientation: landscape) {
            .a4, .a5 { 
                width: 95%; 
                margin: 5px auto; 
            }
            
            .facture-title { 
                font-size: 14px; 
                margin-bottom: 10px; 
            }
            
            .print-controls {
                flex-direction: row;
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .print-controls select,
            .print-controls button,
            .whatsapp-btn {
                width: auto;
                min-width: 120px;
                margin: 2px;
            }
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
        <button onclick="printDocument()" class="print-btn">
            Imprimer
        </button>
        
        <!-- BOUTON WHATSAPP -->
        @if($facture->patient->Telephone1 && $facture->patient->Telephone1 !== 'N/A')
            @php
                $telephoneNettoye = \App\Helpers\QrCodeHelper::formatPhoneForWhatsApp($facture->patient->Telephone1);
            @endphp
            @if($telephoneNettoye)
                <button onclick="envoyerConfirmationWhatsApp()" class="whatsapp-btn">
                    <svg class="whatsapp-logo" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
                    </svg>
                    Confirmation WhatsApp
                </button>
            @endif
        @endif
    </div>
    <div class="recu-header">@include('partials.recu-header')</div>
    <div class="content-before-table">
        <div class="facture-title">REÇU DE CONSULTATION</div>
        
        @if($facture->rendezVous && isset($facture->rendezVous->OrdreRDV) && $facture->rendezVous->OrdreRDV > 0)
            <div class="ordre-rdv">
                <div style="font-size: 0.5em; letter-spacing: 0.5px; margin-bottom: 2px; opacity: 0.9;">NUMÉRO DE RENDEZ-VOUS</div>
                <div style="font-size: 1.1em; line-height: 1.2;">N° {{ str_pad($facture->rendezVous->OrdreRDV, 3, '0', STR_PAD_LEFT) }}</div>
            </div>
        @endif
        
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
                    <span class="ref-value">{{ $facture->DtFacture ? \Carbon\Carbon::parse($facture->DtFacture)->format('d/m/Y') : 'N/A' }}</span>
                </td>
            </tr>
            <tr>
                <td class="label">Téléphone :</td>
                <td class="value">{{ $facture->patient->Telephone1 ?? 'N/A' }}</td>
                <td colspan="2"></td>
            </tr>
            <tr>
                <td class="label">Praticien :</td>
                <td class="value">{{ $facture->medecin->Nom ?? '' }} {{ $facture->medecin->Prenom ?? '' }}</td>
                <td colspan="2"></td>
            </tr>
        </table>
    </div>
    </div> <!-- Fin du conteneur content-before-table -->
    @php
        $detailsGroupes = $facture->getDetailsGroupesParType();
    @endphp
    
    @if(count($detailsGroupes) > 1)
        {{-- Affichage par sections si plusieurs types --}}
        @foreach($detailsGroupes as $section => $details)
            <div class="section-header" style="margin-top: 15px; margin-bottom: 10px; font-weight: bold; font-size: 14px; color: #333; border-bottom: 2px solid #007bff; padding-bottom: 5px;">
                {{ $section }}
            </div>
            <table class="details-table" style="margin-bottom: 20px;">
                <thead>
                <tr>
                    <th>Acte</th>
                    <th>Quantité</th>
                    <th>Prix unitaire</th>
                    <th>Total</th>
                </tr>
                </thead>
                <tbody>
                @foreach($details as $detail)
                    <tr>
                        <td>{{ $detail->Actes ?? 'N/A' }}</td>
                        <td>{{ $detail->Quantite ?? 1 }}</td>
                        <td>{{ number_format($detail->PrixFacture ?? 0, 2) }} MRU</td>
                        <td>{{ number_format(($detail->PrixFacture ?? 0) * ($detail->Quantite ?? 1), 2) }} MRU</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endforeach
    @else
        {{-- Affichage simple si un seul type ou pas de type --}}
        <table class="details-table">
            <thead>
            <tr>
                <th>Acte</th>
                <th>Quantité</th>
                <th>Prix unitaire</th>
                <th>Total</th>
            </tr>
            </thead>
            <tbody>
            @foreach($facture->details as $detail)
                <tr>
                    <td>{{ $detail->Actes ?? 'N/A' }}</td>
                    <td>{{ $detail->Quantite ?? 1 }}</td>
                    <td>{{ number_format($detail->PrixFacture ?? 0, 2) }} MRU</td>
                    <td>{{ number_format(($detail->PrixFacture ?? 0) * ($detail->Quantite ?? 1), 2) }} MRU</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
    <table class="totaux-table">
        <tr>
            <td>Total consultation</td>
            <td>{{ number_format($facture->TotFacture ?? 0, 2) }} MRU</td>
        </tr>
        @if(($facture->TotalPEC ?? 0) > 0)
            <tr>
                <td>Prise en charge</td>
                <td>{{ number_format($facture->TotalPEC ?? 0, 2) }} MRU</td>
            </tr>
            <tr>
                <td>Reste à payer</td>
                <td>{{ number_format($facture->TotalfactPatient ?? 0, 2) }} MRU</td>
            </tr>
        @endif
    </table>
    <div class="montant-lettres">
        Arrêté la présente consultation à la somme de : <strong>{{ $facture->en_lettres ?? '' }}</strong>
    </div>

    <div class="signature-block">
        <div class="signature-title">Signature</div>
        <div class="signature-name">Dr. {{ $facture->medecin->Nom ?? 'Non spécifié' }}</div>
    </div>

         <!-- QR Code pour l'interface patient - Positionné en bas à gauche -->
     <div style="position: fixed; bottom: 120px; left: 20px; z-index: 1000;">
         <div style="text-align: center;">
             @php
                 try {
                     // Utiliser la date du rendez-vous associé ou la date de la facture
                     $dateRendezVous = $facture->rendezVous ? $facture->rendezVous->dtPrevuRDV : $facture->DtFacture;
                     $medecinId = $facture->rendezVous ? $facture->rendezVous->fkidMedecin : null;
                     $token = App\Http\Controllers\PatientInterfaceController::generateToken($facture->IDPatient, $dateRendezVous, $medecinId);
                     $patientUrl = route('patient.rendez-vous', ['token' => $token]);
                 } catch (Exception $e) {
                     $patientUrl = '#';
                 }
             @endphp
             <a href="{{ $patientUrl }}" target="_blank" class="qr-code-link" aria-label="Ouvrir l'interface patient pour suivre votre file d'attente">
                 <div class="qr-code-container">
                     <div style="max-width: 100px; height: auto;">
                         @php
                             try {
                                 $qrCode = App\Helpers\QrCodeHelper::generateRendezVousQrCode($facture->IDPatient);
                                 echo $qrCode;
                             } catch (Exception $e) {
                                 echo '<div style="width: 100px; height: 100px; background: #fff; display: flex; align-items: center; justify-content: center; font-size: 8px; color: #666; border-radius: 4px;">QR Code<br>Non disponible</div>';
                             }
                         @endphp
                     </div>
                 </div>
             </a>
             <div style="margin-top: 4px; font-size: 8px; color: #333; font-weight: 600;">
                 Suivez votre file d'attente
             </div>
         </div>
     </div>

    <div class="recu-footer" style="width: 100%; position: absolute; bottom: 0; left: 0; right: 0; display: block; padding: 0 18mm 0 10mm;">
        @include('partials.recu-footer')
    </div>
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
    
    // Mettre à jour la règle @page pour l'impression
    let style = document.getElementById('print-format-style');
    if (!style) {
        style = document.createElement('style');
        style.id = 'print-format-style';
        document.head.appendChild(style);
    }
    
    if (isA5) {
        style.innerHTML = '@media print { @page { size: A5; margin: 8mm; } }';
    } else {
        style.innerHTML = '@media print { @page { size: A4; margin: 10mm; } }';
    }
}

// Fonction pour imprimer avec le bon format
function printDocument() {
    // S'assurer que le format est à jour avant l'impression
    updatePageFormat();
    // Petit délai pour s'assurer que le style est appliqué
    setTimeout(function() {
        window.print();
    }, 100);
}

// Initialiser le format au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    updatePageFormat();
});

// Fonction globale WhatsApp pour cette page
if (typeof window.whatsappWindow === 'undefined') {
    window.whatsappWindow = null;
}

// Fonction globale pour ouvrir WhatsApp de manière centralisée
window.openWhatsApp = function(url, successCallback) {
    console.log('🔗 URL WhatsApp:', url);
    
    try {
        // Vérifier si un onglet WhatsApp est déjà ouvert
        if (window.whatsappWindow && !window.whatsappWindow.closed) {
            console.log('🔄 Onglet WhatsApp déjà ouvert, focus sur l\'onglet existant');
            window.whatsappWindow.focus();
            
            // Mettre à jour l'URL de l'onglet existant
            window.whatsappWindow.location.href = url;
            
            // Appeler le callback de succès
            if (successCallback) successCallback();
            return;
        }
        
        // Ouvrir WhatsApp dans un nouvel onglet
        console.log('🟠 Tentative d\'ouverture WhatsApp dans un nouvel onglet...');
        window.whatsappWindow = window.open(url, '_blank', 'noopener,noreferrer');
        
        if (window.whatsappWindow) {
            console.log('✅ WhatsApp ouvert avec succès dans un nouvel onglet');
            window.whatsappWindow.focus();
            
            // Appeler le callback de succès
            if (successCallback) successCallback();
        } else {
            console.error('❌ Impossible d\'ouvrir WhatsApp - popup bloqué');
            
            // Fallback : essayer d'ouvrir dans un nouvel onglet avec des paramètres différents
            console.log('🔄 Tentative de fallback avec paramètres différents...');
            window.whatsappWindow = window.open(url, '_blank');
            
            if (window.whatsappWindow) {
                console.log('✅ WhatsApp ouvert avec fallback');
                window.whatsappWindow.focus();
                if (successCallback) successCallback();
            } else {
                console.error('❌ Fallback échoué - copier le lien');
                
                // Dernier recours : copier le lien dans le presse-papiers
                if (navigator.clipboard) {
                    navigator.clipboard.writeText(url).then(function() {
                        alert('Lien WhatsApp copié dans le presse-papiers. Veuillez l\'ouvrir manuellement.');
                        if (successCallback) successCallback();
                    });
                } else {
                    alert('Impossible d\'ouvrir WhatsApp automatiquement. Veuillez copier ce lien: ' + url);
                    if (successCallback) successCallback();
                }
            }
        }
    } catch (error) {
        console.error('❌ Erreur lors de l\'ouverture de WhatsApp:', error);
        
        // Fallback : copier le lien dans le presse-papiers
        if (navigator.clipboard) {
            navigator.clipboard.writeText(url).then(function() {
                alert('Lien WhatsApp copié dans le presse-papiers. Veuillez l\'ouvrir manuellement.');
                if (successCallback) successCallback();
            });
        } else {
            alert('Impossible d\'ouvrir WhatsApp automatiquement. Veuillez copier ce lien: ' + url);
            if (successCallback) successCallback();
        }
    }
};

// Fonction pour créer un short URL avec TinyURL API
async function createShortUrl(longUrl) {
    try {
        const response = await fetch(`https://tinyurl.com/api-create.php?url=${encodeURIComponent(longUrl)}`);
        if (response.ok) {
            return await response.text();
        } else {
            console.error('❌ Erreur lors de la création du short URL');
            return longUrl; // Fallback vers l'URL longue
        }
    } catch (error) {
        console.error('❌ Erreur réseau lors de la création du short URL:', error);
        return longUrl; // Fallback vers l'URL longue
    }
}

// FONCTIONS WHATSAPP
async function envoyerConfirmationWhatsApp() {
    if (!verifierTelephone()) {
        return;
    }
    
    // Données du patient et de la consultation
    const nomPatient = "{{ $facture->patient->NomContact }}";
    const telephone = "{{ $telephoneNettoye ?? '' }}";
    const dateConsultation = "{{ $facture->DtFacture ? \Carbon\Carbon::parse($facture->DtFacture)->format('d/m/Y') : '' }}";
    const medecin = "{{ trim(($facture->medecin->Nom ?? '') . ' ' . ($facture->medecin->Prenom ?? '')) ?: 'Médecin non défini' }}";
    const numeroFacture = "{{ $facture->Nfacture ?? '' }}";
    
    // Récupérer l'heure et le numéro d'ordre depuis le rendez-vous associé
    let heureConsultation = '';
    let ordreConsultation = '';
    
    @if($facture->rendezVous)
        // Debug des valeurs brutes
        console.log('HeureRdv brute:', "{{ $facture->rendezVous->HeureRdv ?? 'null' }}");
        console.log('Type HeureRdv:', typeof("{{ $facture->rendezVous->HeureRdv ?? 'null' }}"));
        
        @php
            $heureRdv = $facture->rendezVous->HeureRdv;
            $heureFormatee = '';
            
            if ($heureRdv) {
                try {
                    // Si c'est déjà un objet Carbon
                    if ($heureRdv instanceof \Carbon\Carbon) {
                        $heureFormatee = $heureRdv->format('H:i');
                    } else {
                        // Essayer de parser la valeur
                        $carbon = \Carbon\Carbon::parse($heureRdv);
                        $heureFormatee = $carbon->format('H:i');
                    }
                } catch (Exception $e) {
                    // Si le parsing échoue, utiliser la valeur brute
                    $heureFormatee = $heureRdv;
                }
            }
            
            // Si toujours pas d'heure, utiliser l'heure de la facture comme fallback
            if (!$heureFormatee && $facture->DtFacture) {
                try {
                    $carbon = \Carbon\Carbon::parse($facture->DtFacture);
                    $heureFormatee = $carbon->format('H:i');
                } catch (Exception $e) {
                    $heureFormatee = '';
                }
            }
        @endphp
        
        heureConsultation = "{{ $heureFormatee ?: 'À définir' }}";
        
        @if(isset($facture->rendezVous->OrdreRDV) && $facture->rendezVous->OrdreRDV > 0)
            ordreConsultation = "{{ str_pad($facture->rendezVous->OrdreRDV, 3, '0', STR_PAD_LEFT) }}";
        @else
            ordreConsultation = "À définir";
        @endif
    @else
        heureConsultation = "Non disponible (pas de RDV associé)";
        ordreConsultation = "Non disponible (pas de RDV associé)";
    @endif
    
    @php
        // Générer le lien de suivi de la file d'attente avec la date du rendez-vous
        try {
            // Utiliser la date du rendez-vous associé ou la date de la facture
            $dateRendezVous = $facture->rendezVous ? $facture->rendezVous->dtPrevuRDV : $facture->DtFacture;
            $medecinId = $facture->rendezVous ? $facture->rendezVous->fkidMedecin : null;
            $token = App\Http\Controllers\PatientInterfaceController::generateToken($facture->IDPatient, $dateRendezVous, $medecinId);
            $patientUrl = route('patient.rendez-vous', ['token' => $token]);
        } catch (Exception $e) {
            $patientUrl = url('/');
        }
    @endphp
    
    const lienSuivi = "{{ $patientUrl }}";
    
    // Créer un short URL pour le lien de suivi
    const shortUrl = await createShortUrl(lienSuivi);
    console.log('🔗 URL longue:', lienSuivi);
    console.log('🔗 URL courte:', shortUrl);
    
    // Debug des données
    console.log('=== DEBUG CONSULTATION ===');
    console.log('Heure:', heureConsultation);
    console.log('Ordre:', ordreConsultation);
    console.log('Médecin:', medecin);
    console.log('==========================');
    
    // Nettoyer le numéro de téléphone
    const phoneClean = telephone.replace(/[\s\-\(\)]/g, '');
    
    // Message bilingue avec format de rendez-vous
    const message = construireMessageBilingue(nomPatient, dateConsultation, heureConsultation, medecin, ordreConsultation, numeroFacture, shortUrl);
    
    // Créer le lien WhatsApp Web
    const whatsappWebUrl = `https://wa.me/${phoneClean}?text=${encodeURIComponent(message)}`;
    
    console.log('🔗 URL WhatsApp Confirmation Consultation:', whatsappWebUrl);
    
    // Utiliser la fonction globale pour ouvrir WhatsApp
    window.openWhatsApp(whatsappWebUrl, function() {
        mostrarNotificationSucces();
    });
}

function construireMessageBilingue(nom, date, heure, medecin, ordre, numeroFacture, lienSuivi) {
    // Debug des paramètres reçus
    console.log('=== CONSTRUCTION MESSAGE CONSULTATION ===');
    console.log('Nom:', nom);
    console.log('Date:', date);
    console.log('Heure reçue:', heure);
    console.log('Médecin:', medecin);
    console.log('Ordre reçu:', ordre);
    console.log('Facture:', numeroFacture);
    console.log('Lien:', lienSuivi);
    console.log('==========================================');
    
    // Construction du message avec formatage simple compatible WhatsApp
    const message = `*تم تأكيد الاستشارة*
*Consultation confirmée*

*${nom || 'Nom non défini'}*

التاريخ: ${date || 'تاريخ غير محدد'}
الوقت: ${heure || 'وقت غير محدد'}
الطبيب: د. ${medecin || 'طبيب غير محدد'}
الرقم: ${ordre || 'رقم غير محدد'}
الفاتورة: ${numeroFacture || 'فاتورة غير محددة'}

━━━━━━━━━━━━━━━━━━━━━━

Date: ${date || 'Date non définie'}
Heure: ${heure || 'Heure non définie'}
Médecin: Dr. ${medecin || 'Médecin non défini'}
Numéro: ${ordre || 'Numéro non défini'}
Facture: ${numeroFacture || 'Facture non définie'}

*رابط متابعة طابور الانتظار:*
*Lien de suivi de la file d'attente:*
${lienSuivi || 'Lien non disponible'}

استخدم هذا الرابط لمعرفة رقمك في الطابور ووقت الانتظار المتوقع
Utilisez ce lien pour connaître votre numéro dans la file et le temps d'attente estimé

شكراً - Merci`;

    // Debug du message final
    console.log('=== MESSAGE FINAL CONSULTATION ===');
    console.log(message);
    console.log('===================================');
    
    return message;
}

// Vérification du numéro de téléphone
function verifierTelephone() {
    const telephone = "{{ $telephoneNettoye ?? '' }}";
    
    if (!telephone || telephone.trim() === '' || telephone === 'N/A') {
        alert('❌ Aucun numéro de téléphone disponible pour ce patient.\n\nVeuillez ajouter un numéro dans la fiche patient.');
        return false;
    }
    
    const phoneClean = telephone.replace(/[\s\-\(\)]/g, '');
    if (phoneClean.length < 8) {
        alert('❌ Le numéro de téléphone semble invalide.\n\nVeuillez vérifier le numéro dans la fiche patient.');
        return false;
    }
    
    return true;
}

function mostrarNotificationSucces() {
    const message = 'Message de confirmation envoyé vers WhatsApp !';
    
    // Créer une notification temporaire
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: #25D366;
        color: white;
        padding: 15px 20px;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        z-index: 10000;
        font-weight: bold;
        animation: slideIn 0.3s ease;
    `;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    // Supprimer après 3 secondes
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

// Ajouter les animations CSS
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
`;
document.head.appendChild(style);
</script>
</body>
</html> 