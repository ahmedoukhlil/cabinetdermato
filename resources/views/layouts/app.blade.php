<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Cabinet Dr Salma</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Animations CSS -->
    <link rel="stylesheet" href="{{ asset('css/animations.css') }}">
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @livewireStyles
    <style>
        .text-primary { color: #1e3a8a !important; }
        .bg-primary { background-color: #1e3a8a !important; }
        .border-primary { border-color: #1e3a8a !important; }
        .hover\:bg-primary:hover { background-color: #1e3a8a !important; }
        .hover\:text-primary:hover { color: #1e3a8a !important; }
        .bg-primary-light { background-color: #e6eaf2 !important; }
        .text-primary-dark { color: #152a5c !important; }
        .hover\:text-white:hover { color: #fff !important; }
        
        /* Masquer les spinners globaux de Livewire */
        [wire\:loading], [wire\:loading\.delay], [wire\:loading\.inline-block], [wire\:loading\.inline] {
            display: none !important;
        }

        /* Animations personnalisées pour les transitions de menu */
        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-20px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @keyframes slideOutUp {
            from {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
            to {
                opacity: 0;
                transform: translateY(-20px) scale(0.95);
            }
        }

        @keyframes fadeInScale {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes pulseGlow {
            0%, 100% {
                box-shadow: 0 0 5px rgba(30, 58, 138, 0.3);
            }
            50% {
                box-shadow: 0 0 20px rgba(30, 58, 138, 0.6);
            }
        }

        /* Classes d'animation utilitaires */
        .animate-slide-in-down {
            animation: slideInDown 0.7s ease-out;
        }

        .animate-slide-out-up {
            animation: slideOutUp 0.5s ease-in;
        }

        .animate-fade-in-scale {
            animation: fadeInScale 0.6s ease-out;
        }

        .animate-pulse-glow {
            animation: pulseGlow 2s ease-in-out infinite;
        }

        /* Transitions fluides pour les boutons */
        .menu-button {
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            transform-origin: center;
        }

        .menu-button:hover {
            transform: translateY(-2px) scale(1.02);
        }

        .menu-button:active {
            transform: translateY(0) scale(0.98);
        }

        /* Effet de ripple pour les boutons */
        .ripple {
            position: relative;
            overflow: hidden;
        }

        .ripple-effect {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.6);
            transform: scale(0);
            animation: ripple-animation 0.6s linear;
            pointer-events: none;
        }

        @keyframes ripple-animation {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }

        /* Optimisations pour les transitions Livewire */
        .livewire-transition {
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Amélioration des performances de rendu */
        .gpu-accelerated {
            transform: translateZ(0);
            backface-visibility: hidden;
            perspective: 1000px;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">

    <!-- Navbar -->
    <nav class="bg-white shadow-lg border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center space-x-8">
                    <div class="flex items-center">
                        <a href="{{ route('accueil.patient') }}" class="flex items-center">
                            <span class="text-xl font-bold text-[#1e3a8a]">Cabinet Dr Salma</span>
                        </a>
                    </div>
                    <!-- Menu desktop -->
                    <div class="hidden md:flex space-x-4">
                        
                    </div>
                </div>

                <!-- Bouton menu mobile -->
                <div class="flex items-center md:hidden">
                    <button type="button" class="mobile-menu-button inline-flex items-center justify-center p-2 rounded-md text-gray-500 hover:text-blue-600 hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>

                <!-- Menu utilisateur -->
                <div class="hidden md:flex items-center space-x-4">
                    @if(auth()->check())
                        <div class="relative group">
                            <button class="flex items-center space-x-3 px-4 py-2 rounded-lg text-gray-700 hover:text-blue-600 hover:bg-blue-50 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 border border-gray-200">
                                <span class="font-medium">{{ auth()->user()->NomComplet ?? auth()->user()->name ?? 'Utilisateur' }}</span>
                                <i class="fas fa-chevron-down text-sm group-hover:text-blue-600 transition-colors duration-200"></i>
                            </button>
                            <div class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-100 opacity-0 group-hover:opacity-100 transition-all duration-200 transform origin-top-right scale-95 group-hover:scale-100 z-50">
                                <div class="py-1">
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="block">
                                        @csrf
                                        <button type="submit" class="w-full text-left px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-all duration-200">
                                            Déconnexion
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 shadow-sm hover:shadow">
                            Connexion
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Menu mobile -->
        <div class="mobile-menu hidden md:hidden">
            <div class="px-2 pt-2 pb-3 space-y-2">
               
                
                @if(auth()->check())
                    <form id="mobile-logout-form" action="{{ route('logout') }}" method="POST" class="block">
                        @csrf
                        <button type="submit" class="w-full text-left px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-all duration-200">
                            Déconnexion
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex flex-col items-center justify-center py-8">
        <div class="w-full bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-8">
            @yield('content')
        </div>
    </main>

    @livewireScripts

            <!-- jQuery et Select2 -->
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        
        <!-- Animations des modaux de la Gestion du Cabinet -->
        <link href="{{ asset('css/modal-animations.css') }}" rel="stylesheet">
        <script src="{{ asset('js/modal-enhancer.js') }}"></script>

    <!-- Fonction globale WhatsApp -->
    <script>
        // Variable globale pour suivre l'onglet WhatsApp ouvert
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
    </script>

    <!-- Styles for nav-link -->
    <style>
        .nav-link {
            @apply text-gray-600 hover:text-blue-600 px-4 py-2 rounded-lg transition-all duration-200 font-medium hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 border border-gray-200;
        }
        .nav-link.active {
            @apply text-blue-600 bg-blue-50 ring-2 ring-blue-500 ring-offset-2 border-blue-200;
        }
        .mobile-nav-link {
            @apply text-gray-600 hover:text-blue-600 hover:bg-blue-50 transition-all duration-200 border border-gray-200;
        }
        .mobile-nav-link.active {
            @apply text-blue-600 bg-blue-50 font-medium border-blue-200;
        }
    </style>

    <!-- Script pour le menu mobile -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btn = document.querySelector('.mobile-menu-button');
            const menu = document.querySelector('.mobile-menu');

            btn.addEventListener('click', () => {
                menu.classList.toggle('hidden');
            });
        });
    </script>

    <!-- Script impression modal paiement -->
    <script>
    document.addEventListener('imprimer-modal', function () {
        console.log('Événement imprimer-modal reçu !');
        let printModal = document.getElementById('historique-paiement-modal');
        if (!printModal) return;
        let printContents = printModal.innerHTML;
        let mywindow = window.open('', 'PRINT', 'height=800,width=1200');
        let styles = '';
        document.querySelectorAll('link[rel="stylesheet"]').forEach(function(node) {
            styles += node.outerHTML;
        });
        styles += `
            <style>
                body { background: #fff !important; color: #222 !important; font-family: Arial, sans-serif !important; font-size: 12px !important; line-height: 1.4 !important; margin: 0 !important; padding: 20px !important; }
                * { visibility: visible !important; display: initial !important; }
                table { width: 100% !important; border-collapse: collapse !important; margin-bottom: 20px !important; }
                th, td { border: 1px solid #000 !important; padding: 4px 8px !important; text-align: left !important; }
                th { background-color: #f3f4f6 !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
                .print\\:hidden { display: none !important; }
                @page { size: A4; margin: 10mm; }
            </style>
        `;
        mywindow.document.write('<html><head><title>Historique des paiements</title>');
        mywindow.document.write(styles);
        mywindow.document.write('</head><body>');
        mywindow.document.write(printContents);
        mywindow.document.write('</body></html>');
        mywindow.document.close();
        mywindow.focus();
        setTimeout(function() {
            mywindow.print();
            mywindow.close();
        }, 500);
    });
    </script>

    {{-- Écouteur global unique pour l'événement open-receipt --}}
    <script>
        // Écouteur global pour éviter les doublons quand plusieurs composants sont chargés
        if (!window.openReceiptListenerAdded) {
            window.addEventListener('open-receipt', function(e) {
                console.log('Événement open-receipt reçu:', e.detail);
                if (e.detail && e.detail.url) {
                    console.log('Ouverture de l\'URL:', e.detail.url);
                    window.open(e.detail.url, '_blank');
                }
            });
            window.openReceiptListenerAdded = true;
        }
    </script>
</body>
</html>