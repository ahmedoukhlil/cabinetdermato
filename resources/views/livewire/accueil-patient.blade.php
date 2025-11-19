@push('styles')
<style>
    /* Transitions ultra-fluides pour la navigation entre boutons */
    .nav-button {
        position: relative;
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        transform-origin: center;
        will-change: transform, box-shadow, background-color, opacity;
        backface-visibility: hidden;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }

    .nav-button:hover {
        transform: translateY(-3px) scale(1.03);
        box-shadow: 0 15px 35px rgba(30, 58, 138, 0.2);
        filter: brightness(1.05);
    }

    .nav-button:active {
        transform: translateY(-1px) scale(0.97);
        transition: all 0.15s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        filter: brightness(0.95);
    }

    /* État de transition fluide */
    .nav-button.transitioning {
        pointer-events: none;
        transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    /* Animation de navigation fluide */
    .nav-button.navigating {
        animation: buttonNavigate 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
    }

    @keyframes buttonNavigate {
        0% {
            transform: scale(1) translateY(0);
            opacity: 1;
        }
        25% {
            transform: scale(0.95) translateY(-2px);
            opacity: 0.8;
        }
        50% {
            transform: scale(0.9) translateY(-5px);
            opacity: 0.6;
        }
        75% {
            transform: scale(0.95) translateY(-2px);
            opacity: 0.8;
        }
        100% {
            transform: scale(1) translateY(0);
            opacity: 1;
        }
    }

    /* Transitions ultra-fluides pour la sous-section Gestion du patient */
    .patient-submenu {
        transition: all 0.8s cubic-bezier(0.34, 1.56, 0.64, 1);
        transform-origin: top center;
        overflow: hidden;
        max-height: 0;
        opacity: 0;
        transform: translateY(-40px) scale(0.85);
        pointer-events: none;
        filter: blur(3px);
        margin-top: 0;
        padding: 0;
    }

    .patient-submenu.show {
        max-height: 300px;
        opacity: 1;
        transform: translateY(0) scale(1);
        pointer-events: all;
        filter: blur(0);
        margin-top: 0.5rem;
        padding: 0.5rem 0;
    }

    .patient-submenu.hide {
        max-height: 0;
        opacity: 0;
        transform: translateY(-40px) scale(0.85);
        pointer-events: none;
        filter: blur(3px);
        margin-top: 0;
        padding: 0;
    }

    /* Animation d'ouverture progressive avec effet de vague */
    .patient-submenu.show {
        animation: submenuWaveIn 0.8s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
    }

    @keyframes submenuWaveIn {
        0% {
            max-height: 0;
            opacity: 0;
            transform: translateY(-40px) scale(0.85);
            filter: blur(3px);
        }
        50% {
            max-height: 300px;
            opacity: 0.7;
            transform: translateY(-10px) scale(0.95);
            filter: blur(1px);
        }
        100% {
            max-height: 300px;
            opacity: 1;
            transform: translateY(0) scale(1);
            filter: blur(0);
        }
    }

    /* Animation de fermeture progressive */
    .patient-submenu.hide {
        animation: submenuWaveOut 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards;
    }

    @keyframes submenuWaveOut {
        0% {
            max-height: 300px;
            opacity: 1;
            transform: translateY(0) scale(1);
            filter: blur(0);
        }
        50% {
            max-height: 150px;
            opacity: 0.5;
            transform: translateY(-20px) scale(0.9);
            filter: blur(2px);
        }
        100% {
            max-height: 0;
            opacity: 0;
            transform: translateY(-40px) scale(0.85);
            filter: blur(3px);
        }
    }

    /* Animation d'entrée progressive ultra-fluide pour les boutons */
    .patient-submenu.show .nav-button {
        opacity: 0;
        transform: translateY(40px) scale(0.8);
        transition: all 0.7s cubic-bezier(0.34, 1.56, 0.64, 1);
        filter: blur(4px);
        animation: buttonSlideIn 0.7s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
    }

    .patient-submenu.show .nav-button:nth-child(1) {
        animation-delay: 0.2s;
        transition-delay: 0.2s;
    }

    .patient-submenu.show .nav-button:nth-child(2) {
        animation-delay: 0.3s;
        transition-delay: 0.3s;
    }

    .patient-submenu.show .nav-button:nth-child(3) {
        animation-delay: 0.4s;
        transition-delay: 0.4s;
    }

    .patient-submenu.show .nav-button:nth-child(4) {
        animation-delay: 0.5s;
        transition-delay: 0.5s;
    }

    .patient-submenu.show .nav-button.visible {
        opacity: 1;
        transform: translateY(0) scale(1);
        filter: blur(0);
    }

    /* Animation d'entrée des boutons avec effet de rebond */
    @keyframes buttonSlideIn {
        0% {
            opacity: 0;
            transform: translateY(40px) scale(0.8);
            filter: blur(4px);
        }
        60% {
            opacity: 1;
            transform: translateY(-5px) scale(1.05);
            filter: blur(0);
        }
        80% {
            transform: translateY(2px) scale(1.02);
        }
        100% {
            opacity: 1;
            transform: translateY(0) scale(1);
            filter: blur(0);
        }
    }

    /* Animation de sortie ultra-fluide pour les boutons */
    .patient-submenu.hide .nav-button {
        opacity: 0;
        transform: translateY(-30px) scale(0.85);
        transition: all 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        filter: blur(2px);
        animation: buttonSlideOut 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards;
    }

    /* Animation de sortie des boutons avec effet de disparition progressive */
    @keyframes buttonSlideOut {
        0% {
            opacity: 1;
            transform: translateY(0) scale(1);
            filter: blur(0);
        }
        30% {
            opacity: 0.7;
            transform: translateY(-10px) scale(0.95);
            filter: blur(1px);
        }
        100% {
            opacity: 0;
            transform: translateY(-30px) scale(0.85);
            filter: blur(2px);
        }
    }

    /* Conteneur principal ultra-fluide pour éviter les sauts */
    .patient-menu-container {
        min-height: 0;
        transition: all 0.8s cubic-bezier(0.34, 1.56, 0.64, 1);
        overflow: hidden;
        position: relative;
    }

    .patient-menu-container.expanded {
        min-height: 140px;
        animation: containerExpand 0.8s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
    }

    /* Animation d'expansion du conteneur */
    @keyframes containerExpand {
        0% {
            min-height: 0;
            opacity: 0.8;
        }
        50% {
            min-height: 140px;
            opacity: 0.9;
        }
        100% {
            min-height: 140px;
            opacity: 1;
        }
    }

    /* Transitions de contenu fluides améliorées */
    .content-section {
        transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
        opacity: 0;
        transform: translateX(100%) scale(0.95);
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        overflow: hidden;
        will-change: transform, opacity, scale;
        z-index: 1;
        background: white;
        border-radius: 8px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .content-section.show {
        opacity: 1;
        transform: translateY(0) scale(1);
        animation: contentSlideIn 0.5s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
        z-index: 10;
    }

    .content-section.hiding {
        animation: contentSlideOut 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards;
        z-index: 5;
    }

    .content-section.entering {
        animation: contentSlideInFromRight 0.5s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
        z-index: 15;
    }

    .content-section.exiting {
        animation: contentSlideOutToLeft 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards;
        z-index: 5;
    }

    /* Animation d'entrée fluide du contenu */
    @keyframes contentSlideIn {
        0% {
            opacity: 0;
            transform: translateX(100%) scale(0.95);
        }
        60% {
            opacity: 0.8;
            transform: translateX(-5px) scale(1.02);
        }
        100% {
            opacity: 1;
            transform: translateX(0) scale(1);
        }
    }

    /* Animation de sortie fluide du contenu */
    @keyframes contentSlideOut {
        0% {
            opacity: 1;
            transform: translateX(0) scale(1);
        }
        100% {
            opacity: 0;
            transform: translateX(-100%) scale(0.95);
        }
    }

    /* Nouvelles animations de transition fluide avec chevauchement */
    @keyframes contentSlideInFromRight {
        0% {
            opacity: 0;
            transform: translateX(100%) scale(0.95);
        }
        60% {
            opacity: 0.8;
            transform: translateX(-5px) scale(1.02);
        }
        100% {
            opacity: 1;
            transform: translateX(0) scale(1);
        }
    }

    @keyframes contentSlideOutToLeft {
        0% {
            opacity: 1;
            transform: translateX(0) scale(1);
        }
        40% {
            opacity: 0.6;
            transform: translateX(-10px) scale(0.98);
        }
        100% {
            opacity: 0;
            transform: translateX(-100%) scale(0.95);
        }
    }

    /* Animation de transition croisée pour éviter les trous */
    @keyframes contentCrossFade {
        0% {
            opacity: 0;
            transform: translateX(50px) scale(0.95);
        }
        50% {
            opacity: 0.5;
            transform: translateX(0) scale(1);
        }
        100% {
            opacity: 1;
            transform: translateX(0) scale(1);
        }
    }

    /* Animation d'entrée progressive pour le contenu */
    .content-section.entering {
        animation: contentSlideIn 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
    }

    /* Animation de sortie progressive pour le contenu */
    .content-section.exiting {
        animation: contentSlideOut 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards;
    }

    /* Barre de progression pour les transitions de contenu */
    .content-section::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 3px;
        background: linear-gradient(90deg, #1e3a8a, #152a5c);
        transform: scaleX(0);
        transform-origin: left;
        transition: transform 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    .content-section.show::after {
        transform: scaleX(1);
    }

    /* Conteneur principal pour les transitions fluides */
    .content-container {
        position: relative;
        min-height: 400px;
        overflow: hidden;
        /* Position relative pour contenir les sections absolues */
        position: relative;
    }

    /* Wrapper pour les transitions fluides */
    .content-wrapper {
        position: relative;
        min-height: 400px;
        overflow: hidden;
    }

    /* État de transition pour éviter les sauts */
    .content-container.transitioning {
        pointer-events: none;
    }

    .content-container.transitioning .content-section {
        pointer-events: none;
    }

    /* Styles pour le clone de sortie */
    .exiting-clone {
        position: absolute !important;
        top: 0 !important;
        left: 0 !important;
        width: 100% !important;
        height: 100% !important;
        z-index: 1000 !important;
        background: white;
        border-radius: 8px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    /* Animation d'entrée pour le nouveau contenu après Livewire */
    .content-section.entering-after-livewire {
        animation: contentSlideInFromRight 0.5s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
    }

    /* TRANSITIONS FLUIDES SPÉCIALES POUR LES BOUTONS DE SOUS-SECTION PATIENT */
    .patient-nav-button {
        position: relative;
        overflow: hidden;
        transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        transform-origin: center;
        will-change: transform, box-shadow, background-color, opacity, filter;
        backface-visibility: hidden;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }

    .patient-nav-button:hover {
        transform: translateY(-4px) scale(1.05);
        box-shadow: 0 20px 40px rgba(30, 58, 138, 0.25);
        filter: brightness(1.1) saturate(1.1);
    }

    .patient-nav-button:active {
        transform: translateY(-2px) scale(0.98);
        transition: all 0.2s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        filter: brightness(0.95);
    }

    /* État de transition fluide pour les boutons de sous-section */
    .patient-nav-button.transitioning {
        pointer-events: none;
        transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    /* Animation de navigation fluide pour les boutons de sous-section */
    .patient-nav-button.navigating {
        animation: patientButtonNavigate 0.7s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
    }

    @keyframes patientButtonNavigate {
        0% {
            transform: scale(1) translateY(0);
            opacity: 1;
            filter: brightness(1);
        }
        20% {
            transform: scale(0.92) translateY(-3px);
            opacity: 0.9;
            filter: brightness(0.9);
        }
        40% {
            transform: scale(0.88) translateY(-6px);
            opacity: 0.8;
            filter: brightness(0.8);
        }
        60% {
            transform: scale(0.92) translateY(-3px);
            opacity: 0.9;
            filter: brightness(0.9);
        }
        80% {
            transform: scale(0.96) translateY(-1px);
            opacity: 0.95;
            filter: brightness(0.95);
        }
        100% {
            transform: scale(1) translateY(0);
            opacity: 1;
            filter: brightness(1);
        }
    }

    /* Effet de brillance au survol pour les boutons de sous-section */
    .patient-nav-button::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
        transition: left 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
        z-index: 1;
        pointer-events: none;
    }

    .patient-nav-button:hover::before {
        left: 100%;
    }

    /* Effet de soulignement animé pour les boutons de sous-section */
    .patient-nav-button::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        width: 0;
        height: 3px;
        background: linear-gradient(90deg, #1e3a8a, #3b82f6, #1e3a8a);
        transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        transform: translateX(-50%);
        border-radius: 2px;
        z-index: 1;
    }

    .patient-nav-button:hover::after {
        width: 80%;
    }

    /* Animation spéciale pour les boutons cliqués */
    .patient-nav-button.clicked {
        animation: patientButtonClick 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
    }

    @keyframes patientButtonClick {
        0% {
            transform: scale(1);
            filter: brightness(1);
        }
        50% {
            transform: scale(0.9);
            filter: brightness(0.8);
        }
        100% {
            transform: scale(1);
            filter: brightness(1);
        }
    }

    /* États actif/inactif pour les boutons de sous-section */
    .patient-nav-button.active {
        background: linear-gradient(135deg, #1e3a8a, #3b82f6) !important;
        color: white !important;
        border-color: #1e3a8a !important;
        box-shadow: 0 20px 40px rgba(30, 58, 138, 0.4) !important;
        transform: translateY(-2px) scale(1.02) !important;
    }

    .patient-nav-button.active .icon-container {
        background: rgba(255, 255, 255, 0.2) !important;
        color: white !important;
    }

    .patient-nav-button.active .icon-container i {
        color: white !important;
    }

    .patient-nav-button.inactive {
        background: #f8fafc !important;
        color: #64748b !important;
        border-color: #e2e8f0 !important;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05) !important;
        transform: scale(0.98) !important;
        filter: grayscale(0.3) brightness(0.9) !important;
    }

    .patient-nav-button.inactive:hover {
        background: #f1f5f9 !important;
        color: #475569 !important;
        border-color: #cbd5e1 !important;
        transform: translateY(-2px) scale(1.02) !important;
        filter: grayscale(0.1) brightness(1) !important;
    }

    /* Animation de transition entre les états */
    .patient-nav-button.active,
    .patient-nav-button.inactive {
        transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1) !important;
    }

    /* Animations d'entrée et de sortie pour le contenu */
    .content-section.entering {
        animation: contentSlideIn 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
    }

    .content-section.exiting {
        animation: contentSlideOut 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards;
    }

    @keyframes contentSlideIn {
        from {
            opacity: 0;
            transform: translateY(20px) scale(0.98);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    @keyframes contentSlideOut {
        from {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
        to {
            opacity: 0;
            transform: translateY(-20px) scale(0.98);
        }
    }

    /* Effet de transition fluide entre les sections */
    .content-transition {
        transition: all 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    }

    .content-transition.fade-out {
        opacity: 0;
        transform: translateY(-15px);
    }

    .content-transition.fade-in {
        opacity: 1;
        transform: translateY(0);
    }

    /* Transitions fluides pour l'apparition/disparition du bouton et sous-menu Gestion du patient */
    .patient-management-section {
        transition: all 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
        overflow: hidden;
    }

    .patient-management-section.hidden {
        max-height: 0;
        opacity: 0;
        transform: translateY(-20px) scale(0.95);
        pointer-events: none;
        margin: 0;
        padding: 0;
    }

    .patient-management-section.visible {
        max-height: 200px;
        opacity: 1;
        transform: translateY(0) scale(1);
        pointer-events: all;
    }

    /* Animation d'apparition progressive pour le bouton principal */
    .patient-button-appear {
        animation: buttonSlideIn 0.5s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
    }

    @keyframes buttonSlideIn {
        from {
            opacity: 0;
            transform: translateY(-20px) scale(0.9);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    /* Animation d'apparition progressive pour le sous-menu */
    .submenu-appear {
        animation: submenuSlideIn 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
    }

    @keyframes submenuSlideIn {
        from {
            opacity: 0;
            transform: translateY(-30px) scale(0.95);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    /* Transitions pour les changements de couleur */
    .nav-button.color-transition {
        transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    }

    /* Effet de profondeur au hover */
    .nav-button:hover {
        box-shadow: 
            0 10px 25px rgba(0, 0, 0, 0.15),
            0 4px 10px rgba(0, 0, 0, 0.1);
    }

    /* Animation de chargement pour les boutons */
    .nav-button.loading {
        position: relative;
        overflow: hidden;
    }

    .nav-button.loading::after {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
        animation: loading-shimmer 1.5s infinite;
    }

    @keyframes loading-shimmer {
        0% { left: -100%; }
        100% { left: 100%; }
    }

    /* Transitions pour les modales */
    .modal-transition {
        transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    }

    /* Optimisations de performance */
    .nav-button {
        backface-visibility: hidden;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }

    /* Responsive transitions */
    @media (max-width: 768px) {
        .nav-button:hover {
            transform: none;
        }
        
        .nav-button:active {
            transform: scale(0.98);
        }
    }

    /* Effets spéciaux pour la sous-section patient */
    .patient-submenu .nav-button.clicked {
        animation: button-click 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    }

    @keyframes button-click {
        0% { transform: scale(1); }
        50% { transform: scale(0.95); }
        100% { transform: scale(1); }
    }

    /* Amélioration des transitions pour les boutons de la sous-section patient */
    .patient-submenu .nav-button {
        position: relative;
        overflow: hidden;
    }

    .patient-submenu .nav-button::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        transition: left 0.6s ease;
    }

    .patient-submenu .nav-button:hover::before {
        left: 100%;
    }

    /* Transitions pour les changements de contenu */
    .content-change-transition {
        transition: all 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    }

    .content-change-transition.fade-out {
        opacity: 0;
        transform: translateY(-20px) scale(0.98);
    }

    .content-change-transition.fade-in {
        opacity: 1;
        transform: translateY(0) scale(1);
    }

    /* Animation spéciale ultra-fluide pour les boutons de navigation du patient */
    .patient-nav-button {
        transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
        position: relative;
        overflow: hidden;
        transform-origin: center;
    }

    .patient-nav-button::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        width: 0;
        height: 3px;
        background: linear-gradient(90deg, #1e3a8a, #152a5c);
        transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        transform: translateX(-50%);
        border-radius: 2px;
    }

    .patient-nav-button:hover::after {
        width: 100%;
        box-shadow: 0 2px 8px rgba(30, 58, 138, 0.3);
    }

    .patient-nav-button:active {
        transform: scale(0.96);
        transition: all 0.1s ease-out;
    }

    /* Effet de brillance au hover */
    .patient-nav-button::before {
        content: '';
        position: absolute;
        top: -100%;
        left: -100%;
        width: 300%;
        height: 300%;
        background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
        transition: all 0.6s ease;
        transform: rotate(45deg);
    }

    .patient-nav-button:hover::before {
        top: -50%;
        left: -50%;
    }

    /* Animation de pulsation pour les boutons actifs */
    .patient-nav-button.active {
        animation: activePulse 2s ease-in-out infinite;
    }

    @keyframes activePulse {
        0%, 100% {
            box-shadow: 0 0 0 0 rgba(30, 58, 138, 0.7);
        }
        50% {
            box-shadow: 0 0 0 10px rgba(30, 58, 138, 0);
        }
    }

    /* Transitions ultra-fluides pour la navigation entre boutons */
    .nav-button.nav-transition {
        transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    /* Effet de focus amélioré pour l'accessibilité */
    .nav-button:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.3);
        transform: translateY(-2px) scale(1.02);
    }

    /* Animation de transition entre états */
    .nav-button.state-transition {
        animation: stateTransition 0.4s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
    }

    @keyframes stateTransition {
        0% {
            transform: scale(1) translateY(0);
        }
        50% {
            transform: scale(0.95) translateY(-2px);
        }
        100% {
            transform: scale(1) translateY(0);
        }
    }
</style>
@endpush

<div class="w-full px-3 sm:px-4 md:px-6 lg:px-8 max-w-7xl mx-auto mt-2 sm:mt-4 md:mt-6 lg:mt-10">
    {{-- Bannière de bienvenue --}}
    <div class="mb-3 sm:mb-4 md:mb-6 lg:mb-8 p-3 sm:p-4 md:p-6 rounded-xl bg-primary text-white shadow-lg flex flex-col md:flex-row items-center justify-between gap-3 sm:gap-4">
        <div class="text-center md:text-left">
            <h1 class="text-xl sm:text-2xl md:text-3xl font-bold mb-1">
                Cabinet Dermatologie
            </h1>
            <p class="text-primary-light text-sm sm:text-base md:text-lg">
                    {{ is_array(Auth::user()->typeuser) ? (Auth::user()->typeuser['Libelle'] ?? '') : (is_object(Auth::user()->typeuser) ? Auth::user()->typeuser->Libelle : Auth::user()->typeuser) }}
                <span class="font-bold">
                    {{ Auth::user()->NomComplet ?? Auth::user()->name ?? '' }}
                </span>
            </p>
        </div>
        <i class="fas fa-staff-snake text-3xl sm:text-4xl md:text-5xl opacity-30"></i>
    </div>

    {{-- Encadré recherche patient + nouveau patient --}}
    <div class="bg-white rounded-xl shadow p-3 sm:p-4 md:p-6 flex flex-col lg:flex-row items-stretch lg:items-center gap-3 sm:gap-4 md:gap-6 mb-3 sm:mb-4 md:mb-6 lg:mb-8 border border-primary-light">
        <div class="w-full lg:flex-1">
            <livewire:patient-search />
        </div>
        <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 w-full lg:w-auto">
            <button wire:click="openNouveauPatientModal" class="nav-button w-full sm:w-auto sm:min-w-[140px] px-3 sm:px-4 py-2 sm:py-2.5 border-2 border-primary bg-white text-primary rounded-xl shadow-lg hover:bg-primary hover:text-white hover:shadow-xl transition-all duration-300 ease-in-out text-xs sm:text-sm flex items-center justify-center gap-1.5">
                <span class="icon-container inline-flex items-center justify-center rounded-full p-1 sm:p-1.5 bg-white text-primary transition-all duration-300 ease-in-out">
                    <i class="fas fa-user-plus text-primary text-sm sm:text-base transition-all duration-300 ease-in-out"></i>
                </span>
                <span class="font-semibold transition-all duration-300 ease-in-out">Nouveau patient</span>
            </button>
            <button wire:click="openGestionPatientsModal" class="nav-button w-full sm:w-auto sm:min-w-[140px] px-3 sm:px-4 py-2 sm:py-2.5 border-2 border-primary bg-white text-primary rounded-xl shadow-lg hover:bg-primary hover:text-white hover:shadow-xl transition-all duration-300 ease-in-out text-xs sm:text-sm flex items-center justify-center gap-1.5">
                <span class="icon-container inline-flex items-center justify-center rounded-full p-1 sm:p-1.5 bg-white text-primary transition-all duration-300 ease-in-out">
                    <i class="fas fa-users text-primary text-sm sm:text-base transition-all duration-300 ease-in-out"></i>
                </span>
                <span class="font-semibold transition-all duration-300 ease-in-out">Liste de patients</span>
            </button>
            <button wire:click="showCreateRdv" class="nav-button w-full sm:w-auto sm:min-w-[140px] px-3 sm:px-4 py-2 sm:py-2.5 border-2 border-primary bg-white text-primary rounded-xl shadow-lg hover:bg-primary hover:text-white hover:shadow-xl transition-all duration-300 ease-in-out text-xs sm:text-sm flex items-center justify-center gap-1.5">
                <span class="icon-container inline-flex items-center justify-center rounded-full p-1 sm:p-1.5 bg-white text-primary transition-all duration-300 ease-in-out">
                    <i class="fas fa-calendar-plus text-primary text-sm sm:text-base transition-all duration-300 ease-in-out"></i>
                </span>
                <span class="font-semibold transition-all duration-300 ease-in-out">Gestion RDV</span>
            </button>
        </div>
    </div>

    @if($isDocteurProprietaire || $isDocteur || $isSecretaire)
        <div class="flex flex-wrap gap-2 sm:gap-3 md:gap-4 mb-3 sm:mb-4 md:mb-6 lg:mb-8 bg-gray-50 z-10 py-2 sm:py-3 md:py-4 justify-center items-center rounded-lg">
            {{-- Gestion du patient (bouton principal) --}}
            <button
                wire:click="togglePatientMenu"
                @if(!$selectedPatient) disabled title="Veuillez sélectionner un patient d'abord" @endif
                class="nav-button flex items-center gap-2 sm:gap-3 px-4 sm:px-5 md:px-6 py-2.5 sm:py-3 md:py-3.5 w-full sm:w-auto sm:min-w-[180px] md:min-w-[200px] lg:min-w-[220px] border-2 rounded-xl shadow-lg hover:shadow-xl transition-all duration-500 ease-out text-sm sm:text-base md:text-lg justify-center transform hover:scale-105 active:scale-95 ripple menu-button
                {{ $showPatientMenu ? 'bg-primary text-white border-primary shadow-xl scale-105 active' : (!$selectedPatient ? 'bg-gray-100 text-gray-400 border-gray-200 cursor-not-allowed hover:scale-100' : 'bg-white text-primary border-primary hover:bg-primary hover:text-white') }}">
                <span class="icon-container inline-flex items-center justify-center rounded-full p-1.5 sm:p-2 md:p-2.5 transition-all duration-500 ease-out {{ $showPatientMenu ? 'bg-white text-primary rotate-12' : 'bg-white text-primary' }}">
                    <i class="fas fa-user-friends text-primary text-lg sm:text-xl md:text-2xl transition-all duration-500 ease-out {{ $showPatientMenu ? 'text-primary' : 'text-primary' }}"></i>
                </span>
                <span class="font-semibold transition-all duration-500 ease-out">Gestion du patient</span>
            </button>
            {{-- Caisse Paie --}}
            <button wire:click="showCaisseOperations"
                class="nav-button flex items-center gap-2 sm:gap-3 px-4 sm:px-5 md:px-6 py-2.5 sm:py-3 md:py-3.5 w-full sm:w-auto sm:min-w-[180px] md:min-w-[200px] lg:min-w-[220px] border-2 border-primary bg-white text-primary rounded-xl shadow-lg hover:bg-primary hover:text-white hover:shadow-xl hover:scale-105 transition-all duration-500 ease-out text-sm sm:text-base md:text-lg justify-center transform active:scale-95">
                <span class="icon-container inline-flex items-center justify-center rounded-full p-1.5 sm:p-2 md:p-2.5 bg-white text-primary transition-all duration-500 ease-out">
                    <i class="fas fa-cash-register text-primary text-lg sm:text-xl md:text-2xl transition-all duration-500 ease-out"></i>
                </span>
                <span class="font-semibold transition-all duration-500 ease-out">Caisse Paie</span>
            </button>
            {{-- Dépenses --}}
            @if($isDocteurProprietaire)
            <button wire:click="openDepenses" class="nav-button flex items-center gap-2 sm:gap-3 px-4 sm:px-5 md:px-6 py-2.5 sm:py-3 md:py-3.5 w-full sm:w-auto sm:min-w-[180px] md:min-w-[200px] lg:min-w-[220px] border-2 border-primary bg-white text-primary rounded-xl shadow-lg hover:bg-primary hover:text-white hover:shadow-xl hover:scale-105 transition-all duration-500 ease-out text-sm sm:text-base md:text-lg justify-center transform active:scale-95">
                <span class="icon-container inline-flex items-center justify-center rounded-full p-1.5 sm:p-2 md:p-2.5 bg-white text-primary transition-all duration-500 ease-out">
                    <i class="fas fa-receipt text-primary text-lg sm:text-xl md:text-2xl transition-all duration-500 ease-out"></i>
                </span>
                <span class="font-semibold transition-all duration-500 ease-out">Dépenses</span>
            </button>
            @endif
            {{-- Statistiques --}}
            @if($isDocteurProprietaire)
            <button wire:click="showStatistiques" class="nav-button flex items-center gap-2 sm:gap-3 px-4 sm:px-5 md:px-6 py-2.5 sm:py-3 md:py-3.5 w-full sm:w-auto sm:min-w-[180px] md:min-w-[200px] lg:min-w-[220px] border-2 border-primary bg-white text-primary rounded-xl shadow-lg hover:bg-primary hover:text-white hover:shadow-xl hover:scale-105 transition-all duration-500 ease-out text-sm sm:text-base md:text-lg justify-center transform active:scale-95">
                <span class="icon-container inline-flex items-center justify-center rounded-full p-1.5 sm:p-2 md:p-2.5 bg-white text-primary transition-all duration-500 ease-out">
                    <i class="fas fa-chart-bar text-primary text-lg sm:text-xl md:text-2xl transition-all duration-500 ease-out"></i>
                </span>
                <span class="font-semibold transition-all duration-500 ease-out">Statistiques</span>
            </button>
            @endif
            {{-- Gestion Stock --}}
            <button wire:click="showPharmacie"
                class="nav-button flex items-center gap-2 sm:gap-3 px-4 sm:px-5 md:px-6 py-2.5 sm:py-3 md:py-3.5 w-full sm:w-auto sm:min-w-[180px] md:min-w-[200px] lg:min-w-[220px] border-2 border-primary bg-white text-primary rounded-xl shadow-lg hover:bg-primary hover:text-white hover:shadow-xl hover:scale-105 transition-all duration-500 ease-out text-sm sm:text-base md:text-lg justify-center transform active:scale-95">
                <span class="icon-container inline-flex items-center justify-center rounded-full p-1.5 sm:p-2 md:p-2.5 bg-white text-primary transition-all duration-500 ease-out">
                    <i class="fas fa-prescription-bottle text-primary text-lg sm:text-xl md:text-2xl transition-all duration-500 ease-out"></i>
                </span>
                <span class="font-semibold transition-all duration-500 ease-out">Gestion Stock</span>
            </button>
            {{-- Gestion du cabinet (bouton principal) --}}
            <button wire:click="toggleCabinetMenu"
                class="nav-button flex items-center gap-2 sm:gap-3 px-4 sm:px-5 md:px-6 py-2.5 sm:py-3 md:py-3.5 w-full sm:w-auto sm:min-w-[180px] md:min-w-[200px] lg:min-w-[220px] border-2 rounded-xl shadow-lg hover:shadow-xl transition-all duration-500 ease-out text-sm sm:text-base md:text-lg justify-center transform hover:scale-105 active:scale-95 ripple menu-button
                {{ $showCabinetMenu ? 'bg-primary text-white border-primary shadow-xl scale-105 active' : 'bg-white text-primary border-primary hover:bg-primary hover:text-white' }}">
                <span class="icon-container inline-flex items-center justify-center rounded-full p-1.5 sm:p-2 md:p-2.5 transition-all duration-500 ease-out {{ $showCabinetMenu ? 'bg-white text-primary rotate-12' : 'bg-white text-primary' }}">
                    <i class="fas fa-cogs text-primary text-lg sm:text-xl md:text-2xl transition-all duration-500 ease-out {{ $showCabinetMenu ? 'text-primary' : 'text-primary' }}"></i>
                </span>
                <span class="font-semibold transition-all duration-500 ease-out">Gestion du cabinet</span>
            </button>
        </div>

        {{-- Conteneur du sous-menu Gestion du patient avec transitions fluides - Visible seulement après clic sur le bouton --}}
        @if($selectedPatient && $showPatientMenu)
        <div class="patient-menu-container patient-management-section {{ $showPatientMenu ? 'expanded' : '' }}">
            <div class="patient-submenu w-full flex flex-wrap gap-2 sm:gap-3 md:gap-4 justify-center items-center mt-2 show" 
                 data-menu="patient">
                {{-- Consultation - Même logique que les sections principales --}}
                <button wire:click="showConsultation"
                    class="nav-button patient-nav-button flex items-center gap-2 sm:gap-3 px-4 sm:px-5 md:px-6 py-2.5 sm:py-3 md:py-3.5 w-full sm:w-auto sm:min-w-[180px] md:min-w-[200px] lg:min-w-[220px] border-2 border-primary bg-white text-primary rounded-xl shadow-lg hover:bg-primary hover:text-white hover:shadow-xl hover:scale-105 transition-all duration-500 ease-out text-sm sm:text-base md:text-lg justify-center transform active:scale-95 {{ !$selectedPatient ? 'bg-gray-100 text-gray-400 border-gray-200 cursor-not-allowed hover:scale-100' : '' }}">
                    <span class="icon-container inline-flex items-center justify-center rounded-full p-1.5 sm:p-2 md:p-2.5 bg-white text-primary transition-all duration-500 ease-out">
                     <i class="fas fa-stethoscope text-primary text-lg sm:text-xl md:text-2xl transition-all duration-500 ease-out"></i>
                 </span>
                 <span class="font-semibold transition-all duration-500 ease-out">Consultation</span>
             </button>
                {{-- Facture/Devis - Même logique que les sections principales --}}
                <button wire:click="showReglement"
                    class="nav-button patient-nav-button flex items-center gap-2 sm:gap-3 px-4 sm:px-5 md:px-6 py-2.5 sm:py-3 md:py-3.5 w-full sm:w-auto sm:min-w-[180px] md:min-w-[200px] lg:min-w-[220px] border-2 border-primary bg-white text-primary rounded-xl shadow-lg hover:bg-primary hover:text-white hover:shadow-xl hover:scale-105 transition-all duration-500 ease-out text-sm sm:text-base md:text-lg justify-center transform active:scale-95 {{ !$selectedPatient ? 'bg-gray-100 text-gray-400 border-gray-200 cursor-not-allowed hover:scale-100' : '' }}">
                    <span class="icon-container inline-flex items-center justify-center rounded-full p-1.5 sm:p-2 md:p-2.5 bg-white text-primary transition-all duration-500 ease-out">
                     <i class="fas fa-file-invoice-dollar text-primary text-lg sm:text-xl md:text-2xl transition-all duration-500 ease-out"></i>
                 </span>
                 <span class="font-semibold transition-all duration-500 ease-out">Facture/Devis</span>
             </button>
                {{-- Rendez-vous - Même logique que les sections principales --}}
                <button wire:click="showRendezVous"
                    class="nav-button patient-nav-button flex items-center gap-2 sm:gap-3 px-4 sm:px-5 md:px-6 py-2.5 sm:py-3 md:py-3.5 w-full sm:w-auto sm:min-w-[180px] md:min-w-[200px] lg:min-w-[220px] border-2 border-primary bg-white text-primary rounded-xl shadow-lg hover:bg-primary hover:text-white hover:shadow-xl hover:scale-105 transition-all duration-500 ease-out text-sm sm:text-base md:text-lg justify-center transform active:scale-95 {{ !$selectedPatient ? 'bg-gray-100 text-gray-400 border-gray-200 cursor-not-allowed hover:scale-100' : '' }}">
                    <span class="icon-container inline-flex items-center justify-center rounded-full p-1.5 sm:p-2 md:p-2.5 bg-white text-primary transition-all duration-500 ease-out">
                     <i class="fas fa-calendar-check text-primary text-lg sm:text-xl md:text-2xl transition-all duration-500 ease-out"></i>
                 </span>
                 <span class="font-semibold transition-all duration-500 ease-out">Rendez-vous</span>
             </button>
                {{-- Ordonnances --}}
                @if(!$isSecretaire)
                <button wire:click="ouvrirOrdonnanceModal"
                    class="nav-button patient-nav-button flex items-center gap-2 sm:gap-3 px-4 sm:px-5 md:px-6 py-2.5 sm:py-3 md:py-3.5 w-full sm:w-auto sm:min-w-[180px] md:min-w-[200px] lg:min-w-[220px] border-2 border-primary bg-white text-primary rounded-xl shadow-lg hover:bg-primary hover:text-white hover:shadow-xl hover:scale-105 transition-all duration-500 ease-out text-sm sm:text-base md:text-lg justify-center transform active:scale-95 {{ !$selectedPatient ? 'bg-gray-100 text-gray-400 border-gray-200 cursor-not-allowed hover:scale-100' : '' }}"
                    @if(!$selectedPatient) disabled @endif
                    type="button">
                    <span class="icon-container inline-flex items-center justify-center rounded-full p-1.5 sm:p-2 md:p-2.5 bg-white text-primary transition-all duration-500 ease-out">
                     <i class="fas fa-file-prescription text-primary text-lg sm:text-xl md:text-2xl transition-all duration-500 ease-out"></i>
                 </span>
                 <span class="font-semibold transition-all duration-500 ease-out">Ordonnances</span>
             </button>
             @endif
                {{-- Vente Pharmacie --}}
                <button wire:click="showVentePharmacie"
                    class="nav-button patient-nav-button flex items-center gap-2 sm:gap-3 px-4 sm:px-5 md:px-6 py-2.5 sm:py-3 md:py-3.5 w-full sm:w-auto sm:min-w-[180px] md:min-w-[200px] lg:min-w-[220px] border-2 border-primary bg-white text-primary rounded-xl shadow-lg hover:bg-primary hover:text-white hover:shadow-xl hover:scale-105 transition-all duration-500 ease-out text-sm sm:text-base md:text-lg justify-center transform active:scale-95 {{ !$selectedPatient ? 'bg-gray-100 text-gray-400 border-gray-200 cursor-not-allowed hover:scale-100' : '' }}"
                    @if(!$selectedPatient) disabled @endif
                    type="button">
                    <span class="icon-container inline-flex items-center justify-center rounded-full p-1.5 sm:p-2 md:p-2.5 bg-white text-primary transition-all duration-500 ease-out">
                     <i class="fas fa-shopping-cart text-primary text-lg sm:text-xl md:text-2xl transition-all duration-500 ease-out"></i>
                 </span>
                 <span class="font-semibold transition-all duration-500 ease-out">Vente Pharmacie</span>
             </button>
            </div>
         </div>
         @endif

    @endif

    {{-- Modal création patient --}}
    @if($showCreateModal)
        <div class="overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full bg-black bg-opacity-50">
            <div class="relative p-4 w-full max-w-6xl max-h-full">
                <!-- Modal content -->
                <div class="relative bg-white rounded-lg shadow-sm">
                    <!-- Modal header -->
                    <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-gray-200">
                        <h3 class="text-xl font-semibold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-users text-blue-600"></i>
                            <span>Gestion des patients</span>
                        </h3>
                        <button type="button" 
                                wire:click="closeCreateModal"
                                class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                            </svg>
                            <span class="sr-only">Fermer</span>
                    </button>
                </div>
                    <!-- Modal body -->
                    <div class="p-4 md:p-5 space-y-4 max-h-[calc(100vh-200px)] overflow-y-auto">
                        <livewire:patient-manager />
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal création RDV pour tous patients --}}
    @if($showCreateRdvModal)
        <div class="overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full bg-black bg-opacity-50">
            <div class="relative p-4 w-full max-w-7xl max-h-full">
                <!-- Modal content -->
                <div class="relative bg-white rounded-lg shadow-sm">
                    <!-- Modal header -->
                    <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-gray-200">
                        <h3 class="text-xl font-semibold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-calendar-alt text-blue-600"></i>
                            <span>Gestion des Rendez-vous</span>
                        </h3>
                        <button type="button" 
                                wire:click="closeCreateRdvModal"
                                class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                            </svg>
                            <span class="sr-only">Fermer</span>
                    </button>
                </div>
                
                <!-- Onglets -->
                    <div class="border-b border-gray-200">
                    <nav class="flex space-x-4 sm:space-x-8 px-4 sm:px-6 overflow-x-auto" aria-label="Tabs">
                        <button wire:click="$set('activeRdvTab', 'create')" 
                                class="py-3 sm:py-4 px-1 border-b-2 font-medium text-xs sm:text-sm whitespace-nowrap {{ $activeRdvTab === 'create' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                                <i class="fas fa-plus mr-1 sm:mr-2"></i>
                            <span class="hidden xs:inline">Gestion RDV</span>
                            <span class="xs:hidden">RDV</span>
                        </button>
                        <button wire:click="$set('activeRdvTab', 'reminders')" 
                                class="py-3 sm:py-4 px-1 border-b-2 font-medium text-xs sm:text-sm whitespace-nowrap {{ $activeRdvTab === 'reminders' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} relative">
                                <i class="fas fa-bell mr-1 sm:mr-2"></i>
                            <span class="hidden xs:inline">Rappels RDV</span>
                            <span class="xs:hidden">Rappels</span>
                            @if($rdvRemindersCount > 0)
                                <span class="ml-1 sm:ml-2 inline-flex items-center justify-center px-1.5 sm:px-2 py-0.5 sm:py-1 text-xs font-bold leading-none text-white bg-red-500 rounded-full">
                                    {{ $rdvRemindersCount }}
                                </span>
                            @endif
                        </button>
                    </nav>
                </div>
                
                    <!-- Modal body -->
                    <div class="p-4 md:p-5 space-y-4 max-h-[calc(100vh-200px)] overflow-y-auto">
                        <div id="modal-loading" class="hidden flex items-center justify-center py-6 sm:py-8">
                            <div class="animate-spin rounded-full h-6 sm:h-8 w-6 sm:w-8 border-b-2 border-primary"></div>
                            <span class="ml-2 sm:ml-3 text-sm sm:text-base text-gray-600">Chargement...</span>
                        </div>
                        
                        <div class="tab-content">
                            @if($activeRdvTab === 'create')
                                <div class="tab-panel showing animate-speed-fade-in" wire:key="create-rdv-panel" style="animation-delay: 0.1s;">
                                    <livewire:create-rendez-vous wire:key="create-rdv-modal" :patient="$selectedPatient" />
                                </div>
                            @elseif($activeRdvTab === 'reminders')
                                <div class="tab-panel showing animate-speed-fade-in" wire:key="reminders-panel" style="animation-delay: 0.1s;">
                                    <livewire:rdv-reminders wire:key="rdv-reminders-modal" />
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Nouveau Patient (création uniquement) --}}
    @if($showNouveauPatientModal)
        <div class="overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full bg-black bg-opacity-50">
            <div class="relative p-4 w-full max-w-4xl max-h-full">
                <!-- Modal content -->
                <div class="relative bg-white rounded-lg shadow-sm">
                    <!-- Modal header -->
                    <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-gray-200">
                        <h3 class="text-xl font-semibold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-user-plus text-blue-600"></i>
                            <span>Nouveau patient</span>
                        </h3>
                        <button type="button" 
                                wire:click="closeNouveauPatientModal"
                                class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                            </svg>
                            <span class="sr-only">Fermer</span>
                        </button>
                    </div>
                    <!-- Modal body -->
                    <div class="p-4 md:p-5 space-y-4 max-h-[calc(100vh-200px)] overflow-y-auto">
                        <livewire:patient-manager wire:key="nouveau-patient-modal" :creationOnly="true" />
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($showGestionPatientsModal)
        <div class="overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full bg-black bg-opacity-50">
            <div class="relative p-4 w-full max-w-7xl max-h-full">
                <!-- Modal content -->
                <div class="relative bg-white rounded-lg shadow-sm">
                    <!-- Modal header -->
                    <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-gray-200">
                        <h3 class="text-xl font-semibold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-users text-blue-600"></i>
                            <span>Gestion des patients</span>
                        </h3>
                        <button type="button" 
                                wire:click="closeGestionPatientsModal"
                                class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                            </svg>
                            <span class="sr-only">Fermer</span>
                    </button>
                </div>
                    <!-- Modal body -->
                    <div class="p-4 md:p-5 space-y-4 max-h-[calc(100vh-200px)] overflow-y-auto">
                        <livewire:patient-manager />
                    </div>
                </div>
            </div>
        </div>
    @endif



        <!-- Notifications pour les consultations -->
        @if (session()->has('success'))
            <div x-data="{ show: true }" 
                 x-show="show" 
                 x-init="setTimeout(() => show = false, 5000)"
                 class="fixed bottom-4 right-4 bg-primary-light border border-primary text-primary px-4 py-3 rounded shadow-lg z-50" 
                 role="alert">
                <div class="flex items-center">
                    <div class="py-1">
                        <svg class="h-5 w-5 text-primary mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                    <button type="button" @click="show = false" class="ml-4 text-primary hover:text-primary-dark">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        @if (session()->has('error'))
            <div x-data="{ show: true }" 
                 x-show="show" 
                 x-init="setTimeout(() => show = false, 5000)"
                 class="fixed bottom-4 right-4 bg-primary-light border border-primary text-primary px-4 py-3 rounded shadow-lg z-50" 
                 role="alert">
                <div class="flex items-center">
                    <div class="py-1">
                        <svg class="h-5 w-5 text-primary mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                    <button type="button" @click="show = false" class="ml-4 text-primary hover:text-primary-dark">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        @endif


        <!-- Composant HistoriquePaiement toujours présent -->
        <livewire:historique-paiement wire:key="historique-paiement" lazy />

    {{-- Modaux harmonisés pour la Gestion du Cabinet --}}
    
    {{-- Modal Gestion des assurances --}}
    @if($showAssureurModal)
        <div class="overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full bg-black bg-opacity-50">
            <div class="relative p-4 w-full max-w-6xl max-h-full">
                <!-- Modal content -->
                <div class="relative bg-white rounded-lg shadow-sm">
                    <!-- Modal header -->
                    <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-gray-200">
                        <h3 class="text-xl font-semibold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-shield-alt text-blue-600"></i>
                            <span>Gestion des assurances</span>
                        </h3>
                        <button type="button" 
                                wire:click="fermerAssureurModal"
                                class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                            </svg>
                            <span class="sr-only">Fermer</span>
                    </button>
                </div>
                    <!-- Modal body -->
                    <div class="p-4 md:p-5 space-y-4 max-h-[calc(100vh-200px)] overflow-y-auto">
                        <livewire:assureur-manager wire:key="assureur-manager-modal" />
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Liste des actes --}}
    @if($showListeActesModal)
        <div class="overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full bg-black bg-opacity-50">
            <div class="relative p-4 w-full max-w-6xl max-h-full">
                <!-- Modal content -->
                <div class="relative bg-white rounded-lg shadow-sm">
                    <!-- Modal header -->
                    <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-gray-200">
                        <h3 class="text-xl font-semibold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-list-alt text-blue-600"></i>
                            <span>Liste des actes</span>
                        </h3>
                        <button type="button" 
                                wire:click="fermerListeActesModal"
                                class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                            </svg>
                            <span class="sr-only">Fermer</span>
                    </button>
                </div>
                    <!-- Modal body -->
                    <div class="p-4 md:p-5 space-y-4 max-h-[calc(100vh-200px)] overflow-y-auto">
                        <livewire:acte-manager wire:key="acte-manager-modal" />
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Liste des médicaments --}}
    @if($showListeMedicamentsModal)
        <div class="overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full bg-black bg-opacity-50">
            <div class="relative p-4 w-full max-w-6xl max-h-full">
                <!-- Modal content -->
                <div class="relative bg-white rounded-lg shadow-sm">
                    <!-- Modal header -->
                    <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-gray-200">
                        <h3 class="text-xl font-semibold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-pills text-blue-600"></i>
                            <span>Liste des médicaments</span>
                        </h3>
                        <button type="button" 
                                wire:click="fermerListeMedicamentsModal"
                                class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                            </svg>
                            <span class="sr-only">Fermer</span>
                    </button>
                </div>
                    <!-- Modal body -->
                    <div class="p-4 md:p-5 space-y-4 max-h-[calc(100vh-200px)] overflow-y-auto">
                        <livewire:medicament-manager wire:key="medicament-manager-modal" />
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Gestion des médecins --}}
    @if($showMedecinsModal)
        <div class="overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full bg-black bg-opacity-50">
            <div class="relative p-4 w-full max-w-6xl max-h-full">
                <!-- Modal content -->
                <div class="relative bg-white rounded-lg shadow-sm">
                    <!-- Modal header -->
                    <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-gray-200">
                        <h3 class="text-xl font-semibold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-user-md text-blue-600"></i>
                            <span>Gestion des médecins</span>
                        </h3>
                        <button type="button" 
                                wire:click="fermerMedecinsModal"
                                class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                            </svg>
                            <span class="sr-only">Fermer</span>
                    </button>
                </div>
                    <!-- Modal body -->
                    <div class="p-4 md:p-5 space-y-4 max-h-[calc(100vh-200px)] overflow-y-auto">
                        <livewire:medecin-manager wire:key="medecin-manager-modal" />
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Modes de paiement --}}
    @if($showTypePaiementModal)
        <div class="overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full bg-black bg-opacity-50">
            <div class="relative p-4 w-full max-w-5xl max-h-full">
                <!-- Modal content -->
                <div class="relative bg-white rounded-lg shadow-sm">
                    <!-- Modal header -->
                    <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-gray-200">
                        <h3 class="text-xl font-semibold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-credit-card text-blue-600"></i>
                            <span>Modes de paiement</span>
                        </h3>
                        <button type="button" 
                                wire:click="fermerTypePaiementModal"
                                class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                            </svg>
                            <span class="sr-only">Fermer</span>
                    </button>
                </div>
                    <!-- Modal body -->
                    <div class="p-4 md:p-5 space-y-4 max-h-[calc(100vh-200px)] overflow-y-auto">
                        <livewire:type-paiement-manager wire:key="type-paiement-manager-modal" />
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Gestion des utilisateurs --}}
    @if($showUsersModal)
        <div class="overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full bg-black bg-opacity-50">
            <div class="relative p-4 w-full max-w-6xl max-h-full">
                <!-- Modal content -->
                <div class="relative bg-white rounded-lg shadow-sm">
                    <!-- Modal header -->
                    <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-gray-200">
                        <h3 class="text-xl font-semibold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-users-cog text-blue-600"></i>
                            <span>Gestion des utilisateurs</span>
                        </h3>
                        <button type="button" 
                                wire:click="closeUsersModal"
                                class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                            </svg>
                            <span class="sr-only">Fermer</span>
                    </button>
                </div>
                    <!-- Modal body -->
                    <div class="p-4 md:p-5 space-y-4 max-h-[calc(100vh-200px)] overflow-y-auto">
                        <livewire:user-manager wire:key="user-manager-modal" />
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Ordonnances --}}
    @if($showOrdonnanceModal && $selectedPatient)
        <div class="overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full bg-black bg-opacity-50">
            <div class="relative p-4 w-full max-w-6xl max-h-full">
                <!-- Modal content -->
                <div class="relative bg-white rounded-lg shadow-sm">
                    <!-- Modal header -->
                    <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-gray-200">
                        <h3 class="text-xl font-semibold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-file-prescription text-primary"></i>
                            <span>Gestion des Ordonnances</span>
                            @if($selectedPatient)
                            <span class="text-sm font-normal text-gray-600 ml-2">
                                - {{ is_array($selectedPatient) ? ($selectedPatient['NomPatient'] ?? $selectedPatient['Nom'] ?? 'Patient') : ($selectedPatient->NomPatient ?? $selectedPatient->Nom ?? 'Patient') }}
                            </span>
                            @endif
                        </h3>
                        <button type="button" 
                                wire:click="fermerOrdonnanceModal"
                                class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                            </svg>
                            <span class="sr-only">Fermer</span>
                        </button>
                    </div>
                    <!-- Modal body -->
                    <div class="p-4 md:p-5 space-y-4 max-h-[calc(100vh-200px)] overflow-y-auto">
                        <livewire:ordonnance-manager wire:key="ordonnance-manager-modal-{{ is_array($selectedPatient) ? ($selectedPatient['ID'] ?? '') : ($selectedPatient->ID ?? '') }}" :patient="$selectedPatient" />
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Consultation --}}
    @if($showConsultation && $selectedPatient)
        <div class="overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full bg-black bg-opacity-50">
            <div class="relative p-4 w-full max-w-6xl max-h-full">
                <!-- Modal content -->
                <div class="relative bg-white rounded-lg shadow-sm">
                    <!-- Modal header -->
                    <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-gray-200">
                        <h3 class="text-xl font-semibold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-stethoscope text-primary"></i>
                            <span>Consultation</span>
                            @if($selectedPatient)
                            <span class="text-sm font-normal text-gray-600 ml-2">
                                - {{ is_array($selectedPatient) ? ($selectedPatient['NomPatient'] ?? $selectedPatient['Nom'] ?? 'Patient') : ($selectedPatient->NomPatient ?? $selectedPatient->Nom ?? 'Patient') }}
                            </span>
                            @endif
                        </h3>
                        <button type="button" 
                                wire:click="fermerConsultationModal"
                                class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                            </svg>
                            <span class="sr-only">Fermer</span>
                        </button>
                    </div>
                    <!-- Modal body -->
                    <div class="p-4 md:p-5 space-y-4 max-h-[calc(100vh-200px)] overflow-y-auto">
                        <livewire:consultation-form wire:key="consultation-modal-{{ is_array($selectedPatient) ? ($selectedPatient['ID'] ?? '') : ($selectedPatient->ID ?? '') }}" :patient="$selectedPatient" />
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Facture/Devis --}}
    @if($showReglement && $selectedPatient)
        <div class="overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full bg-black bg-opacity-50">
            <div class="relative p-4 w-full max-w-6xl max-h-full">
                <!-- Modal content -->
                <div class="relative bg-white rounded-lg shadow-sm">
                    <!-- Modal header -->
                    <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-gray-200">
                        <h3 class="text-xl font-semibold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-file-invoice-dollar text-primary"></i>
                            <span>Facture/Devis</span>
                            @if($selectedPatient)
                            <span class="text-sm font-normal text-gray-600 ml-2">
                                - {{ is_array($selectedPatient) ? ($selectedPatient['NomPatient'] ?? $selectedPatient['Nom'] ?? 'Patient') : ($selectedPatient->NomPatient ?? $selectedPatient->Nom ?? 'Patient') }}
                            </span>
                            @endif
                        </h3>
                        <button type="button" 
                                wire:click="fermerReglementModal"
                                class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                            </svg>
                            <span class="sr-only">Fermer</span>
                        </button>
                    </div>
                    <!-- Modal body -->
                    <div class="p-4 md:p-5 space-y-4 max-h-[calc(100vh-200px)] overflow-y-auto">
                        <livewire:reglement-facture wire:key="reglement-modal-{{ is_array($selectedPatient) ? ($selectedPatient['ID'] ?? '') : ($selectedPatient->ID ?? '') }}" :selectedPatient="$selectedPatient" lazy />
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Rendez-vous --}}
    @if($showRendezVous && $selectedPatient)
        <div class="overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full bg-black bg-opacity-50">
            <div class="relative p-4 w-full max-w-6xl max-h-full">
                <!-- Modal content -->
                <div class="relative bg-white rounded-lg shadow-sm">
                    <!-- Modal header -->
                    <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-gray-200">
                        <h3 class="text-xl font-semibold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-calendar-check text-primary"></i>
                            <span>Rendez-vous</span>
                            @if($selectedPatient)
                            <span class="text-sm font-normal text-gray-600 ml-2">
                                - {{ is_array($selectedPatient) ? ($selectedPatient['NomPatient'] ?? $selectedPatient['Nom'] ?? 'Patient') : ($selectedPatient->NomPatient ?? $selectedPatient->Nom ?? 'Patient') }}
                            </span>
                            @endif
                        </h3>
                        <button type="button" 
                                wire:click="fermerRendezVousModal"
                                class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                            </svg>
                            <span class="sr-only">Fermer</span>
                        </button>
                    </div>
                    <!-- Modal body -->
                    <div class="p-4 md:p-5 space-y-4 max-h-[calc(100vh-200px)] overflow-y-auto">
                        <livewire:create-rendez-vous wire:key="rendez-vous-modal-{{ is_array($selectedPatient) ? ($selectedPatient['ID'] ?? '') : ($selectedPatient->ID ?? '') }}" :patient="$selectedPatient" />
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Caisse Paie --}}
    @if($showCaisseOperations)
        <div class="overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full bg-black bg-opacity-50">
            <div class="relative p-4 w-full max-w-6xl max-h-full">
                <!-- Modal content -->
                <div class="relative bg-white rounded-lg shadow-sm">
                    <!-- Modal header -->
                    <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-gray-200">
                        <h3 class="text-xl font-semibold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-cash-register text-primary"></i>
                            <span>Caisse Paie</span>
                        </h3>
                        <button type="button" 
                                wire:click="fermerCaisseOperationsModal"
                                class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                            </svg>
                            <span class="sr-only">Fermer</span>
                        </button>
                    </div>
                    <!-- Modal body -->
                    <div class="p-4 md:p-5 space-y-4 max-h-[calc(100vh-200px)] overflow-y-auto">
                        <livewire:caisse-operations-manager wire:key="caisse-operations-modal" />
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Dépenses --}}
    @if($showDepenses)
        <div class="overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full bg-black bg-opacity-50">
            <div class="relative p-4 w-full max-w-6xl max-h-full">
                <!-- Modal content -->
                <div class="relative bg-white rounded-lg shadow-sm">
                    <!-- Modal header -->
                    <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-gray-200">
                        <h3 class="text-xl font-semibold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-receipt text-primary"></i>
                            <span>Dépenses</span>
                        </h3>
                        <button type="button" 
                                wire:click="fermerDepensesModal"
                                class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                            </svg>
                            <span class="sr-only">Fermer</span>
                        </button>
                    </div>
                    <!-- Modal body -->
                    <div class="p-4 md:p-5 space-y-4 max-h-[calc(100vh-200px)] overflow-y-auto">
                        <livewire:depenses-manager wire:key="depenses-manager-modal" />
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Statistiques --}}
    @if($isDocteurProprietaire && $showStatistiques)
        <div class="overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full bg-black bg-opacity-50">
            <div class="relative p-4 w-full max-w-6xl max-h-full">
                <!-- Modal content -->
                <div class="relative bg-white rounded-lg shadow-sm">
                    <!-- Modal header -->
                    <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-gray-200">
                        <h3 class="text-xl font-semibold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-chart-bar text-primary"></i>
                            <span>Statistiques</span>
                        </h3>
                        <button type="button" 
                                wire:click="fermerStatistiquesModal"
                                class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                            </svg>
                            <span class="sr-only">Fermer</span>
                        </button>
                    </div>
                    <!-- Modal body -->
                    <div class="p-4 md:p-5 space-y-4 max-h-[calc(100vh-200px)] overflow-y-auto">
                        <livewire:statistiques-manager wire:key="statistiques-manager-modal" />
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Pharmacie (Gestion du stock) --}}
    @if($showPharmacie)
        <div class="overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full bg-black bg-opacity-50">
            <div class="relative p-4 w-full max-w-7xl max-h-full">
                <!-- Modal content -->
                <div class="relative bg-white rounded-lg shadow-sm">
                    <!-- Modal header -->
                    <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-gray-200">
                        <h3 class="text-xl font-semibold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-prescription-bottle text-primary"></i>
                            <span>Gestion du Stock - Pharmacie</span>
                        </h3>
                        <button type="button" 
                                wire:click="fermerPharmacieModal"
                                class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                            </svg>
                            <span class="sr-only">Fermer</span>
                        </button>
                    </div>
                    <!-- Modal body -->
                    <div class="p-4 md:p-5 space-y-4 max-h-[calc(100vh-200px)] overflow-y-auto">
                        <livewire:pharmacie-manager wire:key="pharmacie-manager-modal" />
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Vente Pharmacie (depuis Gestion du patient) --}}
    @if($showVentePharmacie && $selectedPatient)
        <div class="overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full bg-black bg-opacity-50">
            <div class="relative p-4 w-full max-w-7xl max-h-full">
                <!-- Modal content -->
                <div class="relative bg-white rounded-lg shadow-sm">
                    <!-- Modal header -->
                    <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-gray-200">
                        <h3 class="text-xl font-semibold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-shopping-cart text-primary"></i>
                            <span>Vente Pharmacie</span>
                            @if($selectedPatient)
                            <span class="text-sm font-normal text-gray-600 ml-2">
                                - {{ is_array($selectedPatient) ? ($selectedPatient['NomPatient'] ?? $selectedPatient['Nom'] ?? 'Patient') : ($selectedPatient->NomPatient ?? $selectedPatient->Nom ?? 'Patient') }}
                            </span>
                            @endif
                        </h3>
                        <button type="button" 
                                wire:click="fermerVentePharmacieModal"
                                class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                            </svg>
                            <span class="sr-only">Fermer</span>
                        </button>
                    </div>
                    <!-- Modal body -->
                    <div class="p-4 md:p-5 space-y-4 max-h-[calc(100vh-200px)] overflow-y-auto">
                        <livewire:pharmacie-manager wire:key="vente-pharmacie-modal-{{ is_array($selectedPatient) ? ($selectedPatient['ID'] ?? '') : ($selectedPatient->ID ?? '') }}" :patientId="is_array($selectedPatient) ? ($selectedPatient['ID'] ?? null) : ($selectedPatient->ID ?? null)" :venteOnly="true" />
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Sous-menu Gestion du cabinet --}}
    @if($showCabinetMenu)
    <div class="w-full flex flex-wrap gap-2 sm:gap-3 md:gap-4 justify-center items-center mt-2 transition-all duration-700 ease-out transform {{ $showCabinetMenu ? 'opacity-100 translate-y-0 scale-100' : 'opacity-0 -translate-y-4 scale-95' }}" 
         style="animation: slideInDown 0.7s ease-out;" data-menu="cabinet">
        
        {{-- Gestion des assurances --}}
        <button wire:click="ouvrirAssureurModal"
            class="nav-button flex items-center gap-2 sm:gap-3 px-4 sm:px-5 md:px-6 py-2.5 sm:py-3 md:py-3.5 w-full sm:w-auto sm:min-w-[180px] md:min-w-[200px] lg:min-w-[220px] border-2 border-primary bg-white text-primary rounded-xl shadow-lg hover:bg-primary hover:text-white hover:shadow-xl hover:scale-105 transition-all duration-500 ease-out text-sm sm:text-base md:text-lg justify-center transform active:scale-95 touch-friendly">
            <span class="icon-container inline-flex items-center justify-center rounded-full p-1.5 sm:p-2 md:p-2.5 bg-white text-primary transition-all duration-500 ease-out">
                <i class="fas fa-shield-alt text-primary text-lg sm:text-xl md:text-2xl transition-all duration-500 ease-out"></i>
            </span>
            <span class="font-semibold transition-all duration-500 ease-out">
                <span class="hidden sm:inline">Assurances</span>
                <span class="sm:hidden">Assur.</span>
            </span>
        </button>

        {{-- Liste des actes/soins --}}
        <button wire:click="ouvrirListeActesModal"
            class="nav-button flex items-center gap-2 sm:gap-3 px-4 sm:px-5 md:px-6 py-2.5 sm:py-3 md:py-3.5 w-full sm:w-auto sm:min-w-[180px] md:min-w-[200px] lg:min-w-[220px] border-2 border-primary bg-white text-primary rounded-xl shadow-lg hover:bg-primary hover:text-white hover:shadow-xl hover:scale-105 transition-all duration-500 ease-out text-sm sm:text-base md:text-lg justify-center transform active:scale-95 touch-friendly">
            <span class="icon-container inline-flex items-center justify-center rounded-full p-1.5 sm:p-2 md:p-2.5 bg-white text-primary transition-all duration-500 ease-out">
                <i class="fas fa-list-alt text-primary text-lg sm:text-xl md:text-2xl transition-all duration-500 ease-out"></i>
            </span>
            <span class="font-semibold transition-all duration-500 ease-out">
                <span class="hidden sm:inline">Liste des actes</span>
                <span class="sm:hidden">Actes</span>
            </span>
        </button>

        {{-- Liste des médicaments --}}
        <button wire:click="ouvrirListeMedicamentsModal"
            class="nav-button flex items-center gap-2 sm:gap-3 px-4 sm:px-5 md:px-6 py-2.5 sm:py-3 md:py-3.5 w-full sm:w-auto sm:min-w-[180px] md:min-w-[200px] lg:min-w-[220px] border-2 border-primary bg-white text-primary rounded-xl shadow-lg hover:bg-primary hover:text-white hover:shadow-xl hover:scale-105 transition-all duration-500 ease-out text-sm sm:text-base md:text-lg justify-center transform active:scale-95 touch-friendly">
            <span class="icon-container inline-flex items-center justify-center rounded-full p-1.5 sm:p-2 md:p-2.5 bg-white text-primary transition-all duration-500 ease-out">
                <i class="fas fa-pills text-primary text-lg sm:text-xl md:text-2xl transition-all duration-500 ease-out"></i>
            </span>
            <span class="font-semibold transition-all duration-500 ease-out">
                <span class="hidden sm:inline">Liste des médicaments</span>
                <span class="sm:hidden">Médic.</span>
            </span>
        </button>

        {{-- Gestion des médecins --}}
        <button wire:click="ouvrirMedecinsModal"
            class="nav-button flex items-center gap-2 sm:gap-3 px-4 sm:px-5 md:px-6 py-2.5 sm:py-3 md:py-3.5 w-full sm:w-auto sm:min-w-[180px] md:min-w-[200px] lg:min-w-[220px] border-2 border-primary bg-white text-primary rounded-xl shadow-lg hover:bg-primary hover:text-white hover:shadow-xl hover:scale-105 transition-all duration-500 ease-out text-sm sm:text-base md:text-lg justify-center transform active:scale-95 touch-friendly">
            <span class="icon-container inline-flex items-center justify-center rounded-full p-1.5 sm:p-2 md:p-2.5 bg-white text-primary transition-all duration-500 ease-out">
                <i class="fas fa-user-md text-primary text-lg sm:text-xl md:text-2xl transition-all duration-500 ease-out"></i>
            </span>
            <span class="font-semibold transition-all duration-500 ease-out">
                <span class="hidden sm:inline">Médecins</span>
                <span class="sm:hidden">Méd.</span>
            </span>
        </button>

        {{-- Gestion des types de paiement --}}
        <button wire:click="ouvrirTypePaiementModal"
            class="nav-button flex items-center gap-2 sm:gap-3 px-4 sm:px-5 md:px-6 py-2.5 sm:py-3 md:py-3.5 w-full sm:w-auto sm:min-w-[180px] md:min-w-[200px] lg:min-w-[220px] border-2 border-primary bg-white text-primary rounded-xl shadow-lg hover:bg-primary hover:text-white hover:shadow-xl hover:scale-105 transition-all duration-500 ease-out text-sm sm:text-base md:text-lg justify-center transform active:scale-95 touch-friendly">
            <span class="icon-container inline-flex items-center justify-center rounded-full p-1.5 sm:p-2 md:p-2.5 bg-white text-primary transition-all duration-500 ease-out">
                <i class="fas fa-credit-card text-primary text-lg sm:text-xl md:text-2xl transition-all duration-500 ease-out"></i>
            </span>
            <span class="font-semibold transition-all duration-500 ease-out">
                <span class="hidden sm:inline">Modes de paiement</span>
                <span class="sm:hidden">Paiements</span>
            </span>
        </button>

        {{-- Gestion des utilisateurs (Docteur Propriétaire uniquement) --}}
        @if($isDocteurProprietaire)
        <button wire:click="openUsersModal"
            class="nav-button flex items-center gap-2 sm:gap-3 px-4 sm:px-5 md:px-6 py-2.5 sm:py-3 md:py-3.5 w-full sm:w-auto sm:min-w-[180px] md:min-w-[200px] lg:min-w-[220px] border-2 border-primary bg-white text-primary rounded-xl shadow-lg hover:bg-primary hover:text-white hover:shadow-xl hover:scale-105 transition-all duration-500 ease-out text-sm sm:text-base md:text-lg justify-center transform active:scale-95 touch-friendly">
            <span class="icon-container inline-flex items-center justify-center rounded-full p-1.5 sm:p-2 md:p-2.5 bg-white text-primary transition-all duration-500 ease-out">
                <i class="fas fa-users-cog text-primary text-lg sm:text-xl md:text-2xl transition-all duration-500 ease-out"></i>
            </span>
            <span class="font-semibold transition-all duration-500 ease-out">
                <span class="hidden sm:inline">Utilisateurs</span>
                <span class="sm:hidden">Users</span>
            </span>
        </button>
        @endif
    </div>
    @endif


</div>

@push('scripts')
<script>
    document.addEventListener('livewire:load', function () {
        Livewire.on('openModal', (modalName) => {
            console.log('Opening modal:', modalName);
        });

        // Gestion des animations de menu avec transitions souples
        window.addEventListener('menu-opened', function(event) {
            const menu = event.detail.menu;
            console.log('Menu opened:', menu);
            
            // Ajouter une classe pour l'animation d'entrée avec transition souple
            const menuElement = document.querySelector(`[data-menu="${menu}"]`);
            if (menuElement) {
                menuElement.classList.add('menu-slide-in');
                menuElement.classList.add('gpu-accelerated');
                
                // Ajouter un délai pour l'animation des boutons individuels
                const buttons = menuElement.querySelectorAll('.nav-button');
                buttons.forEach((button, index) => {
                    button.style.opacity = '0';
                    button.style.transform = 'translateY(20px) scale(0.9)';
                    
                    setTimeout(() => {
                        button.style.transition = 'all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
                        button.style.opacity = '1';
                        button.style.transform = 'translateY(0) scale(1)';
                    }, 100 + (index * 50)); // Délai progressif pour chaque bouton
                });
            }
        });

        window.addEventListener('menu-closed', function(event) {
            const menu = event.detail.menu;
            console.log('Menu closed:', menu);
            
            // Animation de sortie avec transition souple
            const menuElement = document.querySelector(`[data-menu="${menu}"]`);
            if (menuElement) {
                const buttons = menuElement.querySelectorAll('.nav-button');
                
                // Animation de sortie des boutons
                buttons.forEach((button, index) => {
                setTimeout(() => {
                        button.style.transition = 'all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
                        button.style.opacity = '0';
                        button.style.transform = 'translateY(-20px) scale(0.9)';
                    }, index * 30); // Délai progressif inverse
                });
                
                // Animation de sortie du menu
                setTimeout(() => {
                    menuElement.classList.add('menu-slide-out');
                    setTimeout(() => {
                        menuElement.classList.remove('menu-slide-out');
                    }, 400);
                }, buttons.length * 30 + 100);
            }
        });

        // Effet de ripple amélioré avec transitions souples
        document.addEventListener('click', function(e) {
            if (e.target.closest('.ripple')) {
                const button = e.target.closest('.ripple');
                const ripple = document.createElement('span');
                const rect = button.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;
                
                ripple.style.width = ripple.style.height = size + 'px';
                ripple.style.left = x + 'px';
                ripple.style.top = y + 'px';
                ripple.classList.add('ripple-effect');
                
                button.appendChild(ripple);
                
                setTimeout(() => {
                    ripple.remove();
                }, 600);
            }
        });

        // Transitions souples pour les changements d'état des boutons
        document.addEventListener('livewire:load', function() {
            // Observer les changements d'état des boutons
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                        const button = mutation.target;
                        if (button.classList.contains('nav-button')) {
                            // Ajouter une transition douce pour les changements de classe
                            button.style.transition = 'all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
                        }
                    }
                });
            });

            // Observer tous les boutons de navigation
            document.querySelectorAll('.nav-button').forEach(button => {
                observer.observe(button, {
                    attributes: true,
                    attributeFilter: ['class']
                });
            });
        });

                 // Animation de navigation ultra-fluide entre les sections
        document.addEventListener('click', function(e) {
             console.log('🖱️ Clic détecté sur:', e.target);
             
             if (e.target.closest('.nav-button')) {
                 const button = e.target.closest('.nav-button');
                 console.log('🎯 Bouton nav-button cliqué:', button);
                 
                 // Ajouter la classe de navigation pour l'animation
                 button.classList.add('navigating');
                 
                 // Effet de pulsation ultra-fluide au clic
                 button.style.transform = 'scale(0.93)';
                 button.style.filter = 'brightness(0.9)';
                 
                 setTimeout(() => {
                     button.style.transform = '';
                     button.style.filter = '';
                     button.classList.remove('navigating');
                 }, 600);
                 
                 // Vérifier si c'est un bouton de la sous-section patient
                 if (button.closest('.patient-submenu')) {
                     console.log('🎯 Bouton de sous-section patient cliqué !');
                     
                     // ANIMATION SPÉCIALE POUR LES BOUTONS DE SOUS-SECTION PATIENT
                     button.classList.add('clicked');
                     
                     // Appliquer l'effet de transition fluide spécial
                     applyPatientButtonTransition(button);
                     
                     // NOUVELLE LOGIQUE : Utiliser la même approche que les sections principales
                     const wireClick = button.getAttribute('wire:click');
                     console.log('🔗 wire:click détecté:', wireClick);
                     
                     if (wireClick && (wireClick.includes('showConsultation') || wireClick.includes('showReglement') || wireClick.includes('showRendezVous'))) {
                         console.log('✅ Méthode de sous-section détectée, préparation de la transition...');
                         
                         // Extraire la méthode depuis wire:click
                         const methodMatch = wireClick.match(/wire:click="([^"]+)"/);
                         if (methodMatch) {
                             const method = methodMatch[1];
                             console.log('🎯 Méthode détectée:', method);
                             
                             // Préparer la transition AVANT le changement
                             prepareTransitionBeforeAction(method);
                             
                             // Laisser Livewire continuer normalement
                             // L'animation sera gérée par le hook Livewire
                         }
                     } else {
                         console.warn('⚠️ wire:click ou méthode de sous-section non détectée');
                     }
                 } else {
                     console.log('ℹ️ Bouton non-patient cliqué');
                 }
                 
                 // Transition ultra-fluide pour les changements de contenu
                setTimeout(() => {
                     const contentSections = document.querySelectorAll('[wire\\:key*="consultation"], [wire\\:key*="rendezvous"], [wire\\:key*="dossier"]');
                     contentSections.forEach(section => {
                         if (section.style.display !== 'none') {
                             // Animation d'entrée ultra-fluide
                             section.style.opacity = '0';
                             section.style.transform = 'translateY(20px) scale(0.95)';
                             section.style.transition = 'all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1)';
                             
                             requestAnimationFrame(() => {
                                 section.style.opacity = '1';
                                 section.style.transform = 'translateY(0) scale(1)';
                             });
                         }
                     });
                 }, 200);
             } else {
                 console.log('ℹ️ Clic non sur nav-button');
             }
         });

                 // Fonction pour préparer la transition de contenu ultra-fluide avec chevauchement
         function prepareUltraFluidContentTransition() {
             const currentContent = document.querySelector('.content-section.show');
             const container = document.querySelector('.content-container');
             
             if (currentContent && container) {
                 // Marquer le conteneur comme en transition
                 container.classList.add('transitioning');
                 
                 // Animation de sortie ultra-fluide vers la gauche
                 currentContent.classList.remove('show');
                 currentContent.classList.add('exiting');
                 
                 // Nettoyer après l'animation
                 setTimeout(() => {
                     currentContent.classList.remove('exiting');
                     container.classList.remove('transitioning');
                 }, 400);
             }
         }

         // Fonction pour préparer la transition de contenu (ancienne version)
         function prepareContentTransition() {
             const currentContent = document.querySelector('.content-section.show');
             if (currentContent) {
                 // Animation de sortie
                 currentContent.classList.remove('show');
                 currentContent.classList.add('exiting');
                 
                 // Nettoyer après l'animation
                 setTimeout(() => {
                     currentContent.classList.remove('exiting');
                 }, 400);
             }
         }

         // Nouvelle fonction de transition croisée ultra-fluide
         function prepareCrossFadeTransition() {
             const currentContent = document.querySelector('.content-section.show');
             const container = document.querySelector('.content-container');
             
             if (currentContent && container) {
                 // Marquer le conteneur comme en transition
                 container.classList.add('transitioning');
                 
                 // Animation de sortie progressive (pas de disparition complète)
                 currentContent.classList.remove('show');
                 currentContent.classList.add('exiting');
                 
                 // Le contenu sort progressivement vers la gauche
                 // Pendant ce temps, le nouveau contenu entre depuis la droite
                 // Cela crée un effet de chevauchement fluide
                 
                 setTimeout(() => {
                     currentContent.classList.remove('exiting');
                     container.classList.remove('transitioning');
                 }, 400);
             }
         }

         // FONCTION CRITIQUE : Préparer la transition AVANT que Livewire ne recharge
         function prepareTransitionBeforeAction(newAction) {
             const currentContent = document.querySelector('.content-section.show');
             const container = document.querySelector('.content-container');
             
             if (currentContent && container) {
                 console.log('🚀 Préparation de la transition vers:', newAction);
                 
                 // Marquer le conteneur comme en transition
                 container.classList.add('transitioning');
                 
                 // Créer un clone du contenu actuel pour l'animation de sortie
                 const contentClone = currentContent.cloneNode(true);
                 contentClone.classList.remove('show');
                 contentClone.classList.add('exiting-clone');
                 contentClone.style.position = 'absolute';
                 contentClone.style.top = '0';
                 contentClone.style.left = '0';
                 contentClone.style.width = '100%';
                 contentClone.style.height = '100%';
                 contentClone.style.zIndex = '1000';
                 
                 // Ajouter le clone au conteneur
                 container.appendChild(contentClone);
                 
                 // Animation de sortie du clone vers la gauche
                 requestAnimationFrame(() => {
                     contentClone.style.transition = 'all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
                     contentClone.style.transform = 'translateX(-100%) scale(0.95)';
                     contentClone.style.opacity = '0';
                 });
                 
                 // Nettoyer le clone après l'animation
                 setTimeout(() => {
                     if (contentClone.parentNode) {
                         contentClone.parentNode.removeChild(contentClone);
                     }
                     container.classList.remove('transitioning');
                 }, 400);
                 
                 // Masquer le contenu original pendant la transition
                 currentContent.style.opacity = '0';
                 currentContent.style.transform = 'translateX(-50px) scale(0.95)';
                 currentContent.style.transition = 'all 0.3s ease-out';
                 
                 // Marquer le contenu comme en sortie pour éviter les conflits
                 currentContent.classList.add('exiting');
             }
         }

                 // Fonction pour animer l'entrée du nouveau contenu de manière ultra-fluide
         function animateContentEntry(newContent) {
             if (newContent) {
                 // Préparer l'animation d'entrée ultra-fluide depuis la droite
                 newContent.style.opacity = '0';
                 newContent.style.transform = 'translateX(100%) scale(0.95)';
                 newContent.style.transition = 'all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1)';
                 
                 // Ajouter la classe d'entrée pour l'animation CSS
                 newContent.classList.add('entering');
                 
                 // Utiliser requestAnimationFrame pour une animation ultra-fluide
                 requestAnimationFrame(() => {
                     newContent.style.opacity = '1';
                     newContent.style.transform = 'translateX(0) scale(1)';
                     
                     // Retirer la classe d'entrée après l'animation
                     setTimeout(() => {
                         newContent.classList.remove('entering');
                     }, 500);
                 });
             }
         }

         // FONCTION CRITIQUE : Animation d'entrée APRÈS le rechargement Livewire
         function animateContentEntryAfterLivewire(newContent) {
             if (newContent) {
                 console.log('🎬 Animation du nouveau contenu après Livewire:', newContent);
                 
                 try {
                     // Préparer l'animation d'entrée depuis la droite
                     newContent.style.opacity = '0';
                     newContent.style.transform = 'translateX(100%) scale(0.95)';
                     newContent.style.transition = 'all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1)';
                     
                     // Ajouter la classe spéciale pour l'animation après Livewire
                     newContent.classList.add('entering-after-livewire');
                     
                     // Utiliser requestAnimationFrame pour une animation ultra-fluide
                     requestAnimationFrame(() => {
                         try {
                             newContent.style.opacity = '1';
                             newContent.style.transform = 'translateX(0) scale(1)';
                             
                             // Retirer la classe après l'animation
                             setTimeout(() => {
                                 if (newContent && newContent.classList) {
                                     newContent.classList.remove('entering-after-livewire');
                                 }
                             }, 500);
                         } catch (error) {
                             console.error('❌ Erreur lors de l\'animation d\'entrée:', error);
                             // Fallback : afficher le contenu normalement
                             if (newContent) {
                                 newContent.style.opacity = '1';
                                 newContent.style.transform = 'translateX(0) scale(1)';
                             }
                         }
                     });
                 } catch (error) {
                     console.error('❌ Erreur lors de la préparation de l\'animation:', error);
                     // Fallback : afficher le contenu normalement
                     if (newContent) {
                         newContent.style.opacity = '1';
                         newContent.style.transform = 'translateX(0) scale(1)';
                     }
                 }
                      } else {
             console.warn('⚠️ Aucun nouveau contenu trouvé pour l\'animation');
         }
     }

     // FONCTION SPÉCIALE : Transitions fluides pour les boutons de sous-section patient
     function applyPatientButtonTransition(button) {
         if (!button) return;
         
         console.log('🎯 Application de la transition fluide pour le bouton patient:', button);
         
         try {
             // Marquer le bouton comme en transition
             button.classList.add('transitioning');
             
             // Effet de pulsation ultra-fluide
             button.style.transform = 'scale(0.92) translateY(-2px)';
             button.style.filter = 'brightness(0.85) saturate(0.9)';
             button.style.boxShadow = '0 15px 30px rgba(30, 58, 138, 0.3)';
             
             // Animation de rebond fluide
             setTimeout(() => {
                 button.style.transform = 'scale(1.05) translateY(-4px)';
                 button.style.filter = 'brightness(1.1) saturate(1.1)';
                 button.style.boxShadow = '0 25px 50px rgba(30, 58, 138, 0.4)';
                 
                 setTimeout(() => {
                     button.style.transform = 'scale(1) translateY(0)';
                     button.style.filter = 'brightness(1) saturate(1)';
                     button.style.boxShadow = '';
                     button.classList.remove('transitioning');
                     button.classList.remove('clicked');
                 }, 300);
             }, 200);
             
             // Effet de vague de couleur
             const ripple = document.createElement('div');
             ripple.style.cssText = `
                 position: absolute;
                 top: 50%;
                 left: 50%;
                 width: 0;
                 height: 0;
                 background: radial-gradient(circle, rgba(59, 130, 246, 0.3) 0%, transparent 70%);
                 border-radius: 50%;
                 transform: translate(-50%, -50%);
                 transition: all 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
                 pointer-events: none;
                 z-index: 2;
             `;
             
             button.appendChild(ripple);
             
             // Animation de la vague
             requestAnimationFrame(() => {
                 ripple.style.width = '200px';
                 ripple.style.height = '200px';
                 ripple.style.opacity = '0';
             });
             
             // Nettoyer la vague
             setTimeout(() => {
                 if (ripple.parentNode) {
                     ripple.parentNode.removeChild(ripple);
                 }
             }, 600);
             
         } catch (error) {
             console.error('❌ Erreur lors de l\'application de la transition patient:', error);
             // Fallback : nettoyer les classes
             button.classList.remove('transitioning', 'clicked');
         }
     }

     // FONCTION : Mise à jour de l'état visuel des boutons de sous-section après Livewire
     function updatePatientButtonStates(componentEl) {
         if (!componentEl) return;
         
         console.log('🎨 Mise à jour de l\'état visuel des boutons de sous-section...');
         
         try {
             // Récupérer tous les boutons de sous-section
             const patientButtons = componentEl.querySelectorAll('.patient-nav-button');
             
             patientButtons.forEach(button => {
                 // Nettoyer les états précédents
                 button.classList.remove('active', 'inactive', 'transitioning');
                 
                 // Récupérer l'action du bouton
                 const wireClick = button.getAttribute('wire:click');
                 const actionMatch = wireClick ? wireClick.match(/setAction\('([^']+)'\)/) : null;
                 
                 if (actionMatch) {
                     const buttonAction = actionMatch[1];
                     
                     // Vérifier si c'est l'action actuellement active
                     const currentContent = componentEl.querySelector('.content-section.show');
                     if (currentContent) {
                         const currentAction = currentContent.getAttribute('data-action');
                         
                         if (buttonAction === currentAction) {
                             // Bouton actif - animation d'activation
                             button.classList.add('active');
                             animateButtonActivation(button);
                         } else {
                             // Bouton inactif - animation de désactivation
                             button.classList.add('inactive');
                             animateButtonDeactivation(button);
                         }
                     }
                 }
             });
             
         } catch (error) {
             console.error('❌ Erreur lors de la mise à jour des états des boutons:', error);
         }
     }

     // FONCTION : Animation d'activation d'un bouton
     function animateButtonActivation(button) {
         if (!button) return;
         
         try {
             // Effet de pulsation d'activation
             button.style.transform = 'scale(1.05)';
             button.style.filter = 'brightness(1.1) saturate(1.1)';
             button.style.boxShadow = '0 20px 40px rgba(30, 58, 138, 0.3)';
             
             setTimeout(() => {
                 button.style.transform = 'scale(1)';
                 button.style.filter = 'brightness(1) saturate(1)';
                 button.style.boxShadow = '';
             }, 300);
             
         } catch (error) {
             console.error('❌ Erreur lors de l\'animation d\'activation:', error);
         }
     }

              // FONCTION : Animation de désactivation d'un bouton
         function animateButtonDeactivation(button) {
             if (!button) return;
             
             try {
                 // Effet de désactivation subtil
                 button.style.filter = 'brightness(0.95) saturate(0.9)';
                 button.style.transform = 'scale(0.98)';
                 
                 setTimeout(() => {
                     button.style.filter = 'brightness(1) saturate(1)';
                     button.style.transform = 'scale(1)';
                 }, 200);
                 
             } catch (error) {
                 console.error('❌ Erreur lors de l\'animation de désactivation:', error);
             }
         }

         // FONCTION CRITIQUE : Test des événements attachés
         function testEventListeners() {
             console.log('🧪 TEST DES ÉVÉNEMENTS : Vérification des listeners...');
             
             // Test 1 : Vérifier que les boutons de sous-section existent
             const patientButtons = document.querySelectorAll('.patient-submenu .patient-nav-button');
             console.log('🧪 Test 1 - Boutons trouvés:', patientButtons.length);
             
             if (patientButtons.length === 0) {
                 console.error('❌ CRITIQUE : Aucun bouton de sous-section trouvé !');
                 return;
             }
             
             // Test 2 : Vérifier que les boutons ont wire:click
             patientButtons.forEach((button, index) => {
                 const wireClick = button.getAttribute('wire:click');
                 console.log(`🧪 Test 2 - Bouton ${index + 1} wire:click:`, wireClick);
                 
                 if (!wireClick || !wireClick.includes('setAction')) {
                     console.error(`❌ CRITIQUE : Bouton ${index + 1} n'a pas de wire:click valide !`);
                 }
             });
             
             // Test 3 : Vérifier que les sections de contenu existent
             const contentSections = document.querySelectorAll('.content-section');
             console.log('🧪 Test 3 - Sections de contenu trouvées:', contentSections.length);
             
             // Test 4 : Vérifier que le conteneur existe
             const container = document.querySelector('.content-container');
             console.log('🧪 Test 4 - Conteneur trouvé:', !!container);
             
             // Test 5 : Vérifier que les styles CSS sont appliqués
             const firstButton = patientButtons[0];
             if (firstButton) {
                 const computedStyle = window.getComputedStyle(firstButton);
                 console.log('🧪 Test 5 - Styles CSS appliqués:', {
                     transition: computedStyle.transition,
                     transform: computedStyle.transform,
                     position: computedStyle.position
                 });
             }
             
                      console.log('🧪 FIN DES TESTS');
     }

     // SYSTÈME DE MONITORING EN TEMPS RÉEL
     function startRealTimeMonitoring() {
         console.log('🔍 Démarrage du monitoring en temps réel...');
         
         // Vérifier toutes les 2 secondes
         setInterval(() => {
             // Vérifier que les boutons existent toujours
             const patientButtons = document.querySelectorAll('.patient-submenu .patient-nav-button');
             if (patientButtons.length === 0) {
                 console.warn('⚠️ MONITORING : Aucun bouton de sous-section trouvé !');
                 return;
             }
             
             // Vérifier que les événements sont toujours attachés
             patientButtons.forEach((button, index) => {
                 const wireClick = button.getAttribute('wire:click');
                 if (!wireClick || !wireClick.includes('setAction')) {
                     console.warn(`⚠️ MONITORING : Bouton ${index + 1} a perdu son wire:click !`);
                 }
             });
             
             // Vérifier que le conteneur existe
             const container = document.querySelector('.content-container');
             if (!container) {
                 console.warn('⚠️ MONITORING : Conteneur de contenu perdu !');
             }
             
             // Vérifier que les sections de contenu existent
             const contentSections = document.querySelectorAll('.content-section');
             if (contentSections.length === 0) {
                 console.warn('⚠️ MONITORING : Aucune section de contenu trouvée !');
             }
             
         }, 2000);
         
         console.log('✅ Monitoring en temps réel activé');
     }

         // Fonction pour animer l'entrée du nouveau contenu (ancienne version)
         function animateContentEntryOld(newContent) {
             if (newContent) {
                 // Préparer l'animation d'entrée
                 newContent.style.opacity = '0';
                 newContent.style.transform = 'translateY(20px) scale(0.98)';
                 newContent.style.transition = 'all 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
                 
                 // Utiliser requestAnimationFrame pour une animation fluide
                 requestAnimationFrame(() => {
                     newContent.style.opacity = '1';
                     newContent.style.transform = 'translateY(0) scale(1)';
                 });
             }
         }

                 // HOOK LIVEWIRE UNIFIÉ ET ROBUSTE pour les transitions
         Livewire.hook('message.processed', (message, component) => {
             console.log('🎯 Hook Livewire UNIFIÉ déclenché');
             console.log('📋 Message:', message);
             console.log('🏗️ Component:', component);
             
             try {
                 // Gestion spéciale pour les changements d'action avec navigation ultra-fluide
                 if (message.updateQueue && message.updateQueue.some(update => 
                     update.type === 'callMethod' && 
                     (update.payload.method === 'setAction' && ['consultation', 'reglement', 'rendezvous'].includes(update.payload.params[0])) ||
                     (update.payload.method === 'showConsultation' || update.payload.method === 'showReglement' || update.payload.method === 'showRendezVous')
                 )) {
                     console.log('🎯 Livewire a changé l\'action, animation du nouveau contenu...');
                     
                     // Attendre que le DOM soit complètement mis à jour
                     setTimeout(() => {
                         console.log('⏰ Délai écoulé, recherche du nouveau contenu...');
                         
                         const newContent = component.el.querySelector('.content-section.show');
                         console.log('📄 Nouveau contenu trouvé:', newContent);
                         
                         if (newContent) {
                             // Animation d'entrée depuis la droite APRÈS le rechargement Livewire
                             animateContentEntryAfterLivewire(newContent);
                         } else {
                             console.warn('⚠️ Aucun nouveau contenu trouvé pour l\'animation');
                         }
                         
                         // Nettoyer les classes de transition
                         const oldContent = component.el.querySelector('.content-section.exiting');
                         if (oldContent) {
                             console.log('🧹 Nettoyage de l\'ancien contenu...');
                             oldContent.classList.remove('exiting');
                             oldContent.style.opacity = '';
                             oldContent.style.transform = '';
                             oldContent.style.transition = '';
                         }
                         
                         // ANIMATION SPÉCIALE : Mettre à jour l'état visuel des boutons de sous-section
                         console.log('🎨 Mise à jour des états des boutons...');
                         updatePatientButtonStates(component.el);
                     }, 150); // Délai un peu plus long pour s'assurer que le DOM est stable
                 } else {
                     console.log('ℹ️ Pas de changement d\'action détecté');
                 }
                 
                 // Gestion générale des animations pour les nouveaux éléments
                 const newElements = component.el.querySelectorAll('[data-animate]');
                 if (newElements.length > 0) {
                     console.log('🎬 Animation des nouveaux éléments:', newElements.length);
                     newElements.forEach(element => {
                         const animation = element.getAttribute('data-animate');
                         element.classList.add(animation);
                     });
                 }
                 
             } catch (error) {
                 console.error('❌ Erreur dans le hook Livewire:', error);
             }
         });

        // Gestion des changements d'action avec transitions fluides
        document.addEventListener('livewire:load', function() {
            // Écouter les changements d'action
            Livewire.hook('message.processed', (message, component) => {
                // Vérifier si c'est un changement d'action
                if (message.updateQueue && message.updateQueue.some(update => 
                    update.type === 'callMethod' && update.payload.method === 'setAction'
                )) {
                    // Préparer la transition
                    prepareContentTransition();
                    
                    // Attendre un peu avant d'animer le nouveau contenu
                    setTimeout(() => {
                        const newContent = component.el.querySelector('.content-section.show');
                        if (newContent) {
                            animateContentEntry(newContent);
                        }
                    }, 100);
                }
            });
        });

        // Optimisation des performances pour les animations
        document.addEventListener('DOMContentLoaded', function() {
            // Précharger les animations CSS
            const style = document.createElement('style');
            style.textContent = `
                .nav-button {
                    will-change: transform, opacity, box-shadow;
                    transform-style: preserve-3d;
                    backface-visibility: hidden;
                }
                .icon-container {
                    will-change: transform;
                }
                .submenu-enter, .submenu-exit {
                    will-change: transform, opacity;
                }
                .patient-submenu {
                    will-change: transform, opacity;
                }
                .content-section {
                    will-change: transform, opacity;
                }
            `;
            document.head.appendChild(style);

            // Initialiser les transitions pour la sous-section patient
            initializePatientSubmenu();
        });

        // Fonction d'initialisation de la sous-section patient
        function initializePatientSubmenu() {
            const patientSubmenu = document.querySelector('.patient-submenu');
            const patientMenuContainer = document.querySelector('.patient-menu-container');
            
            if (patientSubmenu && patientMenuContainer) {
                // Ajouter des écouteurs d'événements pour les boutons
                const buttons = patientSubmenu.querySelectorAll('.nav-button');
                
                // Observer les changements de classe pour déclencher les animations
                const observer = new MutationObserver((mutations) => {
                    mutations.forEach((mutation) => {
                        if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                            const target = mutation.target;
                            
                            if (target.classList.contains('show')) {
                                // Animation d'ouverture
                                animateMenuOpening(patientSubmenu, buttons);
                            } else if (target.classList.contains('hide')) {
                                // Animation de fermeture
                                animateMenuClosing(patientSubmenu, buttons);
                            }
                        }
                    });
                });
                
                // Observer les changements de classe du sous-menu
                observer.observe(patientSubmenu, {
                    attributes: true,
                    attributeFilter: ['class']
                });
                
                // Observer les changements de classe du conteneur
                observer.observe(patientMenuContainer, {
                    attributes: true,
                    attributeFilter: ['class']
                });
            }
        }

        // Fonction pour animer l'ouverture du menu
        function animateMenuOpening(menu, buttons) {
            // Réinitialiser l'état des boutons
            buttons.forEach(button => {
                button.classList.remove('visible');
                button.style.opacity = '0';
                button.style.transform = 'translateY(20px) scale(0.9)';
            });
            
            // Animer l'ouverture du conteneur
            const container = menu.closest('.patient-menu-container');
            if (container) {
                container.classList.add('expanded');
            }
            
            // Animer l'apparition progressive des boutons
            buttons.forEach((button, index) => {
                setTimeout(() => {
                    button.classList.add('visible');
                }, 100 + (index * 100)); // Délai progressif
            });
        }

        // Fonction pour animer la fermeture du menu
        function animateMenuClosing(menu, buttons) {
            // Animer la disparition des boutons
            buttons.forEach((button, index) => {
                setTimeout(() => {
                    button.classList.remove('visible');
                }, (buttons.length - 1 - index) * 50); // Délai inversé
            });
            
            // Fermer le conteneur après l'animation des boutons
            setTimeout(() => {
                const container = menu.closest('.patient-menu-container');
                if (container) {
                    container.classList.remove('expanded');
                }
            }, buttons.length * 50 + 200);
        }

    document.addEventListener('alpine:init', () => {
        Alpine.data('modal', () => ({
            show: false,
            open() {
                this.show = true;
            },
            close() {
                this.show = false;
            }
        }));

        Alpine.data('menuAnimation', () => ({
            isOpen: false,
            toggle() {
                this.isOpen = !this.isOpen;
                if (this.isOpen) {
                    this.$nextTick(() => {
                        this.$el.classList.add('animate-slide-in-down');
                    });
                } else {
                    this.$el.classList.add('animate-slide-out-up');
                    setTimeout(() => {
                        this.$el.classList.remove('animate-slide-out-up');
                    }, 500);
                }
            }
        }));
    });
    
    // Gestion du chargement du modal
    window.addEventListener('modal-loading', function(e) {
        const loadingDiv = document.getElementById('modal-loading');
        const contentDiv = document.querySelector('livewire\\:create-rendez-vous');
        
        if (e.detail.loading) {
            if (loadingDiv) loadingDiv.classList.remove('hidden');
            if (contentDiv) contentDiv.style.display = 'none';
        } else {
            if (loadingDiv) loadingDiv.classList.add('hidden');
            if (contentDiv) contentDiv.style.display = 'block';
        }
    });
    
    // Raccourcis clavier pour accélérer la navigation
    document.addEventListener('keydown', function(e) {
        // Seulement si le modal est ouvert
        if (!document.querySelector('.modal-content')) return;
        
        // Alt + P : Focus sur la recherche de patient
        if (e.altKey && e.key === 'p') {
            e.preventDefault();
            const patientSearch = document.querySelector('input[placeholder*="patient"]');
            if (patientSearch) patientSearch.focus();
        }
        
        // Alt + M : Focus sur la sélection du médecin
        if (e.altKey && e.key === 'm') {
            e.preventDefault();
            const medecinSelect = document.getElementById('medecin_id');
            if (medecinSelect) medecinSelect.focus();
        }
        
        // Alt + D : Focus sur la date
        if (e.altKey && e.key === 'd') {
            e.preventDefault();
            const dateInput = document.getElementById('date_rdv');
            if (dateInput) dateInput.focus();
        }
        
        // Alt + H : Focus sur l'heure
        if (e.altKey && e.key === 'h') {
            e.preventDefault();
            const heureInput = document.getElementById('heure_rdv');
            if (heureInput) heureInput.focus();
        }
        
        // Alt + A : Focus sur l'acte prévu
        if (e.altKey && e.key === 'a') {
            e.preventDefault();
            const acteInput = document.getElementById('acte_prevu');
            if (acteInput) acteInput.focus();
        }
        
        // Alt + S : Focus sur le statut
        if (e.altKey && e.key === 's') {
            e.preventDefault();
            const statutSelect = document.getElementById('rdv_confirm');
            if (statutSelect) statutSelect.focus();
        }
        
        // Ctrl + Enter : Soumettre le formulaire
        if (e.ctrlKey && e.key === 'Enter') {
            e.preventDefault();
            const submitBtn = document.querySelector('button[type="submit"]');
            if (submitBtn) submitBtn.click();
        }
        
        // Échap : Fermer le modal
        if (e.key === 'Escape') {
            e.preventDefault();
            const closeBtn = document.querySelector('button[wire\\:click*="closeCreateRdvModal"]');
            if (closeBtn) closeBtn.click();
        }
    });

         // Optimisation des performances
     document.addEventListener('DOMContentLoaded', function() {
         // Utiliser requestAnimationFrame pour les animations fluides
         const observerOptions = {
             threshold: 0.1,
             rootMargin: '0px 0px -50px 0px'
         };

         const observer = new IntersectionObserver((entries) => {
             entries.forEach(entry => {
                 if (entry.isIntersecting) {
                     entry.target.classList.add('animate-fade-in-scale');
                 }
             });
         }, observerOptions);

         // Observer les éléments avec animation
         document.querySelectorAll('[data-animate-on-scroll]').forEach(el => {
             observer.observe(el);
         });
     });

     // Gestion des transitions fluides entre les onglets et le contenu
     document.addEventListener('livewire:load', function() {
         // Écouter les changements d'onglets et les actions du patient
         Livewire.hook('message.processed', (message, component) => {
             // Vérifier si c'est un changement d'onglet
             if (message.updateQueue && message.updateQueue.some(update => 
                 update.type === 'callMethod' && 
                 (update.payload.method === '$set' && update.payload.params && update.payload.params[0] === 'activeRdvTab')
             )) {
                 
                 // Ajouter une animation de transition
                 const tabContent = document.querySelector('.tab-content');
                 const tabPanels = document.querySelectorAll('.tab-panel');
                 
                 if (tabContent && tabPanels.length > 0) {
                     // Animation de sortie pour l'ancien contenu
                     tabPanels.forEach(panel => {
                         if (panel.classList.contains('showing')) {
                             panel.classList.remove('showing');
                             panel.classList.add('hiding');
                             
                             setTimeout(() => {
                                 panel.classList.remove('hiding');
                             }, 300);
                         }
                     });
                     
                     // Animation d'entrée pour le nouveau contenu
                     setTimeout(() => {
                         const activePanel = document.querySelector('.tab-panel');
                         if (activePanel) {
                             activePanel.classList.add('showing');
                         }
                     }, 150);
                 }
             }

             // Gestion des transitions de contenu pour les actions du patient
             if (message.updateQueue && message.updateQueue.some(update => 
                 update.type === 'callMethod' && 
                 (update.payload.method === 'setAction' && ['consultation', 'reglement', 'rendezvous', 'dossier'].includes(update.payload.params[0]))
             )) {
                 
                 // Animation de sortie pour l'ancien contenu
                 const oldContent = document.querySelector('.content-section.show');
                 if (oldContent) {
                     oldContent.classList.remove('show');
                     oldContent.classList.add('exiting');
                     
                     setTimeout(() => {
                         oldContent.classList.remove('exiting');
                     }, 400);
                 }

                 // Animation d'entrée pour le nouveau contenu
                 setTimeout(() => {
                     const newContent = document.querySelector('.content-section:not(.exiting)');
                     if (newContent) {
                         newContent.classList.add('entering');
                         
                         setTimeout(() => {
                             newContent.classList.remove('entering');
                             newContent.classList.add('show');
                         }, 600);
                     }
                 }, 200);
             }

             // Gestion de l'ouverture/fermeture du sous-menu patient
             if (message.updateQueue && message.updateQueue.some(update => 
                 update.type === 'callMethod' && 
                 (update.payload.method === 'togglePatientMenu')
             )) {
                 // Attendre que le DOM soit mis à jour
                 setTimeout(() => {
                     const submenu = document.querySelector('.patient-submenu');
                     const container = document.querySelector('.patient-menu-container');
                     
                     if (submenu && container) {
                         if (submenu.classList.contains('show')) {
                             // Le sous-menu est ouvert, utiliser l'animation d'ouverture
                             openPatientSubmenu();
                         } else {
                             // Le sous-menu est fermé, utiliser l'animation de fermeture
                             closePatientSubmenu();
                         }
                     }
                 }, 50);
             }
         });

         // Animation des boutons d'onglets au clic
         document.addEventListener('click', function(e) {
             if (e.target.classList.contains('tab-button')) {
                 // Ajouter une animation de clic
                 e.target.classList.add('clicked');
                 setTimeout(() => {
                     e.target.classList.remove('clicked');
                 }, 200);
             }
         });
     });

     // Fonction d'initialisation ultra-fluide de la sous-section patient
     function initializePatientSubmenu() {
         const patientSubmenu = document.querySelector('.patient-submenu');
         if (patientSubmenu) {
             // Ajouter des écouteurs d'événements pour les boutons
             const buttons = patientSubmenu.querySelectorAll('.nav-button');
             buttons.forEach((button, index) => {
                 // Ajouter un délai progressif pour l'animation d'entrée
                 button.style.transitionDelay = `${index * 0.1}s`;
                 
                 // Effet de hover ultra-fluide
                 button.addEventListener('mouseenter', function() {
                     this.style.transform = 'translateY(-4px) scale(1.04)';
                     this.style.boxShadow = '0 20px 40px rgba(30, 58, 138, 0.25)';
                     this.style.filter = 'brightness(1.05)';
                 });
                 
                 button.addEventListener('mouseleave', function() {
                     this.style.transform = '';
                     this.style.boxShadow = '';
                     this.style.filter = '';
                 });

                 // Effet de clic ultra-fluide
                 button.addEventListener('click', function() {
                     this.style.transform = 'scale(0.95)';
                     this.style.filter = 'brightness(0.95)';
                 
                 setTimeout(() => {
                         this.style.transform = '';
                         this.style.filter = '';
                     }, 150);
                 });
             });
         }
     }

     // Fonction pour gérer l'ouverture fluide du sous-menu
     function openPatientSubmenu() {
         const submenu = document.querySelector('.patient-submenu');
         const container = document.querySelector('.patient-menu-container');
         
         if (submenu && container) {
             // Préparer l'ouverture
             submenu.classList.remove('hide');
             submenu.classList.add('show');
             container.classList.add('expanded');
             
             // Animation progressive des boutons
             const buttons = submenu.querySelectorAll('.nav-button');
             buttons.forEach((button, index) => {
                 setTimeout(() => {
                     button.classList.add('visible');
                 }, 200 + (index * 100));
             });
         }
     }

     // Fonction pour gérer la fermeture fluide du sous-menu
     function closePatientSubmenu() {
         const submenu = document.querySelector('.patient-submenu');
         const container = document.querySelector('.patient-menu-container');
         
         if (submenu && container) {
             // Animation de sortie des boutons d'abord
             const buttons = submenu.querySelectorAll('.nav-button');
             buttons.forEach((button, index) => {
                 setTimeout(() => {
                     button.classList.remove('visible');
                 }, index * 50);
             });
             
             // Fermer le sous-menu après un délai
             setTimeout(() => {
                 submenu.classList.remove('show');
                 submenu.classList.add('hide');
                 container.classList.remove('expanded');
             }, 300);
         }
     }

                    // Appeler l'initialisation au chargement
         document.addEventListener('DOMContentLoaded', function() {
             console.log('🚀 Initialisation de la navigation ultra-fluide...');
             
             // Attendre un peu que Livewire soit prêt
             setTimeout(() => {
                 initializePatientSubmenu();
                 initializeUltraFluidNavigation();
                 
                 // Test de débogage
                 console.log('✅ Navigation ultra-fluide initialisée');
                 
                 // Vérifier que les boutons sont bien détectés
                 const patientButtons = document.querySelectorAll('.patient-submenu .nav-button');
                 console.log('🎯 Boutons de sous-section patient détectés:', patientButtons.length);
                 
                 patientButtons.forEach((button, index) => {
                     const wireClick = button.getAttribute('wire:click');
                     console.log(`📋 Bouton ${index + 1}:`, wireClick);
                 });
                 
                 // Vérifier la structure des sections de contenu
                 const contentSections = document.querySelectorAll('.content-section');
                 console.log('📄 Sections de contenu détectées:', contentSections.length);
                 
                 contentSections.forEach((section, index) => {
                     const action = section.getAttribute('data-action');
                     const classes = section.className;
                     console.log(`📄 Section ${index + 1} (${action}):`, classes);
                 });
                 
                 // Vérifier le conteneur
                 const container = document.querySelector('.content-container');
                 if (container) {
                     console.log('📦 Conteneur de contenu trouvé:', container.className);
                     console.log('📦 Position du conteneur:', window.getComputedStyle(container).position);
                 } else {
                     console.warn('⚠️ Conteneur de contenu non trouvé');
                 }
                 
                 // DÉBOGAGE SPÉCIAL : Vérifier les boutons de sous-section patient
                 const patientSubmenu = document.querySelector('.patient-submenu');
                 if (patientSubmenu) {
                     console.log('🎯 Sous-menu patient trouvé:', patientSubmenu.className);
                     
                     const patientButtons = patientSubmenu.querySelectorAll('.patient-nav-button');
                     console.log('🎯 Boutons de sous-section patient dans le DOM:', patientButtons.length);
                     
                     patientButtons.forEach((button, index) => {
                         const wireClick = button.getAttribute('wire:click');
                         const classes = button.className;
                         console.log(`🎯 Bouton sous-section ${index + 1}:`, {
                             wireClick: wireClick,
                             classes: classes,
                             text: button.textContent.trim()
                         });
                     });
                 } else {
                     console.warn('⚠️ Sous-menu patient non trouvé');
                 }
                 
                 // TEST CRITIQUE : Vérifier que les événements sont bien attachés
                 testEventListeners();
                 
                 // SYSTÈME DE TEST EN TEMPS RÉEL
                 startRealTimeMonitoring();
             }, 500);
         });

      // Fonction d'initialisation de la navigation ultra-fluide
      function initializeUltraFluidNavigation() {
          // Ajouter des écouteurs d'événements pour tous les boutons de navigation
          const allNavButtons = document.querySelectorAll('.nav-button');
          
          allNavButtons.forEach(button => {
              // Ajouter la classe de transition
              button.classList.add('nav-transition');
              
              // Écouter les clics pour la navigation ultra-fluide
              button.addEventListener('click', function(e) {
                  // Empêcher le comportement par défaut si nécessaire
                  if (button.hasAttribute('wire:click')) {
                      // Ajouter l'animation de navigation
                      button.classList.add('navigating');
                      
                      // Retirer la classe après l'animation
                      setTimeout(() => {
                          button.classList.remove('navigating');
                 }, 600);
             }
         });

              // Écouter les changements d'état
              button.addEventListener('mouseenter', function() {
                  if (!this.classList.contains('navigating')) {
                      this.classList.add('state-transition');
                      setTimeout(() => {
                          this.classList.remove('state-transition');
                      }, 400);
             }
         });
     });
     
          // Observer les changements du DOM pour les nouveaux boutons
          const observer = new MutationObserver((mutations) => {
              mutations.forEach((mutation) => {
                  mutation.addedNodes.forEach((node) => {
                      if (node.nodeType === 1 && node.classList && node.classList.contains('nav-button')) {
                          // Initialiser les nouveaux boutons
                          node.classList.add('nav-transition');
                          node.addEventListener('click', function() {
                              if (this.hasAttribute('wire:click')) {
                                  this.classList.add('navigating');
                                  setTimeout(() => {
                                      this.classList.remove('navigating');
                                  }, 600);
                              }
                          });
                      }
                  });
              });
          });

          observer.observe(document.body, {
              childList: true,
              subtree: true
          });
      }

    // Gérer l'ouverture de facture depuis la pharmacie
    // Attendre que le composant ReglementFacture soit monté avant d'émettre l'événement
    Livewire.hook('message.processed', (message, component) => {
        // Vérifier si c'est le composant ReglementFacture qui vient d'être monté
        if (component && component.el) {
            const reglementComponent = component.el.closest('[wire\\:id*="reglement"]');
            if (reglementComponent) {
                // Le composant ReglementFacture est monté, vérifier s'il y a une facture en attente
                const pendingFactureId = window.pendingFacturePharmacieId;
                if (pendingFactureId) {
                    setTimeout(() => {
                        Livewire.emit('ouvrirFacturePharmacie', pendingFactureId);
                        window.pendingFacturePharmacieId = null;
                    }, 300);
                }
                
                // Vérifier s'il y a un patient en attente pour ouvrir les factures de pharmacie
                const pendingPatientId = window.pendingPatientIdForFactures;
                if (pendingPatientId) {
                    setTimeout(() => {
                        Livewire.emit('ouvrirListeFacturesPharmacie', pendingPatientId);
                        window.pendingPatientIdForFactures = null;
                    }, 300);
                }
                
                // Vérifier s'il y a des données de patient en attente pour ReglementFacture
                const pendingPatientData = window.pendingPatientDataForReglement;
                if (pendingPatientData) {
                    setTimeout(() => {
                        // Émettre l'événement Livewire avec les données du patient
                        Livewire.emit('patientSelectedForReglement', pendingPatientData);
                        window.pendingPatientDataForReglement = null;
                    }, 300);
                }
            }
        }
    });

    // Écouter l'événement browser pour stocker l'ID de facture en attente
    window.addEventListener('ouvrir-facture-pharmacie', function(event) {
        const factureId = event.detail?.factureId;
        if (factureId) {
            window.pendingFacturePharmacieId = factureId;
            // Émettre l'événement Livewire directement aussi
            Livewire.emit('ouvrirFacturePharmacie', factureId);
        }
    });

    // Écouter l'événement pour fermer le modal de pharmacie
    window.addEventListener('fermer-modal-pharmacie', function(event) {
        @this.call('fermerPharmacieModal');
        @this.set('showVentePharmacie', false);
    });

    // Écouter l'événement pour ouvrir la liste des factures de pharmacie
    window.addEventListener('ouvrir-factures-pharmacie', function(event) {
        const patientId = event.detail?.patientId;
        if (patientId) {
            // Stocker l'ID du patient pour l'utiliser après l'ouverture du modal
            window.pendingPatientIdForFactures = patientId;
            
            // Récupérer les données du patient depuis AccueilPatient pour les stocker
            // Cela sera utilisé par le hook message.processed
            const selectedPatient = @this.get('selectedPatient');
            if (selectedPatient) {
                window.pendingPatientDataForReglement = selectedPatient;
            }
            
            // Fermer le modal de pharmacie s'il est ouvert (au cas où)
            @this.call('fermerPharmacieModal');
            @this.set('showVentePharmacie', false);
            
            // Ouvrir le modal de règlement (Facture/Devis) en passant le patientId
            // Cela garantit que le patient est sélectionné dans AccueilPatient
            @this.call('showReglement', patientId);
            
            // Attendre que le composant ReglementFacture soit monté puis émettre l'événement
            // Utiliser plusieurs tentatives pour s'assurer que le composant lazy est chargé
            let attempts = 0;
            const maxAttempts = 5;
            
            const tryEmitEvent = () => {
                attempts++;
                // Vérifier si le composant est monté en cherchant l'élément
                const reglementComponent = document.querySelector('[wire\\:id*="reglement"]');
                if (reglementComponent) {
                    console.log('ReglementFacture component found, emitting events');
                    // Émettre l'événement avec les données du patient
                    if (window.pendingPatientDataForReglement) {
                        Livewire.emit('patientSelectedForReglement', window.pendingPatientDataForReglement);
                    }
                    Livewire.emit('ouvrirListeFacturesPharmacie', patientId);
                    window.pendingPatientIdForFactures = null;
                    window.pendingPatientDataForReglement = null;
                } else if (attempts < maxAttempts) {
                    // Si le composant n'est pas encore monté, réessayer
                    console.log('ReglementFacture component not found, retrying...', attempts);
                    setTimeout(tryEmitEvent, 300);
                } else {
                    // Dernière tentative
                    console.log('ReglementFacture component not found after max attempts, emitting anyway');
                    if (window.pendingPatientDataForReglement) {
                        Livewire.emit('patientSelectedForReglement', window.pendingPatientDataForReglement);
                    }
                    Livewire.emit('ouvrirListeFacturesPharmacie', patientId);
                    window.pendingPatientIdForFactures = null;
                    window.pendingPatientDataForReglement = null;
                }
            };
            
            // Démarrer après un délai initial
            setTimeout(tryEmitEvent, 500);
        }
    });

</script>
@endpush
