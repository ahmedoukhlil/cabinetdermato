<div class="footer" style="text-align:center; margin-top:40px;">
    @php
        $footerImagePath = public_path('Pieddepagedermato.png');
        $footerImageExists = file_exists($footerImagePath);
        $footerImageBase64 = null;
        if ($footerImageExists) {
            try {
                $footerImageData = file_get_contents($footerImagePath);
                $footerImageBase64 = 'data:image/png;base64,' . base64_encode($footerImageData);
            } catch (\Exception $e) {
                \Log::error('Erreur lors de la lecture de l\'image de pied de page', ['error' => $e->getMessage()]);
            }
        }
    @endphp
    @if($footerImageBase64)
    <div style="margin-bottom: 15px;">
        <img src="{{ $footerImageBase64 }}" alt="Pied de page" style="max-width: 100%; height: auto;">
    </div>
    @endif
</div> 