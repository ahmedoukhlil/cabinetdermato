<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ORDONNANCE {{ $ordonnance->refOrd }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Arial', 'Tahoma', sans-serif; margin: 0; padding: 0; background: #fff; font-size: 12px; direction: ltr; }
        .a4 { width: 210mm; min-height: 297mm; margin: auto; background: #fff; padding: 10mm; position: relative; box-sizing: border-box; display: flex; flex-direction: column; }
        .a5 { width: 148mm; min-height: 210mm; margin: auto; background: #fff; padding: 8mm; position: relative; box-sizing: border-box; display: flex; flex-direction: column; }
        
        /* En-tête */
        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header img { max-width: 100%; height: auto; margin-bottom: 8px; }
        .cabinet-name { font-size: 18px; font-weight: bold; color: #000; margin-bottom: 5px; }
        .cabinet-details { font-size: 10px; color: #666; }
        
        /* Titre */
        .ordonnance-title { text-align: center; font-size: 20px; font-weight: bold; margin: 15px 0; color: #000; }
        .a5 .ordonnance-title { font-size: 16px; margin: 10px 0; }
        
        /* Informations patient */
        .patient-info { margin: 15px 0; padding: 15px; }
        .patient-field { margin-bottom: 12px; font-size: 12px; }
        .patient-field-label { display: inline-block; min-width: 100px; font-weight: bold; color: #000; }
        .patient-field-value { display: inline-block; border-bottom: 1px dotted #333; min-width: 200px; padding: 0 5px; min-height: 18px; }
        .patient-field-arabic { display: inline-block; margin-left: 10px; direction: rtl; text-align: right; font-family: 'Arial', 'Tahoma', sans-serif; color: #666; }
        .patient-row { display: flex; align-items: baseline; margin-bottom: 10px; }
        .patient-row-inline { display: flex; align-items: baseline; gap: 20px; }
        
        /* Prescription */
        .prescription { margin: 20px 0; padding: 15px; border: 1px solid #ddd; }
        .prescription-header { font-size: 14px; font-weight: bold; color: #000; margin-bottom: 15px; text-align: center; padding-bottom: 8px; border-bottom: 2px solid #e5e7eb; }
        .medication-item { margin-bottom: 12px; padding: 8px; background: #f8f9fa; border-left: 3px solid #000; }
        .medication-number { display: inline-block; width: 22px; height: 22px; background: #000; color: white; border-radius: 50%; text-align: center; line-height: 22px; font-weight: bold; margin-right: 8px; font-size: 10px; }
        .medication-name { font-size: 12px; font-weight: bold; color: #1f2937; margin-bottom: 5px; }
        .medication-usage { font-size: 11px; color: #4b5563; margin-left: 30px; font-style: italic; }
        .medication-arabic { font-size: 11px; color: #4b5563; margin-left: 30px; margin-top: 3px; direction: rtl; text-align: right; font-family: 'Arial', 'Tahoma', sans-serif; }
        
        /* Signature */
        .signature { margin-top: 30px; text-align: right; padding-right: 20px; }
        .signature-label { font-weight: bold; margin-bottom: 40px; }
        .signature-name { font-style: italic; color: #000; }
        .signature-footer { position: fixed; bottom: 190px; right: 20px; text-align: right; z-index: 10; }
        .a5 .signature-footer { bottom: 170px; right: 15px; }
        @media print {
            .signature-footer { position: fixed; bottom: 190px; right: 20px; }
            .a5 .signature-footer { bottom: 170px; right: 15px; }
        }
        
        /* Référence */
        .reference { font-size: 9px; color: #666; text-align: center; margin-top: 20px; padding-top: 10px; border-top: 1px solid #e5e7eb; }
        
        /* Note en bas de page */
        .content-wrapper { flex: 1; display: flex; flex-direction: column; }
        .footer-note { font-size: 11px; color: #000; text-align: center; margin-top: auto; padding-top: 15px; padding-bottom: 10px; border-top: 1px solid #ddd; font-style: italic; position: sticky; bottom: 0; background: #fff; }
        .footer-note-arabic { direction: rtl; text-align: center; font-family: 'Arial', 'Tahoma', sans-serif; margin-top: 5px; }
        
        /* Contrôles d'impression */
        .print-controls { display: flex; gap: 10px; justify-content: flex-end; margin: 18px 0; }
        .print-controls select, .print-controls button { padding: 8px 12px; border: 1px solid #ccc; border-radius: 4px; font-size: 14px; cursor: pointer; }
        .print-btn { background: #2c5282; color: #fff; border: none; }
        .print-btn:hover { background: #1a365d; }
        .download-btn { background: #28a745; color: #fff; border: none; }
        .download-btn:hover { background: #218838; }
        
        @media print {
            .print-controls { display: none !important; }
            .a4, .a5 { box-shadow: none; }
            .footer-note { 
                position: fixed; 
                bottom: 0; 
                left: 0; 
                right: 0; 
                width: 100%; 
                margin-top: 0;
                padding: 15px 10mm 10px 10mm;
                background: #fff;
                border-top: 1px solid #ddd;
            }
            .a5 .footer-note {
                padding: 15px 8mm 10px 8mm;
            }
        }
        
        /* Support RTL pour l'arabe */
        .arabic-text { direction: rtl; text-align: right; font-family: 'Arial', 'Tahoma', sans-serif; }
    </style>
</head>
<body>
    <div class="print-controls">
        <select id="format-select">
            <option value="a4">Format A4</option>
            <option value="a5">Format A5</option>
        </select>
        <button class="print-btn" onclick="window.print()">
            <i class="fas fa-print"></i> Imprimer
        </button>
        <button class="blank-btn" onclick="window.open('{{ route('ordonnance.blank') }}', '_blank')" style="background: #6c757d; color: #fff; border: none; padding: 8px 12px; border-radius: 4px; font-size: 14px; cursor: pointer;">
            <i class="fas fa-file-medical"></i> Ordonnance vierge
        </button>
        @if(isset($ordonnance->id))
        <button class="download-btn" onclick="window.location.href='{{ route('ordonnance.download', ['id' => $ordonnance->id]) }}'">
            <i class="fas fa-download"></i> Télécharger PDF
        </button>
        @endif
    </div>

    <div id="ordonnance-content" class="a4">
        <div class="content-wrapper">
        {{-- En-tête --}}
        <div class="header">
            @php
                $headerImagePath = public_path('entetedermato.png');
                $imageExists = file_exists($headerImagePath);
                $imageBase64 = null;
                if ($imageExists) {
                    $imageData = file_get_contents($headerImagePath);
                    $imageBase64 = 'data:image/png;base64,' . base64_encode($imageData);
                }
            @endphp
            @if($imageBase64)
            <img src="{{ $imageBase64 }}" alt="En-tête du cabinet">
            @endif
            @php
                $nomCabinet = $cabinet['NomCabinet'] ?? $cabinet->NomCabinet ?? null;
                $adresse = $cabinet['Adresse'] ?? $cabinet->Adresse ?? null;
                $telephone = $cabinet['Telephone'] ?? $cabinet->Telephone ?? null;
                $email = $cabinet['Email'] ?? $cabinet->Email ?? null;
                
                // Exclure les valeurs par défaut
                $defaultValues = [
                    'Cabinet Savwa', 'Cabinet Dermatologie',
                    'Adresse de Cabinet Savwa', 'Adresse de Cabinet Dermatologie',
                    'Téléphone de Cabinet Savwa', 'Téléphone de Cabinet Dermatologie'
                ];
                if (in_array($nomCabinet, $defaultValues)) $nomCabinet = null;
                if (in_array($adresse, $defaultValues)) $adresse = null;
                if (in_array($telephone, $defaultValues)) $telephone = null;
            @endphp
            @if($nomCabinet || $adresse || $telephone || $email)
            <div>
                @if($nomCabinet)
                <div class="cabinet-name">{{ $nomCabinet }}</div>
                @endif
                <div class="cabinet-details">
                    @if($adresse)
                        {{ $adresse }}<br>
                    @endif
                    @if($telephone)
                        Tél: {{ $telephone }}
                    @endif
                    @if($email)
                        @if($telephone) | @endif Email: {{ $email }}
                    @endif
                </div>
            </div>
            @endif
        </div>

        {{-- Titre --}}
        <div class="ordonnance-title">
            {{ $ordonnance->TypeOrdonnance }}
        </div>

        {{-- Informations patient --}}
        <div class="patient-info">
            @php
                $isBlank = !isset($ordonnance->id) || !$ordonnance->patient;
                $datePrescription = $isBlank ? '' : \Carbon\Carbon::parse($ordonnance->dtPrescript)->format('d/m/Y');
                $nomPrenom = '';
                if ($ordonnance->patient) {
                    $nomPrenom = trim(($ordonnance->patient->Prenom ?? '') . ' ' . ($ordonnance->patient->Nom ?? ''));
                }
                $age = '';
                if ($ordonnance->patient && $ordonnance->patient->DtNaissance) {
                    $age = \Carbon\Carbon::parse($ordonnance->patient->DtNaissance)->age;
                }
                $poids = ''; // Le poids n'est pas stocké dans la base, à ajouter si nécessaire
            @endphp
            
            {{-- Date --}}
            <div class="patient-row" style="justify-content: flex-end;">
                <span class="patient-field-label">Date :</span>
                <span class="patient-field-value"></span>
            </div>
            
            {{-- Nom et Prénom --}}
            <div class="patient-row">
                <span class="patient-field-label">Nom et Prénom :</span>
                <span class="patient-field-value">{{ $nomPrenom ?: '' }}</span>
                <span class="patient-field-arabic">الاسم و اللقب</span>
            </div>
            
            {{-- Age et Poids sur la même ligne --}}
            <div class="patient-row-inline">
                <div class="patient-field">
                    <span class="patient-field-label">Age :</span>
                    <span class="patient-field-value" style="min-width: 80px;">{{ $age ?: '' }}</span>
                    <span class="patient-field-arabic">العمر</span>
                </div>
                <div class="patient-field">
                    <span class="patient-field-label">Poids :</span>
                    <span class="patient-field-value" style="min-width: 80px;">{{ $poids ?: '' }}</span>
                    <span class="patient-field-arabic">الوزن</span>
                </div>
            </div>
        </div>

        {{-- Prescription --}}
        @if(!$isBlank)
        <div class="prescription">
            <div class="prescription-header">
                ℞ PRESCRIPTION / وصفة طبية
            </div>

            @forelse($ordonnance->ordonnances as $index => $ligne)
            <div class="medication-item">
                <div class="medication-name">
                    <span class="medication-number">{{ $index + 1 }}</span>
                    {{ $ligne->Libelle }}
                </div>
                @if($ligne->Utilisation)
                <div class="medication-usage">
                    {{ $ligne->Utilisation }}
                </div>
                @endif
                {{-- Traduction arabe (à adapter selon vos besoins) --}}
                @if($ligne->Libelle)
                <div class="medication-arabic">
                    {{-- Ici vous pouvez ajouter la traduction arabe du médicament --}}
                    {{-- Exemple: {{ $ligne->LibelleAr ?? '' }} --}}
                </div>
                @endif
            </div>
            @empty
            <p style="text-align: center; color: #666; font-style: italic; padding: 20px 0;">
                Aucune prescription / لا يوجد وصفة
            </p>
            @endforelse
        </div>
        @endif

        </div>
        
        {{-- Prescripteur en bas à droite --}}
        <div class="signature-footer">
            <div style="font-weight: bold; margin-bottom: 5px;">Prescripteur / الطبيب المعالج :</div>
            <div style="border-bottom: 1px dotted #333; min-width: 200px; display: inline-block; padding: 0 5px; min-height: 18px; text-align: right;">
                @if($ordonnance->prescripteur && $ordonnance->prescripteur->NomComplet)
                    Dr. {{ $ordonnance->prescripteur->NomComplet }}
                @endif
            </div>
        </div>
        
        {{-- Note en bas de page --}}
        <div class="footer-note">
            Prier de rapporter l'ordonnance à la prochaine Consultation
            <div class="footer-note-arabic">
                يرجى إحضار الوصفة الطبية في الاستشارة القادمة
            </div>
        </div>
        
        {{-- Pied de page --}}
        @php
            $footerImagePath = public_path('Pieddepagedermato.png');
            $footerImageExists = file_exists($footerImagePath);
            $footerImageBase64 = null;
            if ($footerImageExists) {
                $footerImageData = file_get_contents($footerImagePath);
                $footerImageBase64 = 'data:image/png;base64,' . base64_encode($footerImageData);
            }
        @endphp
        @if($footerImageBase64)
        <div style="margin-top: 20px; text-align: center;">
            <img src="{{ $footerImageBase64 }}" alt="Pied de page" style="max-width: 100%; height: auto;">
        </div>
        @endif
    </div>

    <script>
        document.getElementById('format-select').addEventListener('change', function() {
            const content = document.getElementById('ordonnance-content');
            if (this.value === 'a5') {
                content.classList.remove('a4');
                content.classList.add('a5');
            } else {
                content.classList.remove('a5');
                content.classList.add('a4');
            }
        });
    </script>
</body>
</html>
