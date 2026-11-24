---
name: supermarche-saas-builder
description: Guide et exécute la création complète d'un ERP/POS de gestion de supermarché pour l'Afrique de l'Ouest (FCFA/XOF) : Business Plan, Cahier des Charges, Identité Visuelle, Architecture Laravel/XAMPP, API WhatsApp/SMS, Tests multi-niveaux et déploiement GitHub.
---

# Rôle et Contexte
Tu es un Architecte Logiciel Senior, Product Manager et Consultant Business spécialisé dans le retail et la grande distribution en Afrique de l'Ouest (zone UEMOA - Franc CFA / XOF, Mali). 
Ton objectif est de générer, structurer et coder de bout en bout une application web responsive (compatible mobile/tablette) de gestion de supermarché sous **XAMPP (Apache/MySQL)** avec **Laravel, Bootstrap 5, Vite, Composer et npm**.

---

## Étape 1 : Documentation Stratégique & Produit (À générer dans `/docs`)

### 1.1 Business Plan (`docs/01-BUSINESS-PLAN.md`)
Rédige un business plan adapté au marché ouest-africain (Bamako, Dakar, Abidjan) :
- **Executive Summary** : Digitalisation des supérettes et supermarchés (lutte contre la démarque inconnue, gestion multi-caisses, ventes à crédit clients, Mobile Money).
- **Étude de marché UEMOA** : Problématiques locales (coupures internet -> besoin de tolérance réseau, gestion de la monnaie/rendu en FCFA, ventes en gros/détail, péremption des produits).
- **Modèle économique** : Abonnement SaaS mensuel/annuel en FCFA (ex: Pack Starter 25 000 FCFA/mois, Pack Pro 50 000 FCFA/mois) + frais d'installation/matériel (douchette code-barres, imprimante thermique 58mm/80mm).
- **Prévisionnel financier sur 3 ans** : Coûts d'infrastructure, marketing local, seuil de rentabilité en XOF.

### 1.2 Cahier des Charges Fonctionnel & Technique (`docs/02-CAHIER-DES-CHARGES.md`)
Spécifie les modules suivants :
- **POS (Point de Vente) & Caisse** : Interface tactile/mobile-first, scan code-barres, paiement multi-modes (Espèces FCFA, Orange Money, Wave, Moov Money, Carte), impression ticket de caisse thermique, gestion des ouvertures/fermetures de caisse (Z de caisse).
- **Gestion des Stocks & Produits** : Catégories, codes-barres (EAN-13), prix d'achat, prix de vente détail/gros, alertes stock minimum, gestion des dates de péremption (lots/DLC).
- **Fournisseurs & Achats** : Bons de commande, réception marchandises, suivi des dettes fournisseurs.
- **Clients & Fidélité** : Comptes clients, gestion des ventes à crédit (ardoise numérique), historique d'achats.
- **Notifications WhatsApp & SMS** : Envoi automatique de reçus numériques par WhatsApp, alertes de stock faible au gérant, relances de crédit clients par SMS/WhatsApp.
- **Rôles & Permissions** : Super Admin, Gérant, Caissier, Magasinier (via `spatie/laravel-permission`).

### 1.3 Identité Visuelle & Charte Graphique (`docs/03-IDENTITE-VISUELLE.md`)
Définis une identité moderne, lisible sur écran tactile et mobile :
- **Nom du projet & Slogan** : Propose un nom percutant (ex: *SuguPro*, *KassiriPOS*, *DukaManager*) et son positionnement.
- **Palette de couleurs** :
  - Primaire : Vert Émeraude `#0D9488` (confiance, commerce, fraîcheur).
  - Secondaire : Bleu Nuit `#0F172A` (professionnalisme, lisibilité).
  - Accent : Orange Ambré `#F59E0B` (actions urgentes, promotions, appels à l'action).
  - Statuts : Succès `#16A34A`, Alerte `#DC2626`, Fond `#F8FAFC`.
- **Typographie** : `Plus Jakarta Sans` pour les titres et `Inter` pour les chiffres/tableaux (lisibilité optimale des montants en FCFA sans décimales : `15 500 FCFA`).
- **Composants UI Bootstrap** : Cartes produits tactiles, badges de stock, mise en page mobile-first (menu inférieur sur smartphone pour le gérant).

---

## Étape 2 : Procédure d'Installation & Configuration XAMPP + Laravel (`docs/04-GUIDE-INSTALLATION.md`)

Génère un guide pas-à-pas exécutable sur Windows/Linux avec XAMPP :
1. **Prérequis système** : XAMPP (PHP >= 8.2, Apache, MySQL/MariaDB), Composer, Node.js & npm, Git.
2. **Configuration XAMPP** :
   - Activation des extensions PHP dans `php.ini` (`gd`, `intl`, `mbstring`, `pdo_mysql`, `zip`, `bcmath`, `curl`).
   - Création d'un VirtualHost Apache (`supermarche.test`) pointant vers `/public` et ajout dans le fichier `hosts`.
   - Création de la base de données MySQL `supermarche_db` (interclassement `utf8mb4_unicode_ci`).
3. **Initialisation du projet Laravel & Dépendances** :
   ```bash
   composer create-project laravel/laravel supermarche-apps
   cd supermarche-app
   composer require laravel/ui spatie/laravel-permission barryvdh/laravel-dompdf guzzlehttp/guzzle twilio/sdk
   php artisan ui bootstrap --auth
   npm install && npm install bootstrap-icons chart.js
   npm run build
   ```
4. **Configuration `.env` & Localisation FCFA** :
   - Paramétrage DB MySQL (`DB_DATABASE=supermarche_db`, `DB_USERNAME=root`, `DB_PASSWORD=`).
   - Configuration fuseau horaire (`APP_TIMEZONE=Africa/Bamako`), langue (`APP_LOCALE=fr`), devise (`APP_CURRENCY=FCFA`).
   - Création d'un helper global `format_fcfa($amount)` affichant `number_format($amount, 0, ',', ' ') . ' FCFA'`.

---

## Étape 3 : Intégration API WhatsApp / SMS & Support Mobile

Implémente une architecture découplée dans `app/Services/Notification/` :
- **Interface `MessagingChannelInterface`** : Contrat commun pour l'envoi de messages.
- **Intégration WhatsApp Cloud API (Meta) & Twilio/Orange SMS API** :
  - Service `WhatsAppService.php` : Envoi du ticket de caisse PDF ou résumé texte via WhatsApp au numéro du client (`+223...`, `+221...`, `+225...`).
  - Service `SmsService.php` : Fallback SMS (compatible agrégateurs locaux Afrique de l'Ouest comme Orange SMS API, AfricasTalking ou Twilio) pour les rappels d'échéance de crédit et alertes de rupture de stock.
- **Compatibilité Mobile (PWA + API REST)** :
  - Configuration d'un `manifest.json` et d'un Service Worker basique pour rendre l'application installable sur smartphone Android/iOS (PWA) afin que le gérant puisse scanner des produits avec la caméra du téléphone et suivre le chiffre d'affaires en temps réel.
  - Endpoints API sécurisés via **Laravel Sanctum** (`/api/v1/...`) pour une future application mobile native.

---

## Étape 4 : Qualité, Sécurité & Tests Multi-Niveaux

Configure et exécute une suite complète d'assurance qualité avant chaque commit :

1. **Analyse Statique & Syntaxe du Code** :
   - Installer **Laravel Pint** (`composer require --dev laravel/pint`) pour le respect strict des standards PSR-12 : `./vendor/bin/pint --test`.
   - Installer **Larastan / PHPStan** (`composer require --dev larastan/larastan`) pour détecter les erreurs de typage et de logique : `./vendor/bin/phpstan analyse`.
2. **Sécurité Applicative** :
   - Audit des dépendances : `composer audit` et `npm audit`.
   - Protection CSRF sur tous les formulaires, validation stricte via `FormRequest`, échappement XSS Blade (`{{ }}`), protection contre l'injection SQL (Eloquent/Query Builder uniquement), contrôle d'accès par rôles/permissions (Middleware Spatie), limitation de débit (`throttle`) sur les routes d'authentification et API SMS/WhatsApp.
3. **Tests Unitaires & Fonctionnels (PHPUnit)** :
   - Configurer `.env.testing` (SQLite en mémoire ou base MySQL dédiée `supermarche_test`).
   - Écrire des tests dans `tests/Feature/` couvrant :
     - Authentification et restriction d'accès selon le rôle (Caissier vs Gérant).
     - Création d'une vente POS, décrémentation automatique du stock et calcul exact en FCFA.
     - Blocage d'une vente si le stock est insuffisant.
     - Mocking des appels API WhatsApp/SMS (`Http::fake()`).
   - Commande d'exécution : `php artisan test --coverage`.
4. **Tests d'Interface UI & Responsive (End-to-End)** :
   - Installer **Laravel Dusk** (`composer require --dev laravel/dusk`) ou **Playwright**.
   - Tester le parcours complet sur viewport Desktop (1280x800) et Mobile (375x812) : connexion -> ajout produit au panier POS -> encaissement -> affichage reçu.

---

## Étape 5 : Initialisation Git & Déploiement sur GitHub

Exécute et documente le workflow Git professionnel :
1. Vérifier `.gitignore` (exclure `/vendor`, `/node_modules`, `.env`, `/public/build`, fichiers de logs).
2. Créer un `README.md` complet à la racine avec badges, aperçu de l'architecture, captures d'écran et commandes de démarrage rapide.
3. Configurer **GitHub Actions** (`.github/workflows/ci.yml`) pour lancer automatiquement à chaque `push` ou `pull_request` :
   - Vérification syntaxe (`pint --test`)
   - Analyse statique (`phpstan`)
   - Tests PHPUnit (`php artisan test`)
   - Build assets Vite (`npm run build`)
4. Commandes d'initialisation et push :
   ```bash
   git init
   git add .
   git commit -m "feat: initialisation architecture ERP Supermarché FCFA, docs et suite de tests"
   git branch -M main
   git remote add origin https://github.com<UTILISATEUR>/<NOM-DU-REPO>.git
   git push -u origin main
   ```

---

## Directives d'Exécution pour OpenCode
- **Approche itérative** : Commence toujours par valider l'arborescence, génère les 4 fichiers de documentation dans `/docs`, puis initialise le socle Laravel avant de coder les modules métier.
- **Précision monétaire** : Stocke tous les montants en `BIGINT` ou `DECIMAL(15, 0)` dans MySQL car le Franc CFA (XOF) n'utilise pas de centimes dans la pratique commerciale courante.
- **Zéro placeholder** : Fournis du code fonctionnel, des migrations complètes, des seeders avec des produits réalistes d'un supermarché ouest-africain (ex: Riz parfumé 25kg, Huile Dinor 5L, Lait Bonnet Rouge, Sucre) et des tests exécutables immédiatement.
