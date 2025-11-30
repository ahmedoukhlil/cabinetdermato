<div class="print-header" style="text-align:center; margin-bottom:20px;">
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
        
    @endphp
    @if($imageBase64)
    <div style="margin-bottom: 15px; width: 100%; text-align: center;">
        <img src="{{ $imageBase64 }}" alt="En-tête du cabinet" style="max-width: 100%; height: auto; display: block; margin: 0 auto;">
    </div>
    @endif
</div> 