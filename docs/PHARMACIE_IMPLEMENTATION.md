# Documentation - Implémentation Module Pharmacie

## Vue d'ensemble

Le module Pharmacie permet de gérer le stock de médicaments dermatologiques avec :
- Gestion des entrées de stock (achats)
- Gestion des sorties de stock (ventes)
- Suivi des lots avec dates d'expiration
- Alertes de stock faible et expiration
- Vente directe depuis l'onglet Pharmacie
- Intégration automatique avec les factures

## Structure de la base de données

### Tables créées

1. **stock_medicaments** : Stock global par médicament et cabinet
2. **lots_medicaments** : Gestion des lots avec dates d'expiration
3. **mouvements_stock** : Historique de tous les mouvements

## Fonctionnalités principales

### 1. Onglet "Stock actuel"
- Liste des médicaments avec leur stock disponible
- Filtres : Tous, Stock faible, Expirés, Expire bientôt
- Recherche par nom de médicament
- Affichage : Nom, Stock, Seuil min, Prix achat, Prix vente, Statut

### 2. Onglet "Entrées de stock"
- Formulaire d'ajout d'entrée :
  - Sélection/Création médicament
  - Quantité
  - Prix d'achat unitaire
  - Numéro de lot (optionnel)
  - Date d'expiration (optionnel)
  - Fournisseur
  - Référence facture fournisseur
- Création automatique d'un lot si date d'expiration renseignée
- Mise à jour automatique du stock

### 3. Onglet "Vente directe"
- Sélection patient (optionnel)
- Ajout de médicaments au panier
- Vérification du stock disponible
- Création automatique d'une facture
- Déduction automatique du stock (FIFO pour les lots)
- Génération d'un reçu

### 4. Onglet "Historique"
- Liste de tous les mouvements (entrées, sorties, ajustements)
- Filtres par type, date, médicament
- Détails de chaque mouvement

### 5. Alertes
- Stock faible (quantité <= seuil minimum)
- Lots expirés
- Lots qui expirent bientôt (30 jours)

## Intégration avec les factures

Lorsqu'un médicament est ajouté à une facture (via ConsultationForm) :
1. Vérification du stock disponible
2. Si stock suffisant, création automatique d'un mouvement SORTIE
3. Déduction du stock (FIFO : lots les plus anciens en premier)
4. Liaison du mouvement avec la facture et le détail de facture

## Logique FIFO (First In First Out)

Pour les sorties de stock :
1. Recherche des lots actifs avec date d'expiration
2. Tri par date d'expiration (plus ancien en premier)
3. Déduction de la quantité demandée en commençant par le lot le plus ancien
4. Si un lot est épuisé, passage au lot suivant

## Permissions

- **Docteur Propriétaire** : Accès complet
- **Secrétaire** : Accès complet
- **Docteur** : Lecture seule (peut voir le stock mais pas modifier)

## Routes

```php
Route::middleware(['permission:pharmacie.view'])->group(function () {
    // Routes pour la pharmacie
});
```

## Prochaines étapes

1. ✅ Migrations créées
2. ✅ Modèles Eloquent créés
3. ⏳ Composant Livewire PharmacieManager (en cours)
4. ⏳ Intégration dans AccueilPatient
5. ⏳ Vues Blade
6. ⏳ Intégration avec ConsultationForm pour déduction automatique
7. ⏳ Tests et ajustements

