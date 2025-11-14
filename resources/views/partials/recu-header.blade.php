<div class="header" style="text-align:center; margin-bottom:20px;">
    @php
        $headerImagePath = public_path('entetedermato.png');
        $imageExists = file_exists($headerImagePath);
        $imageBase64 = null;
        if ($imageExists) {
            try {
                $imageData = file_get_contents($headerImagePath);
                $imageBase64 = 'data:image/png;base64,' . base64_encode($imageData);
            } catch (\Exception $e) {
                \Log::error('Erreur lors de la lecture de l\'image d\'en-tête', ['error' => $e->getMessage()]);
            }
        }
        
        // Gérer $cabinet comme tableau ou objet
        $nomCabinet = null;
        $adresse = null;
        $telephone = null;
        if (isset($cabinet)) {
            if (is_array($cabinet)) {
                $nomCabinet = $cabinet['NomCabinet'] ?? null;
                $adresse = $cabinet['Adresse'] ?? null;
                $telephone = $cabinet['Telephone'] ?? null;
            } else {
                $nomCabinet = $cabinet->NomCabinet ?? null;
                $adresse = $cabinet->Adresse ?? null;
                $telephone = $cabinet->Telephone ?? null;
            }
            
            // Exclure les valeurs par défaut
            $defaultValues = [
                'Cabinet Savwa', 'Cabinet Dermatologie',
                'Adresse de Cabinet Savwa', 'Adresse de Cabinet Dermatologie',
                'Téléphone de Cabinet Savwa', 'Téléphone de Cabinet Dermatologie'
            ];
            if (in_array($nomCabinet, $defaultValues)) $nomCabinet = null;
            if (in_array($adresse, $defaultValues)) $adresse = null;
            if (in_array($telephone, $defaultValues)) $telephone = null;
        }
    @endphp
    @if($imageBase64)
    <div style="margin-bottom: 15px;">
        <img src="{{ $imageBase64 }}" alt="En-tête du cabinet" style="max-width: 100%; height: auto;">
    </div>
    @endif
    @if($nomCabinet || $adresse || $telephone)
    <div class="text-center mb-4">
        @if($nomCabinet)
        <h1 class="text-2xl font-bold text-gray-800">{{ $nomCabinet }}</h1>
        @endif
        @if($adresse)
        <p class="text-gray-600">{{ $adresse }}</p>
        @endif
        @if($telephone)
        <p class="text-gray-600">Tél: {{ $telephone }}</p>
        @endif
    </div>
    @endif
</div> 