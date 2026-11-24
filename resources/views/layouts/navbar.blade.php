<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home') }}">
            <i class="bi bi-shop"></i>
            <span>{{ config('app.name', 'SuguPro') }}</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar"
            aria-controls="mainNavbar" aria-expanded="false" aria-label="Basculer la navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav mx-auto">
                @auth
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                            <i class="bi bi-speedometer2 me-1"></i> Tableau de bord
                        </a>
                    </li>

                    @can('pos.sell')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('pos.*') ? 'active' : '' }}" href="{{ route('pos.index') }}">
                                <i class="bi bi-cash-register me-1"></i> Caisse
                            </a>
                        </li>
                    @endcan

                    {{-- Catalogue --}}
                    @can('products.view')
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ request()->routeIs('products.*') || request()->routeIs('stocks.*') ? 'active' : '' }}"
                                href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-box-seam me-1"></i> Catalogue
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('products.*') ? 'active' : '' }}"
                                        href="{{ route('products.index') }}">
                                        <i class="bi bi-box-seam me-1"></i> Produits
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('stocks.*') ? 'active' : '' }}"
                                        href="{{ route('stocks.index') }}">
                                        <i class="bi bi-boxes me-1"></i> Stock
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endcan

                    {{-- Achats --}}
                    @can('purchases.manage')
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ request()->routeIs('suppliers.*') || request()->routeIs('purchase-orders.*') ? 'active' : '' }}"
                                href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-truck me-1"></i> Achats
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('suppliers.*') ? 'active' : '' }}"
                                        href="{{ route('suppliers.index') }}">
                                        <i class="bi bi-building me-1"></i> Fournisseurs
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('purchase-orders.*') ? 'active' : '' }}"
                                        href="{{ route('purchase-orders.index') }}">
                                        <i class="bi bi-file-earmark-plus me-1"></i> Bons de commande
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endcan

                    {{-- Ventes & Clients --}}
                    @can('customers.manage')
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ request()->routeIs('customers.*') || request()->routeIs('credit-payments.*') || request()->routeIs('sales-dashboard.*') ? 'active' : '' }}"
                                href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-people me-1"></i> Ventes & Clients
                            </a>
                            <ul class="dropdown-menu">
                                @can('reports.view')
                                    <li>
                                        <a class="dropdown-item {{ request()->routeIs('sales-dashboard.*') ? 'active' : '' }}"
                                            href="{{ route('sales-dashboard.index') }}">
                                            <i class="bi bi-graph-up me-1"></i> Tableau de bord ventes
                                        </a>
                                    </li>
                                @endcan
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('customers.*') ? 'active' : '' }}"
                                        href="{{ route('customers.index') }}">
                                        <i class="bi bi-people me-1"></i> Clients
                                    </a>
                                </li>
                                @can('credit.grant')
                                    <li>
                                        <a class="dropdown-item {{ request()->routeIs('credit-payments.*') ? 'active' : '' }}"
                                            href="{{ route('credit-payments.index') }}">
                                            <i class="bi bi-cash-coin me-1"></i> Crédits
                                        </a>
                                    </li>
                                @endcan
                            </ul>
                        </li>
                    @endcan
                @endauth
            </ul>

            <ul class="navbar-nav ms-auto">
                @auth
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="bi bi-person-circle me-1"></i> {{ auth()->user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <span class="dropdown-item-text small text-muted">{{ auth()->user()->email }}</span>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button class="dropdown-item" type="submit">
                                        <i class="bi bi-box-arrow-right me-1"></i> Déconnexion
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('login') ? 'active' : '' }}" href="{{ route('login') }}">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Connexion
                        </a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>