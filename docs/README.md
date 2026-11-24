# SuguPro — Documentation

> ERP + POS de gestion de supermarché pour l'Afrique de l'Ouest (FCFA / XOF).
> Stack : Laravel 13 · Bootstrap 5 · Vite · MySQL/MariaDB · XAMPP

## Sommaire

| # | Document | Contenu |
|---|---|---|
| 01 | [Business Plan](./01-BUSINESS-PLAN.md) | Marché UEMOA, modèle SaaS FCFA, prévisionnel 3 ans, go-to-market, risques |
| 02 | [Cahier des charges](./02-CAHIER-DES-CHARGES.md) | Modules, règles métier, modèle de données, API, planning, recette |
| 03 | [Identité visuelle](./03-IDENTITE-VISUELLE.md) | Marque, palette, typographie, composants, écrans, design tokens |
| 04 | [Guide d'installation](./04-GUIDE-INSTALLATION.md) | XAMPP, VirtualHost, base de données, Laravel, FCFA, dépannage |

## Démarrage rapide

```powershell
# 1. Base de données
& "C:\xampp\mysql\bin\mysql.exe" -u root -e "CREATE DATABASE supermarche_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 2. Dépendances
composer install
npm install

# 3. Configuration
copy .env.example .env
php artisan key:generate
php artisan migrate --seed

# 4. Assets et serveur
npm run build
php artisan serve
```

Application : `http://127.0.0.1:8000` (ou `http://supermarche.test` via Apache).

## Conventions du projet

- **Montants** : entiers en FCFA (`DECIMAL(15,0)` / `BIGINT`), affichage via `format_fcfa()`.
- **Langue** : interface en français, fuseau `Africa/Bamako`.
- **Multi-tenant** : colonne `tenant_id` + global scope sur tous les modèles métier.
- **Sécurité** : FormRequest, Policies, Spatie Permissions, CSRF, `throttle`.
- **Qualité** : Pint (PSR-12), Larastan niveau 5, PHPUnit, CI GitHub Actions.
