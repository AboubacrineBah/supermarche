<?php

if (! function_exists('format_fcfa')) {
    /**
     * Formate un montant en franc CFA, sans décimale.
     *
     * Exemple : 15500 => "15 500 FCFA"
     */
    function format_fcfa(int|float|string|null $amount, bool $withSymbol = true): string
    {
        $value = (int) round((float) ($amount ?? 0));
        $formatted = number_format($value, 0, ',', ' ');

        return $withSymbol ? $formatted.' FCFA' : $formatted;
    }
}

if (! function_exists('format_quantity')) {
    /**
     * Formate une quantité en retirant les décimales inutiles.
     *
     * Exemple : 2.50 => "2,5", 10 => "10"
     */
    function format_quantity(int|float|string|null $quantity): string
    {
        $value = round((float) ($quantity ?? 0), 2);
        $formatted = number_format($value, 2, ',', ' ');

        return rtrim(rtrim($formatted, '0'), ',');
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

        return (int) ($clean === '' || $clean === '-' ? 0 : $clean);
    }
}
