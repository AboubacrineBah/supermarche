<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Support\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantContext
{
    public function __construct(private TenantContext $tenantContext) {}

    /**
     * Résout le magasin courant à partir de l'utilisateur authentifié.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user instanceof User) {
            return $next($request);
        }

        if (! $user->is_active) {
            abort(403, 'Ce compte est désactivé.');
        }

        if ($user->hasRole('super-admin')) {
            $this->tenantContext->set(null);
        } else {
            $tenant = $user->tenant;

            if ($tenant === null) {
                abort(403, 'Aucun magasin n’est associé à ce compte.');
            }

            if (! $tenant->is_active) {
                abort(403, 'Ce magasin est désactivé.');
            }

            $this->tenantContext->set($tenant->id);
        }

        try {
            return $next($request);
        } finally {
            $this->tenantContext->forget();
        }
    }
}
