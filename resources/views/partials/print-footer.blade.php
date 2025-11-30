<div class="print-footer">
    @php
        $footerImagePath = public_path('Pieddepagedermato.png');
        $imageExists = file_exists($footerImagePath);
        $imageBase64 = null;
        if ($imageExists) {
            try {
                $imageData = file_get_contents($footerImagePath);
                $imageBase64 = 'data:image/png;base64,' . base64_encode($imageData);
            } catch (\Exception $e) {
                \Log::error('Erreur lors de la lecture de l\'image de pied de page', ['error' => $e->getMessage()]);
            }
        }
    @endphp
    @if($imageBase64)
    <div style="margin-top: 20px;">
        <img src="{{ $imageBase64 }}" alt="Pied de page" style="width: 100%; max-width: 100%; height: auto; display: block;" />
    </div>
    @endif
</div> 