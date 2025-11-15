# Guide d'utilisation - Module Pharmacie

## Vue d'ensemble

Le module Pharmacie permet de gérer le stock de médicaments dermatologiques avec suivi des lots et dates d'expiration.

## Accès au module

1. Cliquez sur le bouton **"Pharmacie"** dans la barre de navigation principale
2. Le modal s'ouvre avec 4 onglets :
   - **Stock actuel** : Liste des médicaments avec leur stock
   - **Entrées de stock** : Ajouter de nouveaux médicaments au stock
   - **Vente directe** : Vendre des médicaments directement
   - **Historique** : Consulter tous les mouvements de stock

## Fonctionnalités

### 1. Stock actuel

**Filtres disponibles :**
- **Tous** : Affiche tous les médicaments
- **Stock faible** : Médicaments dont la quantité est inférieure au seuil minimum
- **Expirés** : Lots dont la date d'expiration est dépassée
- **Expire bientôt** : Lots qui expirent dans les 30 prochains jours

**Colonnes affichées :**
- Nom du médicament
- Quantité en stock
- Seuil minimum
- Prix d'achat moyen
- Prix de vente
- Statut (OK / Faible)

### 2. Entrées de stock

**Pour ajouter une entrée :**
1. Cliquez sur "Nouvelle entrée de stock"
2. Sélectionnez un médicament (ou créez-le d'abord dans Gestion du cabinet)
3. Renseignez :
   - Quantité
   - Prix d'achat unitaire
   - Numéro de lot (optionnel)
   - Date d'expiration (optionnel mais recommandé)
   - Fournisseur
   - Référence facture fournisseur
   - Notes
4. Cliquez sur "Enregistrer"

**Comportement :**
- Si une date d'expiration est renseignée, un lot est créé automatiquement
- Le stock global est mis à jour
- Un mouvement d'entrée est enregistré dans l'historique
- Le prix d'achat moyen est recalculé automatiquement

### 3. Vente directe

**Pour effectuer une vente :**
1. Cliquez sur "Nouvelle vente"
2. (Optionnel) Recherchez un patient
3. Parcourez la liste des médicaments disponibles
4. Cliquez sur "Ajouter" pour chaque médicament souhaité
5. Vérifiez le panier et le total
6. Cliquez sur "Finaliser la vente"

**Comportement :**
- Une facture est créée automatiquement
- Le stock est déduit selon la méthode FIFO (First In First Out)
- Les lots les plus anciens sont utilisés en premier
- Un mouvement de sortie est enregistré pour chaque lot utilisé

### 4. Historique

**Filtres disponibles :**
- Recherche par nom de médicament
- Filtre par type de mouvement (Entrées, Sorties, Ajustements)
- Filtre par date (début et fin)

**Informations affichées :**
- Date et heure du mouvement
- Médicament concerné
- Type de mouvement
- Quantité
- Prix unitaire
- Montant total
- Utilisateur ayant effectué le mouvement

## Intégration automatique avec les factures

Lorsqu'un médicament est ajouté à une facture (via le module Règlement Facture) :
1. Le système vérifie automatiquement le stock disponible
2. Si le stock est insuffisant, une erreur est affichée
3. Si le stock est suffisant :
   - Le médicament est ajouté à la facture
   - Le stock est déduit automatiquement (méthode FIFO)
   - Un mouvement de sortie est enregistré
   - Le mouvement est lié à la facture et au patient

## Alertes

Le système affiche automatiquement des alertes en haut de l'interface :

- **Stock faible** : Nombre de médicaments dont la quantité est inférieure au seuil minimum
- **Expirés** : Nombre de lots dont la date d'expiration est dépassée
- **Expire bientôt** : Nombre de lots qui expirent dans les 30 prochains jours

## Méthode FIFO (First In First Out)

Pour les sorties de stock :
1. Le système recherche tous les lots actifs avec date d'expiration
2. Les lots sont triés par date d'expiration (plus ancien en premier)
3. La quantité demandée est déduite en commençant par le lot le plus ancien
4. Si un lot est épuisé, le système passe au lot suivant
5. Si aucun lot n'a de date d'expiration, la déduction se fait directement sur le stock global

## Permissions

- **Docteur Propriétaire** : Accès complet (lecture et écriture)
- **Secrétaire** : Accès complet (lecture et écriture)
- **Docteur** : Accès en lecture seule

## Notes importantes

1. **Dates d'expiration** : Il est fortement recommandé de renseigner les dates d'expiration pour une meilleure traçabilité
2. **Seuil minimum** : Configurez un seuil minimum pour chaque médicament pour recevoir des alertes
3. **Prix** : Le prix de vente peut être défini dans le stock ou utiliser le prix de référence du médicament
4. **Lots** : Les lots permettent de suivre précisément les dates d'expiration et d'appliquer la méthode FIFO

## Prochaines améliorations possibles

- Export des mouvements de stock en Excel/PDF
- Rapports de valorisation du stock
- Statistiques de rotation des stocks
- Gestion des fournisseurs
- Commandes automatiques lorsque le stock est faible

