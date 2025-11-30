<?php

namespace App\Http\Livewire\Middleware;

use Livewire\HydrationMiddleware\HydrationMiddleware;
use Livewire\Request;

/**
 * Middleware Livewire pour corriger les problèmes avec les noms de propriétés vides
 * Ce middleware filtre les mises à jour syncInput avec des noms vides avant qu'elles n'atteignent les composants
 */
class FixLivewireActions implements HydrationMiddleware
{
    public static function hydrate($unHydratedInstance, $request)
    {
        // Intercepter les mises à jour syncInput avec des noms vides
        if (isset($request->updates) && is_array($request->updates)) {
            $request->updates = array_values(array_filter($request->updates, function($update) {
                if (isset($update['type']) && $update['type'] === 'syncInput') {
                    $name = $update['payload']['name'] ?? '';
                    // Filtrer les mises à jour avec des noms vides ou invalides
                    if (empty($name) || !is_string($name)) {
                        return false;
                    }
                    // Vérifier que le nom de propriété n'est pas vide après beforeFirstDot
                    $propertyName = head(explode('.', $name));
                    if (empty($propertyName)) {
                        return false;
                    }
                }
                return true;
            }));
        }
    }

    public static function dehydrate($instance, $response)
    {
        // Pas de modification nécessaire lors de la déshydratation
    }
}

