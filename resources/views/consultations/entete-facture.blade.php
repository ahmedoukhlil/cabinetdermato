<div style="width: 100%; margin-bottom: 10px;">
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
    <img src="{{ $imageBase64 }}" alt="En-tête du cabinet" style="width: 100%; max-width: 100%; height: auto; display: block;" />
    @endif
</div> 