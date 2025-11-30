<?php

/**
 * Correctif pour Livewire str() helper
 * Ce fichier doit être chargé AVANT que Livewire ne charge sa propre fonction str()
 */

namespace Livewire;

use Illuminate\Support\Str;

// Redéfinir la fonction str() de Livewire avec le correctif
if (!function_exists('Livewire\str')) {
    function str($string = null)
    {
        if (is_null($string)) {
            return new class {
                public function __toString()
                {
                    return '';
                }
                
                public function __call($method, $params) {
                    // Gérer __toString() comme cas spécial
                    if ($method === '__toString') {
                        return '';
                    }
                    
                    // Vérifier si la méthode existe dans Str
                    if (!method_exists(Str::class, $method)) {
                        throw new \BadMethodCallException("Method [{$method}] does not exist on Str class.");
                    }
                    
                    $reflection = new \ReflectionMethod(Str::class, $method);
                    $requiredParams = $reflection->getNumberOfRequiredParameters();
                    
                    // Si la méthode nécessite plus de paramètres que fournis
                    if ($requiredParams > count($params)) {
                        // Méthodes spéciales qui peuvent être appelées sur un objet Stringable vide
                        $stringableMethods = ['studly', 'contains', 'startsWith', 'endsWith', 'after', 'before', 'afterLast', 'beforeLast', 'replace', 'replaceFirst', 'replaceLast', 'kebab', 'camel', 'snake', 'upper', 'lower', 'ucfirst', 'lcfirst'];
                        
                        if (in_array($method, $stringableMethods)) {
                            // Retourner un objet Stringable vide pour permettre les chaînages
                            return Str::of('');
                        }
                        
                        // Pour les autres méthodes, lancer une exception
                        throw new \ArgumentCountError("Too few arguments to function Illuminate\Support\Str::{$method}(), " . count($params) . " passed and exactly {$requiredParams} expected");
                    }
                    
                    // Appeler la méthode statique avec les paramètres
                    return Str::$method(...$params);
                }
            };
        }

        return Str::of($string);
    }
}

