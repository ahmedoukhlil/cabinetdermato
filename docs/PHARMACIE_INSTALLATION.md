# Instructions d'installation - Module Pharmacie

## Étapes d'installation

### 1. Exécuter les migrations

Exécutez les migrations pour créer les tables nécessaires :

```bash
php artisan migrate
```

Les migrations suivantes seront exécutées :
- `2025_11_14_213247_create_stock_medicaments_table.php`
- `2025_11_14_213322_create_lots_medicaments_table.php`
- `2025_11_14_213307_create_mouvements_stock_table.php`

### 2. Vérifier les modèles

Les modèles suivants ont été créés :
- `app/Models/StockMedicament.php`
- `app/Models/LotMedicament.php`
- `app/Models/MouvementStock.php`

### 3. Vérifier les composants

Le composant Livewire suivant a été créé :
- `app/Http/Livewire/PharmacieManager.php`
- `resources/views/livewire/pharmacie-manager.blade.php`

### 4. Vider le cache (si nécessaire)

```bash
php artisan cache:clear
php artisan view:clear
php artisan config:clear
```

## Structure de la base de données

### Table `stock_medicaments`
- Stock global par médicament et cabinet
- Gestion des seuils minimums
- Prix d'achat et de vente

### Table `lots_medicaments`
- Gestion des lots avec dates d'expiration
- Suivi des quantités par lot
- Informations fournisseur

### Table `mouvements_stock`
- Historique complet de tous les mouvements
- Liaison avec les factures
- Traçabilité complète

## Fonctionnalités implémentées

✅ Gestion des entrées de stock avec lots et dates d'expiration
✅ Gestion des sorties de stock (vente directe)
✅ Déduction automatique lors des ventes via factures
✅ Système d'alertes (stock faible, expirés, expire bientôt)
✅ Méthode FIFO pour les sorties
✅ Historique complet des mouvements
✅ Interface utilisateur complète avec onglets

## Test du système

### Test 1 : Créer une entrée de stock
1. Ouvrir l'onglet Pharmacie
2. Aller dans "Entrées de stock"
3. Ajouter un médicament avec quantité, prix, date d'expiration
4. Vérifier que le stock est mis à jour

### Test 2 : Vente directe
1. Aller dans "Vente directe"
2. Ajouter des médicaments au panier
3. Finaliser la vente
4. Vérifier que le stock est déduit

### Test 3 : Vente via facture
1. Créer une facture pour un patient
2. Ajouter un médicament à la facture
3. Vérifier que le stock est automatiquement déduit

### Test 4 : Alertes
1. Créer un médicament avec un stock faible
2. Vérifier que l'alerte s'affiche
3. Créer un lot avec date d'expiration proche
4. Vérifier l'alerte "expire bientôt"

## Notes importantes

- Les migrations doivent être exécutées dans l'ordre
- Le système utilise la méthode FIFO pour les sorties
- Les dates d'expiration sont optionnelles mais recommandées
- Le stock est géré par cabinet (un seul cabinet par instance)

## Support

En cas de problème :
1. Vérifier les logs : `storage/logs/laravel.log`
2. Vérifier que les migrations ont bien été exécutées
3. Vérifier les permissions des utilisateurs

