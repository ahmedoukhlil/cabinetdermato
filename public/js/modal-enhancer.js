/**
 * Améliorateur de fluidité pour les modaux existants de la Gestion du Cabinet
 * Version SIMPLIFIÉE avec animations réduites
 */
class ModalEnhancer {
    constructor() {
        this.activeModals = new Set();
        this.init();
    }

    init() {
        // Écouter les événements Livewire pour détecter l'ouverture/fermeture des modaux
        this.listenToLivewireEvents();
        
        // Améliorer les animations existantes de manière SIMPLE
        this.enhanceExistingAnimations();
        
        // Ajouter des fermetures fluides SIMPLES
        this.addSmoothClosing();
        
        // Optimiser les performances de manière SIMPLE
        this.optimizePerformance();
    }

    /**
     * Écouter les événements Livewire
     */
    listenToLivewireEvents() {
        // Écouter les événements de chargement Livewire
        document.addEventListener('livewire:load', () => {
            console.log('🚀 Livewire chargé, amélioration SIMPLE des modaux activée');
        });

        // Écouter les événements de mise à jour
        document.addEventListener('livewire:update', () => {
            this.enhanceNewModals();
        });

        // Écouter les événements de rendu
        document.addEventListener('livewire:render', () => {
            this.enhanceNewModals();
        });
    }

    /**
     * Améliorer les animations existantes de manière SIMPLE
     */
    enhanceExistingAnimations() {
        // Améliorer le backdrop de manière SIMPLE
        this.enhanceBackdrop();
        
        // Améliorer les boutons de fermeture de manière SIMPLE
        this.enhanceCloseButtons();
        
        // Améliorer les transitions de manière SIMPLE
        this.enhanceTransitions();
    }

    /**
     * Améliorer le backdrop des modaux de manière SIMPLE
     */
    enhanceBackdrop() {
        const backdrops = document.querySelectorAll('.modal-backdrop');
        
        backdrops.forEach(backdrop => {
            // Utiliser des transitions SIMPLES
            backdrop.style.transition = 'all 0.2s ease-out';
            
            // Pas d'effets de parallaxe complexes
            // Pas de particules
        });
    }

    /**
     * Améliorer les boutons de fermeture de manière SIMPLE
     */
    enhanceCloseButtons() {
        const closeButtons = document.querySelectorAll('.modal-close-button');
        
        closeButtons.forEach(button => {
            // Transitions SIMPLES
            button.style.transition = 'all 0.2s ease-out';
            
            // Effets de survol SIMPLES
            button.addEventListener('mouseenter', () => {
                button.style.transform = 'scale(1.05)';
            });

            button.addEventListener('mouseleave', () => {
                button.style.transform = 'scale(1)';
            });

            // Effets actifs SIMPLES
            button.addEventListener('mousedown', () => {
                button.style.transform = 'scale(0.95)';
            });

            button.addEventListener('mouseup', () => {
                button.style.transform = 'scale(1.05)';
            });
        });
    }

    /**
     * Améliorer les transitions de manière SIMPLE
     */
    enhanceTransitions() {
        const modals = document.querySelectorAll('.modal-container');
        
        modals.forEach(modal => {
            // Transitions SIMPLES
            modal.style.transition = 'all 0.3s ease-out';
            modal.style.willChange = 'transform, opacity';
            modal.style.backfaceVisibility = 'hidden';

            // Effets de survol SIMPLES
            modal.addEventListener('mouseenter', () => {
                modal.style.boxShadow = '0 15px 35px rgba(0, 0, 0, 0.2)';
            });

            modal.addEventListener('mouseleave', () => {
                modal.style.boxShadow = '0 10px 25px rgba(0, 0, 0, 0.15)';
            });
        });
    }

    /**
     * Ajouter des fermetures fluides SIMPLES
     */
    addSmoothClosing() {
        // Intercepter les clics sur les boutons de fermeture
        document.addEventListener('click', (e) => {
            const closeButton = e.target.closest('.modal-close-button');
            if (closeButton) {
                e.preventDefault();
                this.smoothCloseModal(closeButton);
            }
        });

        // Fermer avec Escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.closeAllModals();
            }
        });
    }

    /**
     * Fermer un modal de manière fluide et SIMPLE
     */
    smoothCloseModal(closeButton) {
        const modal = closeButton.closest('.modal-container');
        const backdrop = modal.previousElementSibling;
        
        if (!modal || !backdrop) return;

        // Ajouter les classes de fermeture SIMPLES
        backdrop.classList.add('closing');
        modal.classList.add('closing');

        // Attendre la fin de l'animation SIMPLE
        setTimeout(() => {
            // Déclencher la fermeture Livewire
            const wireClick = closeButton.getAttribute('wire:click');
            if (wireClick) {
                // Émettre l'événement Livewire
                Livewire.find(closeButton.closest('[wire\\:id]')?.getAttribute('wire:id')).call(wireClick);
            }
        }, 200); // Timing SIMPLE : 200ms au lieu de 600ms
    }

    /**
     * Fermer tous les modaux
     */
    closeAllModals() {
        const modals = document.querySelectorAll('.modal-container');
        modals.forEach(modal => {
            const closeButton = modal.querySelector('.modal-close-button');
            if (closeButton) {
                this.smoothCloseModal(closeButton);
            }
        });
    }

    /**
     * Améliorer les nouveaux modaux
     */
    enhanceNewModals() {
        // Attendre que le DOM soit mis à jour
        setTimeout(() => {
            this.enhanceExistingAnimations();
        }, 100);
    }

    /**
     * Optimiser les performances de manière SIMPLE
     */
    optimizePerformance() {
        // Utiliser requestAnimationFrame pour les animations
        const optimizeAnimations = () => {
            const modals = document.querySelectorAll('.modal-container');
            modals.forEach(modal => {
                // Optimisations SIMPLES
                modal.style.willChange = 'transform, opacity';
                modal.style.backfaceVisibility = 'hidden';
            });
        };

        // Optimiser après le chargement
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', optimizeAnimations);
        } else {
            optimizeAnimations();
        }

        // Optimiser lors des mises à jour Livewire
        document.addEventListener('livewire:update', () => {
            requestAnimationFrame(optimizeAnimations);
        });
    }
}

// Initialiser l'améliorateur de modaux
document.addEventListener('DOMContentLoaded', () => {
    window.modalEnhancer = new ModalEnhancer();
});

// Exporter pour utilisation dans d'autres scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ModalEnhancer;
}
