<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ordonnance - {{ $ordonnance->refOrd ?? 'ORDONNANCE' }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #fff;
            padding: 0;
            margin: 0;
        }

        .page {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            background: white;
            padding: 12mm 15mm 10mm 15mm;
            position: relative;
        }

        .a4 {
            width: 210mm;
            min-height: 297mm;
            padding: 12mm 15mm 10mm 15mm;
        }

        .a5 {
            width: 148mm;
            min-height: 210mm;
            padding: 8mm 10mm 8mm 10mm;
        }

        .a5 .header-left,
        .a5 .header-right {
            font-size: 9px;
        }

        .a5 .header-left .cabinet-name,
        .a5 .header-right .cabinet-name-ar {
            font-size: 11px;
        }

        .a5 .header-center img {
            max-width: 70px;
            max-height: 60px;
        }

        .a5 .patient-info {
            font-size: 9px;
        }

        .a5 .title-ar {
            font-size: 15px;
        }

        .a5 .title-fr {
            font-size: 13px;
        }

        .a5 .ordonnance-body {
            min-height: 340px;
            font-size: 11px;
        }

        .a5 .medication-name {
            font-size: 11px;
        }

        .a5 .medication-dosage {
            font-size: 10px;
        }

        .a5 .footer {
            font-size: 8px;
        }

        /* Informations patient */
        .patient-info {
            margin-bottom: 24px;
            font-size: 13px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            align-items: baseline;
            line-height: 1.5;
        }

        .info-row-rtl {
            direction: rtl;
            text-align: right;
        }

        .info-field {
            flex: 1;
        }

        .info-field-center {
            flex: 1;
            text-align: center;
        }

        .patient-info strong {
            font-weight: bold;
            font-size: 13px;
        }

        /* Titre Ordonnance */
        .title-section {
            text-align: center;
            margin: 20px 0 26px 0;
        }

        .title-ar {
            font-size: 17px;
            font-weight: bold;
            margin-bottom: 4px;
            letter-spacing: 0.3px;
        }

        .title-fr {
            font-size: 15px;
            font-weight: bold;
            letter-spacing: 5px;
        }

        /* Corps de l'ordonnance */
        .ordonnance-body {
            min-height: 460px;
            padding: 10px 20px;
            font-size: 12px;
            line-height: 2.2;
            position: relative;
        }
        

        .medication-item {
            margin-bottom: 18px;
            page-break-inside: avoid;
        }

        .medication-name {
            font-weight: bold;
            font-size: 13px;
        }

        .medication-dosage {
            margin-left: 25px;
            font-style: normal;
            color: #444;
            font-size: 12px;
        }

        /* Pied de page */
        .footer {
            position: absolute;
            bottom: 8mm;
            left: 15mm;
            right: 15mm;
            border-top: 1.5px solid #000;
            padding-top: 5px;
            text-align: center;
            font-size: 9px;
            line-height: 1.6;
        }

        .footer-ar {
            font-weight: normal;
            margin-bottom: 1px;
            font-size: 9px;
        }

        .footer-fr {
            font-style: italic;
            font-size: 9px;
        }

        /* Contrôles d'impression */
        .print-controls {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin: 18px 0;
        }

        .print-controls select,
        .print-controls button {
            padding: 8px 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
        }

        .print-controls button {
            background: #2c5282;
            color: #fff;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .print-controls button:hover {
            background: #1a365d;
        }

        .print-controls .btn-success {
            background: #28a745;
        }

        .print-controls .btn-success:hover {
            background: #218838;
        }

        .print-controls .btn-secondary {
            background: #6c757d;
        }

        .print-controls .btn-secondary:hover {
            background: #5a6268;
        }

        @media print {
            body {
                margin: 0;
                padding: 0;
            }

            .page {
                margin: 0;
                padding: 12mm 15mm 10mm 15mm;
                box-shadow: none;
            }

            .print-controls {
                display: none !important;
            }

            .footer {
                position: fixed;
                bottom: 8mm;
                left: 15mm;
                right: 15mm;
            }


            @page {
                margin: 0;
                size: A4 portrait;
            }
        }

        @media screen {
            .page {
                box-shadow: 0 0 10px rgba(0,0,0,0.1);
                margin: 20px auto;
            }
            
            body {
                background: #f5f5f5;
            }
        }
    </style>
</head>
<body>
    <div class="print-controls">
        <select id="pageFormat" onchange="updatePageFormat()">
            <option value="A4">Format A4</option>
            <option value="A5">Format A5</option>
        </select>
        <button onclick="window.print()">
            <i class="fas fa-print"></i> Imprimer
        </button>
        <button class="btn-secondary" onclick="window.location.href='{{ route('ordonnance.blank') }}'">
            <i class="fas fa-file-medical"></i> Ordonnance vierge
        </button>
    </div>

    <div class="page a4">
        <!-- En-tête -->
        @include('partials.recu-header')

        <!-- Informations patient -->
        <div class="patient-info">
            <!-- Ligne 1 : Date seule -->
            <div class="info-row" style="margin-bottom: 10px; justify-content: flex-end;">
                @php
                    $isBlank = !isset($ordonnance->patient) || empty($ordonnance->refOrd);
                @endphp
                <span><strong>Date : {{ !$isBlank && isset($ordonnance->dtPrescript) ? \Carbon\Carbon::parse($ordonnance->dtPrescript)->format('d/m/Y') : '.....................' }}</strong></span>
            </div>
            
            <!-- Ligne 2 : Nom et Prénom -->
            <div class="info-row" style="margin-bottom: 10px;">
                <div style="width: 100%; display: flex; justify-content: space-between; align-items: baseline;">
                    <span><strong>Nom et Prénom :</strong></span>
                    <span style="flex: 1; text-align: center; border-bottom: 1px dotted #000; margin: 0 5px;">
                        <strong>{{ isset($ordonnance->patient) && $ordonnance->patient->NomContact ? $ordonnance->patient->NomContact : '' }}</strong>
                    </span>
                    <span style="direction: rtl;"><strong>الاسم و اللقب :</strong></span>
                </div>
            </div>
            
            <!-- Ligne 3 : Age, Poids, Age (arabe) -->
            <div class="info-row" style="display: flex; justify-content: space-between; align-items: baseline;">
                @php
                    $age = null;
                    if (isset($ordonnance->patient) && $ordonnance->patient->DtNaissance) {
                        try {
                            $dateNaissance = \Carbon\Carbon::parse($ordonnance->patient->DtNaissance);
                            $age = $dateNaissance->age;
                        } catch (\Exception $e) {
                            $age = null;
                        }
                    }
                @endphp
                <span style="flex: 0 0 auto;"><strong>Age : {{ $age ? $age . ' ans' : '......' }}</strong></span>
                <span style="flex: 1; text-align: center;"><strong>Poids :</strong>...............<strong>الوزن</strong></span>
                <span style="flex: 0 0 auto; direction: rtl;"><strong>العمر : {{ $age ? $age : '......' }}</strong></span>
            </div>
        </div>

        <!-- Titre -->
        <div class="title-section">
            <div class="title-ar">وصفة طبية</div>
            <div class="title-fr">ORDONNANCE</div>
        </div>

        <!-- Corps de l'ordonnance -->
        <div class="ordonnance-body">
            @if(isset($ordonnance->ordonnances) && count($ordonnance->ordonnances) > 0)
                @foreach($ordonnance->ordonnances as $index => $ligne)
                    <div class="medication-item">
                        <div class="medication-name">{{ $index + 1 }}. {{ $ligne->Libelle }}</div>
                        @if($ligne->Utilisation)
                            <div class="medication-dosage">{{ $ligne->Utilisation }}</div>
                        @endif
                    </div>
                @endforeach
            @else
                <!-- Espace vide pour prescription manuscrite -->
            @endif
        </div>

        <!-- Pied de page -->
        <div class="footer">
            <div class="footer-ar">الرجاء إحضار الوصفة عند الاستشارة القادمة</div>
            <div class="footer-fr">Prier de rapporter l'ordonnance à la prochaine Consultation</div>
            <div style="margin-top: 10px;">
            @include('partials.recu-footer')
            </div>
        </div>
    </div>

    <script>
        // Cache des éléments DOM
        const elements = {
            pageFormat: document.getElementById('pageFormat'),
            container: document.querySelector('.page')
        };

        // Fonction pour changer le format de page
        function updatePageFormat() {
            const isA5 = elements.pageFormat.value === 'A5';
            elements.container.classList.toggle('a4', !isA5);
            elements.container.classList.toggle('a5', isA5);
        }

        // Auto-print si paramètre autoprint
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('autoprint') === '1') {
            window.onload = function() {
                setTimeout(function() {
                    window.print();
                }, 500);
            };
        }
    </script>
</body>
</html>
