# Cahier des Charges — SuguPro

> ERP + POS de gestion de supermarché pour l'Afrique de l'Ouest.
> Version 1.0 — Novembre 2026 — Document contractuel de référence.

---

## 1. Objet et périmètre

### 1.1 Objet

Concevoir, développer et déployer une application web **responsive** (desktop, tablette, smartphone) de gestion complète d'un supermarché ou d'une supérette : caisse, stock, achats, clients, crédit, notifications WhatsApp/SMS, rôles et pilotage.

### 1.2 Dans le périmètre (v1.0)

- POS mono et multi-caisses, impression thermique, clôture Z.
- Catalogue produits avec codes-barres, prix détail/gros, lots et DLC.
- Stock multi-emplacements simple (magasin + rayon), inventaires.
- Achats fournisseurs, réceptions, dettes.
- Clients, ardoise (crédit), fidélité simple (points).
- Notifications WhatsApp Cloud API et SMS (fallback).
- Rôles/permissions, journal d'audit.
- Tableau de bord et rapports exportables (PDF/CSV).
- PWA installable + API REST `/api/v1` (Sanctum).

### 1.3 Hors périmètre (v1.0)

- Comptabilité générale complète (plan comptable OHADA) → v2.
- Intégration directe des API Orange Money / Wave (v1 = saisie du numéro de transaction + rapprochement manuel) → v2.
- E-commerce client final → v2.
- Gestion de paie → hors produit.
- Mode hors-ligne total (v1 = tolérance réseau sur le POS, pas d'installation complète offline).

### 1.4 Hypothèses et contraintes

| Contrainte | Valeur |
|---|---|
| Devise | XOF (FCFA), montants entiers, `DECIMAL(15,0)` ou `BIGINT` |
| Langue | Français (interface), extensible bambara/wolof |
| Fuseau | `Africa/Bamako` (UTC+0) |
| Navigateurs | Chrome/Edge ≥ 110, Safari iOS ≥ 16, Android Chrome |
| Connexion | 3G minimum, tolérance coupures |
| Impression | Thermique ESC/POS 58 mm et 80 mm via navigateur ou agent local |
| Mobile Money | Saisie référence + mode de paiement, pas d'API en v1 |
| Conformité | Protection des données personnelles (loi malienne 2013-015), RGPD-like |

---

## 2. Acteurs et rôles

| Rôle | Description | Accès |
|---|---|---|
| **Super Admin** | Éditeur SaaS, support | Tous les tenants, paramétrage global, impersonation encadrée |
| **Gérant / Propriétaire** | Responsable du magasin | Tous les modules de son tenant, rapports, prix, utilisateurs |
| **Caissier** | Encaissement | POS, consultation produits, ses propres ventes, ouverture/fermeture de sa caisse |
| **Magasinier** | Réception et stock | Produits, stock, inventaires, achats (réception), pas de prix de vente modifiable |
| **Comptable** (option) | Contrôle | Lecture des ventes/achats, exports, pas de modification |

### 2.1 Matrice de permissions (spatie/laravel-permission)

| Permission | Super Admin | Gérant | Caissier | Magasinier | Comptable |
|---|:--:|:--:|:--:|:--:|:--:|
| `products.view` | ✅ | ✅ | ✅ | ✅ | ✅ |
| `products.create/update` | ✅ | ✅ | ❌ | ✅ (hors prix vente) | ❌ |
| `products.delete` | ✅ | ✅ | ❌ | ❌ | ❌ |
| `stock.adjust` | ✅ | ✅ | ❌ | ✅ | ❌ |
| `purchases.manage` | ✅ | ✅ | ❌ | ✅ | ❌ |
| `pos.sell` | ✅ | ✅ | ✅ | ❌ | ❌ |
| `pos.discount` | ✅ | ✅ | ⚠️ plafonné | ❌ | ❌ |
| `pos.cancel` | ✅ | ✅ | ⚠️ avant validation | ❌ | ❌ |
| `cash.open/close` | ✅ | ✅ | ✅ (sa caisse) | ❌ | ❌ |
| `customers.manage` | ✅ | ✅ | ✅ (création) | ❌ | ❌ |
| `credit.grant` | ✅ | ✅ | ❌ | ❌ | ❌ |
| `reports.view` | ✅ | ✅ | ❌ | ❌ | ✅ |
| `settings.manage` | ✅ | ✅ | ❌ | ❌ | ❌ |
| `users.manage` | ✅ | ✅ | ❌ | ❌ | ❌ |

---

## 3. Exigences fonctionnelles

### 3.1 Module POS & Caisse (MPOS)

#### Fonctionnalités

- Interface tactile plein écran, grille de produits favoris, recherche instantanée (nom, code-barres, catégorie).
- Scan code-barres USB ou caméra (via `BarcodeDetector` / `zxing-js` en fallback).
- Panier : quantité, remise ligne, remise globale, TVA optionnelle (par défaut 0 %), arrondi FCFA.
- Paiement **multi-modes** : espèces, Orange Money, Wave, Moov Money, carte bancaire, crédit client, mixte (ex. 5 000 espèces + 2 000 OM).
- **Rendu de monnaie assisté** : boutons rapides 500 / 1 000 / 2 000 / 5 000 / 10 000 FCFA, calcul automatique.
- Impression ticket thermique 58/80 mm + envoi WhatsApp.
- Ouverture/fermeture de caisse (fond de caisse, comptage, écart), **Z de caisse** journalier.
- Mise en attente / reprise d'une vente (panier suspendu).
- Annulation de ligne ou de vente (tracée, motif obligatoire).
- Mode dégradé : si l'API est injoignable, les ventes sont mises en file locale (IndexedDB) puis synchronisées.

#### Règles métier

| ID | Règle |
|---|---|
| RM-POS-01 | Une vente validée est immuable ; seule une vente d'avoir peut la compenser. |
| RM-POS-02 | Le stock est décrémenté à la validation, pas à l'ajout au panier. |
| RM-POS-03 | Impossible de vendre plus que le stock disponible (sauf paramètre `allow_negative_stock = false` par défaut). |
| RM-POS-04 | Une caisse doit être ouverte pour encaisser ; une seule session ouverte par utilisateur. |
| RM-POS-05 | La remise caissier est plafonnée à 5 % (paramétrable) ; au-delà, validation gérant. |
| RM-POS-06 | Le montant total est arrondi au franc entier ; la somme des paiements doit égaler le total. |
| RM-POS-07 | Toute vente à crédit exige un client identifié et un encours sous le plafond autorisé. |
| RM-POS-08 | Le Z de caisse est généré à la clôture et n'est plus modifiable. |

#### Critères d'acceptation

- ✅ Scanner un produit l'ajoute au panier en < 300 ms.
- ✅ Une vente de 3 articles s'encaisse en < 30 s (souris/tactile).
- ✅ Le ticket contient : nom du magasin, date/heure, n° ticket, caissier, lignes, total, modes de paiement, rendu, QR code de vérification.
- ✅ Après validation, le stock affiché est immédiatement décrémenté.
- ✅ Une vente refusée pour stock insuffisant affiche un message explicite et ne crée aucune ligne.

---

### 3.2 Module Produits & Stock (MSTK)

- Catégories et sous-catégories (arborescence 2 niveaux), marques, unités (pièce, kg, L, sac 25 kg).
- Produit : SKU interne, code-barres EAN-13 (unique par tenant), nom, description, image, TVA, prix d'achat, prix détail, prix gros, seuil de quantité gros, stock min, stock max, actif.
- **Lots** : numéro de lot, date de fabrication, **DLC**, quantité, prix d'achat. Sortie FIFO/FEFO.
- Mouvements de stock typés : entrée achat, sortie vente, ajustement, retour client, retour fournisseur, perte/casse, transfert.
- Alertes : stock ≤ seuil, DLC < 30 jours, produit sans mouvement depuis 90 jours.
- Inventaire : session de comptage, saisie des quantités réelles, écart valorisé, validation avec ajustement.
- Import/export CSV et Excel du catalogue.

#### Règles métier

| ID | Règle |
|---|---|
| RM-STK-01 | Un code-barres est unique par tenant ; doublon bloqué avec suggestion du produit existant. |
| RM-STK-02 | Le stock est calculé depuis les mouvements (source de vérité), avec table de synthèse `stocks` pour la performance. |
| RM-STK-03 | Un produit avec mouvements ne peut être supprimé : désactivation uniquement. |
| RM-STK-04 | La sortie de stock suit FEFO (premier périmé, premier sorti) si le produit est suivi en lots. |
| RM-STK-05 | Un ajustement manuel exige un motif et génère une ligne d'audit. |
| RM-STK-06 | Le prix de vente ne peut être inférieur au prix d'achat sans confirmation gérant. |

---

### 3.3 Module Achats & Fournisseurs (MACH)

- Fiche fournisseur : raison sociale, NIF, téléphone, WhatsApp, adresse, conditions de paiement (comptant, 15 j, 30 j), plafond de dette.
- Bon de commande (BC) : lignes, quantités, prix négociés, statut (brouillon, envoyé, partiel, reçu, annulé).
- Réception : partielle ou totale, création de lots, mise à jour du stock et du prix d'achat moyen pondéré (option).
- Facture fournisseur et règlements (espèces, virement, Mobile Money), suivi du solde dû.
- Alertes de réassort basées sur la consommation moyenne (30/90 jours) et le délai de livraison.

#### Règles métier

| ID | Règle |
|---|---|
| RM-ACH-01 | Une réception ne peut excéder la quantité commandée (sauf option `over_receipt`). |
| RM-ACH-02 | La réception crée automatiquement les mouvements de stock d'entrée. |
| RM-ACH-03 | Le solde fournisseur = total factures − total règlements. |
| RM-ACH-04 | Un BC reçu partiellement reste ouvert jusqu'à clôture manuelle ou réception complète. |

---

### 3.4 Module Clients & Crédit (MCLI)

- Client : nom, téléphone (clé WhatsApp), WhatsApp opt-in, adresse, limite de crédit, points de fidélité.
- Historique d'achats, panier moyen, fréquence, dernière visite.
- **Ardoise numérique** : encours, échéances, règlements partiels, relances.
- Plafond de crédit par client ; blocage automatique au dépassement.
- Relances automatiques J+3, J+7, J+15 après échéance (WhatsApp puis SMS).
- Fidélité : 1 point par 1 000 FCFA dépensés, conversion en remise paramétrable.

#### Règles métier

| ID | Règle |
|---|---|
| RM-CLI-01 | Téléphone unique par tenant, normalisé au format international (`+223XXXXXXXX`). |
| RM-CLI-02 | Une vente à crédit ne peut dépasser le plafond du client. |
| RM-CLI-03 | Un client avec encours échu > 30 j passe en statut « bloqué » (vente à crédit refusée). |
| RM-CLI-04 | Chaque règlement génère un reçu et met à jour l'encours de façon transactionnelle. |

---

### 3.5 Module Notifications WhatsApp & SMS (MNOT)

- **WhatsApp Cloud API (Meta)** : envoi de templates approuvés (reçu de vente, relance crédit, alerte stock).
- **SMS** : fallback via agrégateur (Orange SMS API, Africa's Talking, Twilio).
- File d'attente (`queues`) avec 3 tentatives et backoff exponentiel.
- Journal des envois : destinataire, canal, template, statut (queued, sent, delivered, failed), coût, erreur.
- Paramétrage par tenant : numéro expéditeur, templates, activation par type d'événement.
- Limitation de débit (`throttle`) et opt-out client.

#### Règles métier

| ID | Règle |
|---|---|
| RM-NOT-01 | Aucun message n'est envoyé sans consentement (opt-in) pour les messages marketing ; les reçus transactionnels sont autorisés. |
| RM-NOT-02 | Si WhatsApp échoue 2 fois, bascule automatique sur SMS (si activé). |
| RM-NOT-03 | Un reçu WhatsApp contient un lien PDF signé valable 30 jours. |
| RM-NOT-04 | Les numéros sont normalisés et validés avant envoi. |

---

### 3.6 Module Rapports & Pilotage (MRPT)

- Tableau de bord : CA jour/semaine/mois, marge brute, nombre de tickets, panier moyen, top 10 produits, top clients, alertes.
- Rapport de caisse : journal des ventes, écarts, annulations, remises.
- Rapport de stock : valorisation au prix d'achat, rotation, produits dormants, DLC.
- Rapport achats/fournisseurs : dettes, échéances.
- Export PDF (dompdf) et CSV/Excel.
- Envoi automatique du rapport quotidien au gérant par WhatsApp à 21 h.

---

### 3.7 Module Paramètres & Administration (MADM)

- Paramètres magasin : nom, logo, adresse, téléphone, NIF, devise, TVA par défaut, message ticket, pied de ticket.
- Multi-magasins (Enterprise) : caisses, entrepôts, transferts.
- Utilisateurs : invitation, rôles, désactivation, réinitialisation de mot de passe.
- Journal d'audit : qui, quoi, quand, avant/après (sur les entités sensibles).
- Sauvegarde : export complet des données du tenant à la demande.

---

## 4. Exigences non fonctionnelles

| Catégorie | Exigence | Cible |
|---|---|---|
| Performance | Temps de réponse API (p95) | < 400 ms |
| Performance | Chargement du POS | < 2 s sur 3G |
| Disponibilité | Uptime mensuel | ≥ 99,5 % |
| Capacité | Produits par tenant | 50 000 |
| Capacité | Ventes/jour par tenant | 5 000 |
| Capacité | Utilisateurs simultanés par tenant | 20 |
| Sécurité | Mots de passe | bcrypt/argon2, min 8 caractères |
| Sécurité | Sessions | régénération à la connexion, expiration 120 min |
| Sécurité | CSRF/XSS/SQLi | protections Laravel natives, Blade échappé, Eloquent uniquement |
| Sécurité | Rate limiting | 5 tentatives/min sur login, 60/min sur API |
| Sécurité | Données sensibles | chiffrement des tokens WhatsApp/SMS en base |
| Accessibilité | Contraste | WCAG AA (4,5:1) |
| Accessibilité | Cibles tactiles | ≥ 44 × 44 px |
| Sauvegarde | Fréquence | quotidienne, rétention 30 j, test de restauration mensuel |
| Observabilité | Logs | structurés, rotation 14 j, alerte sur erreurs 500 |
| Localisation | Montants | `15 500 FCFA`, séparateur espace insécable |
| Compatibilité | Impression | 58 mm et 80 mm, ESC/POS |

---

## 5. Architecture technique

### 5.1 Stack

| Couche | Technologie | Version cible |
|---|---|---|
| Langage | PHP | ≥ 8.2 (validé 8.5.11 sous XAMPP) |
| Framework | Laravel | 13.x |
| Base de données | MySQL / MariaDB | 8.0+ / 10.4+ |
| Frontend | Blade + Bootstrap 5.3 + Bootstrap Icons | — |
| Build | Vite | 6+ |
| Auth scaffold | `laravel/ui` (Bootstrap) | 4.6+ |
| Permissions | `spatie/laravel-permission` | 7.x/8.x |
| PDF | `barryvdh/laravel-dompdf` | 3.x |
| HTTP | `guzzlehttp/guzzle` | 7.x |
| SMS | `twilio/sdk` + agrégateurs locaux | 8.x |
| API mobile | Laravel Sanctum | 4.x |
| Tests | PHPUnit + Dusk | 11/8 |
| Qualité | Pint + Larastan | — |
| CI/CD | GitHub Actions | — |

### 5.2 Architecture applicative

```
app/
├── Console/Commands/          # Rappels crédit, rapport quotidien, purge
├── Enums/                     # PaymentMethod, SaleStatus, StockMovementType...
├── Events/                    # SaleCompleted, StockLow, CreditDue
├── Exceptions/
├── Http/
│   ├── Controllers/
│   │   ├── Web/               # Dashboard, Products, POS, Purchases...
│   │   └── Api/V1/            # Endpoints Sanctum
│   ├── Middleware/            # EnsureTenant, RoleMiddleware, ThrottleByTenant
│   ├── Requests/              # FormRequest par action
│   └── Resources/             # API Resources
├── Jobs/                      # SendWhatsAppReceipt, SendSmsReminder
├── Listeners/                 # UpdateStock, NotifyLowStock
├── Models/                    # Tenant, User, Product, Sale, SaleItem...
├── Policies/                  # ProductPolicy, SalePolicy...
├── Providers/
├── Services/
│   ├── Pos/                   # CartService, CheckoutService, ReceiptService
│   ├── Stock/                 # StockService, InventoryService
│   ├── Notification/          # MessagingChannelInterface, WhatsAppService, SmsService
│   └── Reporting/             # DashboardService
└── Support/helpers.php        # format_fcfa()
```

### 5.3 Multi-tenant

- Stratégie v1 : **base unique, colonne `tenant_id`** sur toutes les tables métier + `BelongsToTenant` trait (global scope).
- Résolution du tenant par sous-domaine (`magasin.sugupro.app`) ou par `tenant_id` de l'utilisateur connecté.
- Index composites `(tenant_id, ...)` sur toutes les clés de recherche.
- Aucune requête métier sans scope tenant (test automatisé dédié).

### 5.4 Sécurité applicative

- FormRequest pour toute écriture, règles `exists`/`unique` scopées au tenant.
- Policies sur chaque modèle + middleware Spatie.
- Transactions DB sur ventes, réceptions, règlements.
- Journal d'audit via observer sur `Sale`, `StockMovement`, `Product`, `User`.
- Secrets (clés WhatsApp/SMS) chiffrés (`encrypted` cast) et jamais loggés.
- Webhooks WhatsApp avec vérification de signature `X-Hub-Signature-256`.

---

## 6. Modèle de données (v1)

### 6.1 Tables principales

| Table | Colonnes clés |
|---|---|
| `tenants` | id, name, slug, phone, address, currency, tax_rate, logo_path, settings(json), is_active |
| `users` | id, tenant_id, name, email, phone, password, is_active, last_login_at |
| `roles`, `permissions`, `model_has_roles` | spatie |
| `categories` | id, tenant_id, parent_id, name, slug, position |
| `brands` | id, tenant_id, name |
| `units` | id, tenant_id, name, symbol, allow_decimal |
| `products` | id, tenant_id, category_id, brand_id, unit_id, sku, barcode, name, description, image_path, cost_price, retail_price, wholesale_price, wholesale_min_qty, stock_min, stock_max, track_lots, is_active |
| `product_batches` | id, tenant_id, product_id, lot_number, manufactured_at, expires_at, quantity, cost_price |
| `stocks` | id, tenant_id, product_id, warehouse_id, quantity, updated_at (synthèse) |
| `warehouses` | id, tenant_id, name, type(store/rayon), is_default |
| `stock_movements` | id, tenant_id, product_id, batch_id, warehouse_id, user_id, type, quantity, unit_cost, reference_type, reference_id, reason, created_at |
| `suppliers` | id, tenant_id, name, nif, phone, whatsapp, address, payment_terms_days, credit_limit |
| `purchase_orders` | id, tenant_id, supplier_id, user_id, reference, status, ordered_at, expected_at, total |
| `purchase_order_items` | id, purchase_order_id, product_id, quantity, received_quantity, unit_cost |
| `supplier_invoices` | id, tenant_id, supplier_id, purchase_order_id, reference, amount, due_at, paid_amount, status |
| `supplier_payments` | id, supplier_invoice_id, user_id, amount, method, reference, paid_at |
| `customers` | id, tenant_id, name, phone, whatsapp, address, credit_limit, balance, loyalty_points, is_blocked |
| `sales` | id, tenant_id, cash_session_id, user_id, customer_id, reference, subtotal, discount, tax, total, paid, change_due, status, sold_at |
| `sale_items` | id, sale_id, product_id, batch_id, quantity, unit_price, discount, line_total |
| `sale_payments` | id, sale_id, method, amount, reference, received_by |
| `cash_sessions` | id, tenant_id, user_id, opened_at, closed_at, opening_float, closing_count, expected_cash, difference, status, z_report(json) |
| `inventories` | id, tenant_id, warehouse_id, user_id, reference, status, counted_at, validated_at |
| `inventory_items` | id, inventory_id, product_id, expected_qty, counted_qty, difference, unit_cost |
| `credit_transactions` | id, tenant_id, customer_id, sale_id, type(charge/payment), amount, due_at, paid_at, status |
| `notification_logs` | id, tenant_id, notifiable_type, notifiable_id, channel, recipient, template, status, provider_id, cost, error, sent_at |
| `audits` | id, tenant_id, user_id, auditable_type, auditable_id, event, old_values(json), new_values(json), ip, created_at |
| `settings` | id, tenant_id, group, key, value(json) |

### 6.2 Contraintes d'intégrité

- `UNIQUE (tenant_id, barcode)` sur `products` (si barcode non nul).
- `UNIQUE (tenant_id, sku)`.
- `UNIQUE (tenant_id, phone)` sur `customers`.
- `UNIQUE (cash_session_id, status='open')` : une seule session ouverte par caisse/utilisateur.
- FK `ON DELETE RESTRICT` pour les entités historisées (produits, clients, fournisseurs).
- Tous les montants : `DECIMAL(15,0)`, quantités : `DECIMAL(12,2)`.

---

## 7. API REST `/api/v1` (Sanctum)

| Méthode | Endpoint | Rôle | Description |
|---|---|---|---|
| POST | `/auth/login` | public | Token personnel (device_name) |
| POST | `/auth/logout` | auth | Révocation du token |
| GET | `/me` | auth | Profil + permissions |
| GET | `/products` | auth | Liste paginée + recherche `?q=` |
| GET | `/products/barcode/{code}` | caissier+ | Produit par code-barres |
| GET | `/categories` | auth | Arborescence |
| GET | `/stocks` | magasinier+ | Stock par produit/entrepôt |
| POST | `/sales` | caissier+ | Créer une vente (idempotency key) |
| GET | `/sales/{reference}` | auth | Détail + ticket |
| POST | `/cash-sessions/open` | caissier+ | Ouvrir une caisse |
| POST | `/cash-sessions/{id}/close` | caissier+ | Clôturer + Z |
| GET | `/customers` | auth | Liste + encours |
| POST | `/customers/{id}/payments` | gérant | Règlement de crédit |
| GET | `/reports/dashboard` | gérant | KPIs du jour |
| GET | `/notifications` | gérant | Journal des envois |
| POST | `/webhooks/whatsapp` | signature | Statuts Meta |

**Conventions** : JSON `{data, meta}` ; erreurs `{message, errors}` ; pagination `per_page` max 100 ; idempotence sur `POST /sales` via header `Idempotency-Key`.

---

## 8. Parcours utilisateurs clés

### 8.1 Encaissement (caissier)

1. Connexion → sélection de la caisse → ouverture avec fond de caisse.
2. Scan produit → panier → quantité/remise.
3. « Encaisser » → choix du mode → saisie du montant reçu → rendu calculé.
4. Validation → ticket imprimé + WhatsApp proposé → stock décrémenté.
5. Fin de journée → clôture → comptage → Z de caisse PDF.

### 8.2 Réception de commande (magasinier)

1. Liste des BC « envoyés » → ouvrir → « Réceptionner ».
2. Saisie des quantités reçues, lots et DLC.
3. Validation → stock incrémenté, dettes fournisseur mises à jour.
4. Écart éventuel → motif → notification gérant.

### 8.3 Relance de crédit (gérant)

1. Tableau de bord → « Créances échues ».
2. Sélection des clients → « Relancer WhatsApp ».
3. Message template pré-rempli (nom, montant, échéance, lien de paiement).
4. Journal des envois → statut livré/échec → fallback SMS.

---

## 9. Planning de réalisation (6 sprints de 2 semaines)

| Sprint | Contenu | Livrable démontrable |
|---|---|---|
| S0 (1 sem.) | Setup Laravel, XAMPP, auth, rôles, layout Bootstrap | Connexion + dashboard vide |
| S1 | Produits, catégories, unités, import CSV | CRUD catalogue |
| S2 | Stock, mouvements, lots, alertes | Stock temps réel |
| S3 | POS, panier, paiements, ticket, caisse | Vente de bout en bout |
| S4 | Achats, fournisseurs, réceptions, dettes | Réception + stock |
| S5 | Clients, crédit, fidélité, WhatsApp/SMS | Relance et reçu WhatsApp |
| S6 | Rapports, exports, PWA, API, tests, CI | Version 1.0 RC |

---

## 10. Livrables et critères de recette

### 10.1 Livrables

1. Code source Laravel versionné (GitHub), sans secret commité.
2. Base de données migrée + seeders de démonstration.
3. Documentation `/docs` (business plan, CDC, charte, installation).
4. Suite de tests (feature + unit) verte, couverture ≥ 70 % sur les services critiques.
5. CI GitHub Actions (Pint, PHPStan, PHPUnit, build Vite).
6. Guide utilisateur caissier (1 page A4) et gérant (5 pages).
7. Instance de démonstration avec jeu de données réel.

### 10.2 Recette fonctionnelle (extrait)

| # | Scénario | Résultat attendu |
|---|---|---|
| R1 | Vente de 3 produits avec stock suffisant | Vente créée, stock −3, ticket disponible |
| R2 | Vente avec stock insuffisant | Refus, message clair, aucun mouvement |
| R3 | Paiement mixte 5 000 espèces + 2 000 OM sur total 7 000 | Validé, rendu 0 |
| R4 | Paiement 10 000 sur total 7 500 | Rendu 2 500 |
| R5 | Vente à crédit au-delà du plafond | Refus, encours inchangé |
| R6 | Clôture de caisse avec écart | Écart calculé, Z généré, audit |
| R7 | Envoi WhatsApp en échec | Fallback SMS, journal `failed` puis `sent` |
| R8 | Caissier accède aux rapports | 403 |
| R9 | Produit avec DLC < 30 j | Alerte visible sur dashboard |
| R10 | Coupure API pendant une vente | Vente mise en file, rejouée à la reconnexion |

---

## 11. Maintenance et exploitation

- **Sauvegardes** : `mysqldump` quotidien + copie chiffrée hors site ; test de restauration mensuel.
- **Mises à jour** : fenêtre dimanche 02 h – 04 h, migrations réversibles, tag Git.
- **Monitoring** : uptime, erreurs 500, taille DB, files d'attente bloquées.
- **Support** : WhatsApp Business, SLA 4 h ouvrées (Pro), 2 h (Enterprise).
- **Évolution** : revue produit mensuelle basée sur les KPIs et retours terrain.
