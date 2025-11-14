# Ajustements de la Base de Données

## 1. Table `medicaments`

### Ajout de colonne

```sql
ALTER TABLE `medicaments` 
ADD COLUMN `PrixRef` DOUBLE DEFAULT 0 AFTER `LibelleMedic`;
```

**Structure avant :**
```
medicaments
├── IDMedic (PK)
├── LibelleMedic
└── fkidtype
```

**Structure après :**
```
medicaments
├── IDMedic (PK)
├── LibelleMedic
├── PrixRef (NOUVEAU) ← double, default 0
└── fkidtype
```

---

## 2. Table `detailfacturepatient`

### Ajout de colonne

```sql
ALTER TABLE `detailfacturepatient` 
ADD COLUMN `fkidmedicament` INT UNSIGNED NULL AFTER `fkidacte`;
```

**Structure avant :**
```
detailfacturepatient
├── idDetfacture (PK)
├── fkidfacture
├── fkidacte
├── IsAct
└── ...
```

**Structure après :**
```
detailfacturepatient
├── idDetfacture (PK)
├── fkidfacture
├── fkidacte
├── fkidmedicament (NOUVEAU) ← INT UNSIGNED NULL
├── IsAct
└── ...
```

---

## 3. Logique `IsAct` dans `detailfacturepatient`

| IsAct | Type | Colonne utilisée | Relation |
|-------|------|------------------|----------|
| 1 | Acte | `fkidacte` | `actes.ID` |
| 2 | Médicament | `fkidmedicament` | `medicaments.IDMedic` (fkidtype=1) |
| 3 | Analyse | `fkidmedicament` | `medicaments.IDMedic` (fkidtype=2) |
| 4 | Radio | `fkidmedicament` | `medicaments.IDMedic` (fkidtype=3) |

---

## 4. Relations Eloquent ajoutées

### `Detailfacturepatient` Model

```php
// Relation existante
public function acte()
{
    return $this->belongsTo(Acte::class, 'fkidacte', 'ID');
}

// Relation ajoutée
public function medicament()
{
    return $this->belongsTo(Medicament::class, 'fkidmedicament', 'IDMedic');
}
```

### `Medicament` Model

```php
// Propriétés ajoutées
protected $casts = [
    'PrixRef' => 'float'
];

protected $fillable = [
    'PrixRef'
];
```

---

## 5. Schéma complet des relations

```
facture (1) ──< (N) detailfacturepatient (N) >── (1) actes
                                    │
                                    │ (N) >── (1) medicaments
                                    │              ├── fkidtype = 1 → Médicament
                                    │              ├── fkidtype = 2 → Analyse
                                    │              └── fkidtype = 3 → Radio
```

---

## 6. Migrations créées

1. **2025_11_14_012554_add_medicament_support_to_detailfacturepatient_table.php**
   - Ajoute `fkidmedicament` à `detailfacturepatient`

2. **2025_11_14_014310_add_prix_ref_to_medicaments_table.php**
   - Ajoute `PrixRef` à `medicaments`

