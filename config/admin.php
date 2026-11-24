<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Compte administrateur initial
    |--------------------------------------------------------------------------
    |
    | Ces valeurs sont utilisées par AdminUserSeeder pour créer le premier
    | compte super-admin. Le mot de passe doit impérativement être défini
    | via ADMIN_PASSWORD hors environnement local.
    |
    */

    'name' => env('ADMIN_NAME', 'Administrateur'),

    'email' => env('ADMIN_EMAIL', 'admin@sugupro.test'),

    'password' => env('ADMIN_PASSWORD'),

];
