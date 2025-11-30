<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Livewire\LifecycleManager;
use App\Http\Livewire\Middleware\FixLivewireActions;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Charger le correctif Livewire AVANT que Livewire ne charge sa propre fonction str()
        // Ce fichier doit être chargé très tôt dans le processus de démarrage
        require_once app_path('Helpers/LivewireStrFix.php');
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        
        // Enregistrer le middleware Livewire pour corriger les problèmes avec les noms de propriétés vides
        // Ce middleware doit être exécuté AVANT PerformDataBindingUpdates pour filtrer les mises à jour invalides
        // On l'enregistre comme middleware d'hydratation initial pour qu'il s'exécute en premier
        LifecycleManager::registerInitialHydrationMiddleware([
            [FixLivewireActions::class, 'hydrate'],
        ]);
    }
}
