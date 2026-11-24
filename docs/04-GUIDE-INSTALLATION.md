# Guide d'Installation — SuguPro sous XAMPP (Windows)

> Ce guide correspond à l'environnement réel détecté sur ce poste :
> **PHP 8.5.11** (`C:\xampp\php\php.exe`), **Composer 2.10.3**, **Node.js 26.9.0**, **npm 12.2.0**, **MariaDB 10.4.32**, **Git 2.55**, XAMPP installé dans `C:\xampp`.
> Projet : `C:\Aboubacrine BAH\NexaWeb Mali - Gestion supermarche - laravel`

> **État de l'installation (21/11/2026)** : Laravel **13.43**, base `supermarche_db` migrée, rôles seedés, VirtualHost `http://supermarche.test` actif, services **Apache2.4** et **mysql** démarrés.
> Compte admin : `admin@sugupro.test` / `MotDePasse!2026` (à changer avant toute mise en ligne).
> Point connu : l'extension `curl` ne se charge pas sous Apache (DLL OpenSSL 2023 d'Apache vs PHP 8.5 de 2026). Guzzle bascule automatiquement sur les *streams* PHP ; les appels HTTP fonctionnent. Voir la section Dépannage.

---

## 0. Vue d'ensemble

```
C:\Aboubacrine BAH\NexaWeb Mali - Gestion supermarche - laravel\
├── app\                  ← code Laravel
├── bootstrap\
├── config\
├── database\
├── docs\                 ← documentation (déjà générée)
├── public\               ← racine web Apache
├── resources\
├── routes\
├── storage\
├── tests\
├── .env
├── composer.json
├── package.json
└── vite.config.js
```

Deux modes d'exécution possibles :

| Mode | URL | Usage |
|---|---|---|
| **Apache (XAMPP) + VirtualHost** | `http://supermarche.test` | Mode recommandé, proche de la production |
| **Serveur intégré Laravel** | `http://127.0.0.1:8000` | Développement rapide, sans toucher à Apache |

---

## 1. Vérification des prérequis

Ouvrir **PowerShell** et exécuter :

```powershell
php -v
composer --version
node -v
npm -v
git --version
& "C:\xampp\mysql\bin\mysql.exe" --version
```

Résultat attendu (relevé sur ce poste) :

```
PHP 8.5.11 (cli) ...
Composer version 2.10.3 ...
v26.9.0
12.2.0
git version 2.55.0.windows.5
Ver 15.1 Distrib 10.4.32-MariaDB
```

Vérifier que `php` pointe bien vers XAMPP :

```powershell
where.exe php
# C:\xampp\php\php.exe
```

Vérifier les extensions indispensables :

```powershell
php -m | Select-String "gd|intl|mbstring|pdo_mysql|zip|bcmath|curl|fileinfo|openssl"
```

Toutes doivent apparaître. Si `php` n'est pas reconnu, ajouter `C:\xampp\php` au `PATH` :

```powershell
[Environment]::SetEnvironmentVariable(
  "Path",
  [Environment]::GetEnvironmentVariable("Path","Machine") + ";C:\xampp\php",
  "Machine"
)
```
Puis fermer et rouvrir PowerShell.

---

## 2. Configuration de PHP (`php.ini`)

Fichier : `C:\xampp\php\php.ini`

### 2.1 Extensions

Retirer le `;` devant les lignes suivantes si elles sont commentées :

```ini
extension=gd
extension=intl
extension=mbstring
extension=pdo_mysql
extension=zip
extension=bcmath
extension=curl
extension=fileinfo
extension=openssl
```

### 2.2 Réglages recommandés

```ini
memory_limit = 512M
upload_max_filesize = 20M
post_max_size = 24M
max_execution_time = 120
date.timezone = Africa/Bamako
```

> Ne pas modifier `extension_dir` : il doit rester `C:\xampp\php\ext`.

Redémarrer Apache depuis le **XAMPP Control Panel** après toute modification.

---

## 3. Configuration d'Apache et du VirtualHost

### 3.1 Vérifier le port 80

XAMPP Control Panel → Apache → **Start**. Si le démarrage échoue :

```powershell
netstat -ano | Select-String ":80 "
```

Le PID affiché peut être occupé par IIS, Skype ou un autre service. Changer le port Apache en **8080** (`httpd.conf` : `Listen 8080`, `ServerName localhost:8080`) et utiliser `http://supermarche.test:8080`. Sinon, arrêter le service concurrent.

### 3.2 Autoriser les VirtualHosts

Fichier : `C:\xampp\apache\conf\httpd.conf`

Décommenter (ou ajouter) :

```apache
Include conf/extra/httpd-vhosts.conf
```

Vérifier que `mod_rewrite` est actif :

```apache
LoadModule rewrite_module modules/mod_rewrite.so
```

### 3.3 Déclarer le site

Fichier : `C:\xampp\apache\conf\extra\httpd-vhosts.conf`

```apache
<VirtualHost *:80>
    ServerName supermarche.test
    DocumentRoot "C:/Aboubacrine BAH/NexaWeb Mali - Gestion supermarche - laravel/public"

    <Directory "C:/Aboubacrine BAH/NexaWeb Mali - Gestion supermarche - laravel/public">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog "logs/supermarche-error.log"
    CustomLog "logs/supermarche-access.log" combined
</VirtualHost>
```

> Utiliser des **slashes** `/` dans les chemins Apache, même sous Windows.
> `AllowOverride All` est indispensable pour `.htaccess` (routes Laravel).

### 3.4 Fichier `hosts`

Ouvrir le **Bloc-notes en administrateur** sur :

```
C:\Windows\System32\drivers\etc\hosts
```

Ajouter à la fin :

```
127.0.0.1    supermarche.test
::1          supermarche.test
```

Tester :

```powershell
ping supermarche.test
```

Puis redémarrer Apache et ouvrir `http://supermarche.test` (une page 403/404 est normale tant que Laravel n'est pas installé).

### 3.5 (Optionnel) HTTPS local

Générer un certificat auto-signé avec `mkcert` :

```powershell
choco install mkcert -y
mkcert -install
mkcert supermarche.test
```

Déclarer un `<VirtualHost *:443>` avec `SSLEngine on`, `SSLCertificateFile` et `SSLCertificateKeyFile`, puis `Listen 443` dans `httpd-ssl.conf`.

---

## 4. Création de la base de données

### 4.1 Via phpMyAdmin

1. Ouvrir `http://localhost/phpmyadmin`.
2. Onglet **Bases de données**.
3. Nom : `supermarche_db`, interclassement : `utf8mb4_unicode_ci`, cliquer **Créer**.
4. Créer aussi `supermarche_test` (même interclassement) pour PHPUnit.

### 4.2 Via la ligne de commande (recommandé)

```powershell
& "C:\xampp\mysql\bin\mysql.exe" -u root -e "CREATE DATABASE IF NOT EXISTS supermarche_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci; CREATE DATABASE IF NOT EXISTS supermarche_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

Vérification :

```powershell
& "C:\xampp\mysql\bin\mysql.exe" -u root -e "SHOW DATABASES;"
```

> Le compte `root` de XAMPP n'a pas de mot de passe par défaut. En production, créer un utilisateur dédié avec un mot de passe fort.

---

## 5. Initialisation du projet Laravel

Le dossier racine contient déjà `docs/` et `.opencode/`, donc `composer create-project` ne peut pas écrire directement dedans. Deux méthodes.

### Méthode A — Laravel à la racine (recommandée)

Créer le projet dans un dossier temporaire, puis déplacer son contenu à la racine.

```powershell
cd "C:\Aboubacrine BAH\NexaWeb Mali - Gestion supermarche - laravel"

# 1. Création dans un dossier temporaire
composer create-project laravel/laravel _laravel_tmp --prefer-dist

# 2. Déplacement de tout le contenu (y compris fichiers cachés)
robocopy "_laravel_tmp" "." /E /MOVE /NFL /NDL /NJH /NJS /NP

# 3. Nettoyage du dossier vide
Remove-Item "_laravel_tmp" -Recurse -Force -ErrorAction SilentlyContinue

# 4. Vérification
Get-ChildItem -Force | Select-Object Name
```

Résultat attendu : `app`, `bootstrap`, `config`, `database`, `public`, `resources`, `routes`, `storage`, `tests`, `vendor`, `.env`, `artisan`, etc., **à côté** de `docs` et `.opencode`.

### Méthode B — Application dans un sous-dossier

```powershell
composer create-project laravel/laravel supermarche-app --prefer-dist
```

Le `DocumentRoot` du VirtualHost devient alors :

```apache
DocumentRoot "C:/Aboubacrine BAH/NexaWeb Mali - Gestion supermarche - laravel/supermarche-app/public"
```

> La méthode A est retenue pour la suite du guide.

### 5.1 Vérification

```powershell
php artisan --version
# Laravel Framework 13.x
```

---

## 6. Installation des dépendances

### 6.1 Packages Composer

```powershell
composer require laravel/ui spatie/laravel-permission barryvdh/laravel-dompdf guzzlehttp/guzzle twilio/sdk
composer require laravel/sanctum
composer require --dev laravel/pint larastan/larastan
```

> `laravel/sanctum` est souvent déjà présent dans Laravel 13 ; la commande est sans effet si c'est le cas.

### 6.2 Scaffolding Bootstrap + authentification

```powershell
php artisan ui bootstrap --auth
```

Cette commande génère :
- les vues `resources/views/auth/*` (login, register, mot de passe oublié) ;
- `resources/sass/app.scss` ;
- les routes d'authentification dans `routes/web.php` ;
- `Auth::routes()`.

### 6.3 Packages npm

```powershell
npm install
npm install bootstrap@5.3 bootstrap-icons @fontsource/plus-jakarta-sans @fontsource/inter chart.js
npm install -D sass
npm run build
```

> Si `npm run build` échoue avec « Cannot find module sass », c'est que `sass` n'est pas installé : relancer `npm install -D sass`.

### 6.4 Publication des assets des packages

```powershell
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan vendor:publish --provider="Barryvdh\DomPDF\ServiceProvider"
```

---

## 7. Configuration de l'application

### 7.1 Fichier `.env`

```dotenv
APP_NAME=SuguPro
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://supermarche.test

APP_LOCALE=fr
APP_FALLBACK_LOCALE=fr
APP_FAKER_LOCALE=fr_FR
APP_TIMEZONE=Africa/Bamako
APP_CURRENCY=FCFA

LOG_CHANNEL=stack
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=supermarche_db
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=database
SESSION_LIFETIME=120
QUEUE_CONNECTION=database
CACHE_STORE=database

# Notifications (à compléter plus tard)
WHATSAPP_PHONE_NUMBER_ID=
WHATSAPP_TOKEN=
WHATSAPP_VERIFY_TOKEN=
SMS_PROVIDER=log
SMS_API_KEY=
SMS_SENDER=SuguPro

# Devise
CURRENCY_CODE=XOF
CURRENCY_SYMBOL=FCFA
```

Générer la clé :

```powershell
php artisan key:generate
```

### 7.2 Vérifier la timezone et la locale

`config/app.php` :

```php
'timezone' => env('APP_TIMEZONE', 'Africa/Bamako'),
'locale' => env('APP_LOCALE', 'fr'),
'fallback_locale' => env('APP_FALLBACK_LOCALE', 'fr'),
'faker_locale' => env('APP_FAKER_LOCALE', 'fr_FR'), 
```

### 7.3 Helper FCFA global

Créer `app/Support/helpers.php` :

```php
<?php

use Illuminate\Support\Number;

if (! function_exists('format_fcfa')) {
    /**
     * Formate un montant en franc CFA, sans décimale.
     * Exemple : 15500 => "15 500 FCFA"
     */
    function format_fcfa(int|float|string|null $amount, bool $withSymbol = true): string
    {
        $value = (int) round((float) ($amount ?? 0));
        $formatted = number_format($value, 0, ',', ' ');

        return $withSymbol ? $formatted . ' FCFA' : $formatted;
    }
}

if (! function_exists('parse_fcfa')) {
    /**
     * Convertit une saisie utilisateur ("15 500", "15.500", "15500 FCFA") en entier.
     */
    function parse_fcfa(?string $input): int
    {
        if ($input === null || trim($input) === '') {
            return 0;
        }

        $clean = preg_replace('/[^0-9\-]/', '', $input);

        return (int) ($clean === '' ? 0 : $clean);
    }
}
```

Déclarer l'autoload dans `composer.json` :

```json
"autoload": {
    "psr-4": {
        "App\\": "app/",
        "Database\\Factories\\": "database/factories/",
        "Database\\Seeders\\": "database/seeders/"
    },
    "files": [
        "app/Support/helpers.php"
    ]
}
```

Puis :

```powershell
composer dump-autoload
php artisan tinker --execute="echo format_fcfa(15500);"
# 15 500 FCFA
```

### 7.4 Base de données et migrations

```powershell
php artisan migrate
```

Si les tables `sessions`, `cache`, `jobs` manquent (pilotes `database`), générer les migrations :

```powershell
php artisan make:queue-table
php artisan make:session-table
php artisan make:cache-table
php artisan migrate
```

### 7.5 Rôles et permissions

Créer le seeder :

```powershell
php artisan make:seeder RolesAndPermissionsSeeder
```

`database/seeders/RolesAndPermissionsSeeder.php` :

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'products.view', 'products.create', 'products.update', 'products.delete',
            'stock.adjust', 'purchases.manage', 'pos.sell', 'pos.discount', 'pos.cancel',
            'cash.open', 'cash.close', 'customers.manage', 'credit.grant',
            'reports.view', 'settings.manage', 'users.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $gerant = Role::firstOrCreate(['name' => 'gerant', 'guard_name' => 'web']);
        $gerant->syncPermissions($permissions);

        $caissier = Role::firstOrCreate(['name' => 'caissier', 'guard_name' => 'web']);
        $caissier->syncPermissions([
            'products.view', 'pos.sell', 'pos.discount', 'cash.open', 'cash.close', 'customers.manage',
        ]);

        $magasinier = Role::firstOrCreate(['name' => 'magasinier', 'guard_name' => 'web']);
        $magasinier->syncPermissions([
            'products.view', 'products.create', 'products.update', 'stock.adjust', 'purchases.manage',
        ]);

        Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web'])
            ->syncPermissions($permissions);
    }
}
```

```powershell
php artisan db:seed --class=RolesAndPermissionsSeeder
```

---

## 8. Intégration de la charte graphique

### 8.1 Polices et Bootstrap dans `resources/sass/app.scss`

```scss
@import "@fontsource/plus-jakarta-sans/500.css";
@import "@fontsource/plus-jakarta-sans/700.css";
@import "@fontsource/inter/400.css";
@import "@fontsource/inter/600.css";

@import "bootstrap/scss/bootstrap";
@import "bootstrap-icons/font/bootstrap-icons.css";

:root {
  --sp-primary: #0D9488;
  --sp-primary-dark: #0F766E;
  --sp-secondary: #0F172A;
  --sp-accent: #F59E0B;
  --sp-bg: #F8FAFC;
  --sp-border: #E2E8F0;
}

body {
  background: var(--sp-bg);
  font-family: "Inter", "Segoe UI", sans-serif;
  color: var(--sp-secondary);
}

h1, h2, h3, h4, .navbar-brand {
  font-family: "Plus Jakarta Sans", system-ui, sans-serif;
}

.amount {
  font-variant-numeric: tabular-nums;
  font-weight: 600;
}
```

### 8.2 `vite.config.js`

```js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/sass/app.scss', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
});
```

### 8.3 Layout principal

Dans `resources/views/layouts/app.blade.php` :

```blade
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body>
    @include('layouts.navbar')
    <main class="container-fluid py-4">
        @yield('content')
    </main>
</body>
</html>
```

### 8.4 Build

```powershell
npm run dev     # pendant le développement (HMR)
npm run build   # avant commit / déploiement
```

---

## 9. Démarrage

### 9.1 Via Apache

1. XAMPP Control Panel → **Start** Apache et MySQL.
2. Ouvrir `http://supermarche.test`.
3. Page d'accueil Laravel affichée → installation réussie.

### 9.2 Via le serveur intégré

```powershell
php artisan serve --host=127.0.0.1 --port=8000
```

Ouvrir `http://127.0.0.1:8000`.

> Le serveur intégré ne sert que le développement. Toujours utiliser Apache/Nginx en production.

### 9.3 Créer le premier administrateur

```powershell
php artisan tinker
```

```php
$user = \App\Models\User::create([
    'name' => 'Administrateur',
    'email' => 'admin@sugupro.test',
    'password' => bcrypt('MotDePasse!2026'),
]);
$user->assignRole('super-admin');
```

---

## 10. Tests et qualité

### 10.1 Configuration `.env.testing`

```dotenv
APP_ENV=testing
APP_KEY=base64:...   # php artisan key:generate --env=testing
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=supermarche_test
DB_USERNAME=root
DB_PASSWORD=
CACHE_STORE=array
SESSION_DRIVER=array
QUEUE_CONNECTION=sync
```

### 10.2 Commandes

```powershell
php artisan test
php artisan test --coverage
.\vendor\bin\pint --test
.\vendor\bin\phpstan analyse
composer audit
npm audit
```

### 10.3 PHPStan (`phpstan.neon`)

```neon
includes:
    - vendor/larastan/larastan/extension.neon

parameters:
    paths:
        - app
    level: 5
    excludePaths:
        - app/Console/Kernel.php
```

---

## 11. Dépannage (erreurs fréquentes)

| Symptôme | Cause | Solution |
|---|---|---|
| `could not find driver` | `pdo_mysql` désactivé | Activer l'extension dans `php.ini`, redémarrer Apache |
| `Vite manifest not found` | Assets non compilés | `npm install && npm run build` |
| `403 Forbidden` sur `supermarche.test` | `AllowOverride` à `None` | Mettre `AllowOverride All`, redémarrer Apache |
| `404` sur toutes les routes sauf `/` | `mod_rewrite` inactif | Décommenter `LoadModule rewrite_module` |
| Apache ne démarre pas | Port 80 occupé | `netstat -ano \| Select-String ":80 "`, libérer ou passer en 8080 |
| MySQL ne démarre pas | Port 3306 occupé / données corrompues | Vérifier `netstat`, renommer `C:\xampp\mysql\data` après sauvegarde |
| `Access denied for user 'root'` | Mot de passe MySQL défini | Renseigner `DB_PASSWORD` dans `.env` |
| `Class "Database\Seeders\..." not found` | Autoload non régénéré | `composer dump-autoload` |
| `The only supported ciphers are AES-128-CBC...` | `APP_KEY` absente | `php artisan key:generate` |
| Page blanche, aucune erreur | `APP_DEBUG=false` ou permissions `storage` | `APP_DEBUG=true`, `php artisan optimize:clear` |
| `npm` très lent / erreurs SSL | Proxy ou cache | `npm cache clean --force`, `npm config set registry https://registry.npmjs.org/` |
| Composer « memory limit » | Mémoire PHP insuffisante | `$env:COMPOSER_MEMORY_LIMIT=-1` |
| `PHP Startup: Unable to load dynamic library 'curl'` sous Apache | DLL OpenSSL/libcurl d'Apache plus anciennes que celles de PHP 8.5 | Non bloquant : Guzzle utilise les streams. Pour corriger durablement, passer PHP en FastCGI (`mod_proxy_fcgi`) ou aligner XAMPP (Apache + PHP de la même version) |
| `419 Page Expired` | Jeton CSRF manquant | Vérifier `@csrf` et le cookie de session |
| Caractères accentués cassés | Encodage base | `utf8mb4` + `charset`/`collation` dans `config/database.php` |

### Commandes de remise à zéro utiles

```powershell
php artisan optimize:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
composer dump-autoload
npm run build
```

---

## 12. Checklist de fin d'installation

- [ ] `php -v` ≥ 8.2 et `php -m` contient `pdo_mysql`, `intl`, `gd`, `zip`.
- [ ] Base `supermarche_db` créée en `utf8mb4_unicode_ci`.
- [ ] VirtualHost `supermarche.test` actif, `http://supermarche.test` répond.
- [ ] `.env` configuré, `APP_KEY` générée, timezone `Africa/Bamako`.
- [ ] `php artisan migrate` sans erreur.
- [ ] Rôles et permissions seedés.
- [ ] `format_fcfa(15500)` retourne `15 500 FCFA`.
- [ ] `npm run build` génère `public/build/manifest.json`.
- [ ] Connexion à `/login` fonctionnelle avec l'administrateur créé.
- [ ] `php artisan test` passe.
- [ ] `.gitignore` exclut `vendor`, `node_modules`, `.env`, `public/build`, `storage/*.log`.

---

## 13. Étape suivante : Git & GitHub

```powershell
git init
git add .
git commit -m "feat: initialisation architecture ERP Supermarché FCFA, docs et suite de tests"
git branch -M main
git remote add origin https://github.com/<UTILISATEUR>/<NOM-DU-REPO>.git
git push -u origin main
```

Configurer la CI dans `.github/workflows/ci.yml` (Pint, PHPStan, PHPUnit, `npm run build`) — voir le cahier des charges, section Qualité.
