<?php

namespace App\Support;

use Closure;

/**
 * Contexte multi-tenant de la requête courante.
 *
 * Par défaut, le tenant est déduit de l'utilisateur authentifié.
 * Les seeders, commandes et tests peuvent forcer un tenant explicitement.
 */
final class TenantContext
{
    private ?int $tenantId = null;

    private bool $forced = false;

    public function id(): ?int
    {
        return $this->forced ? $this->tenantId : auth()->user()?->tenant_id;
    }

    public function set(?int $tenantId): void
    {
        $this->tenantId = $tenantId;
        $this->forced = true;
    }

    public function forget(): void
    {
        $this->tenantId = null;
        $this->forced = false;
    }

    /**
     * Exécute un callback dans le contexte d'un tenant donné, puis restaure l'état précédent.
     */
    public function run(?int $tenantId, Closure $callback): mixed
    {
        $previousTenantId = $this->tenantId;
        $previousForced = $this->forced;

        $this->tenantId = $tenantId;
        $this->forced = true;

        try {
            return $callback();
        } finally {
            $this->tenantId = $previousTenantId;
            $this->forced = $previousForced;
        }
    }
}
