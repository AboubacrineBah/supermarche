@extends('layouts.app')

@section('title', 'Tableau de bord')

@section('content')
    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-3">
            <div class="sp-kpi h-100">
                <div class="sp-kpi__icon bg-primary-subtle text-primary"><i class="bi bi-box-seam"></i></div>
                <div>
                    <div class="text-muted-soft small">Produits au catalogue</div>
                    <div class="sp-kpi__value">{{ number_format($stats['products'], 0, ',', ' ') }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="sp-kpi h-100">
                <div class="sp-kpi__icon bg-success-subtle text-success"><i class="bi bi-check2-circle"></i></div>
                <div>
                    <div class="text-muted-soft small">Produits actifs</div>
                    <div class="sp-kpi__value">{{ number_format($stats['active_products'], 0, ',', ' ') }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="sp-kpi h-100">
                <div class="sp-kpi__icon bg-warning-subtle text-warning-emphasis"><i class="bi bi-tags"></i></div>
                <div>
                    <div class="text-muted-soft small">Catégories</div>
                    <div class="sp-kpi__value">{{ number_format($stats['categories'], 0, ',', ' ') }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <a href="{{ route('stocks.index', ['status' => 'low']) }}" class="text-decoration-none text-reset">
                <div class="sp-kpi h-100">
                    <div class="sp-kpi__icon bg-warning-subtle text-warning-emphasis"><i class="bi bi-exclamation-triangle"></i></div>
                    <div>
                        <div class="text-muted-soft small">Alertes de stock</div>
                        <div class="sp-kpi__value">{{ number_format($stats['low_stock'], 0, ',', ' ') }}</div>
                        <div class="text-muted-soft small">{{ number_format($stats['out_of_stock'], 0, ',', ' ') }} en rupture</div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="sp-card h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <h2 class="h5 mb-0">Derniers produits ajoutés</h2>
                        <p class="text-muted-soft small mb-0">Les six références les plus récentes du catalogue.</p>
                    </div>
                    @can('products.view')
                        <a href="{{ route('products.index') }}" class="btn btn-outline-primary btn-sm">
                            Tout voir <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    @endcan
                </div>

                @if ($recentProducts->isEmpty())
                    <div class="sp-empty">
                        <i class="bi bi-box-seam"></i>
                        <p class="mb-2">Aucun produit pour le moment.</p>
                        @can('products.create')
                            <a href="{{ route('products.create') }}" class="btn btn-primary">Ajouter un produit</a>
                        @endcan
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Produit</th>
                                    <th>Catégorie</th>
                                    <th class="text-end">Stock</th>
                                    <th class="text-end">Prix détail</th>
                                    <th class="text-end">Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recentProducts as $product)
                                    <tr>
                                        <td>
                                            <div class="fw-semibold">{{ $product->name }}</div>
                                            <div class="text-muted-soft small">{{ $product->sku }}</div>
                                        </td>
                                        <td class="text-muted-soft">{{ $product->category?->name ?? '—' }}</td>
                                        @php
                                            $stockQuantity = (float) ($product->stock?->quantity ?? 0);
                                            $stockBadge = $stockQuantity <= 0
                                                ? 'text-bg-danger'
                                                : ($stockQuantity <= (float) $product->stock_min ? 'text-bg-warning' : 'text-bg-success');
                                        @endphp
                                        <td class="text-end">
                                            <span class="badge {{ $stockBadge }}">{{ format_quantity($stockQuantity) }}</span>
                                        </td>
                                        <td class="amount text-end">{{ format_fcfa($product->retail_price) }}</td>
                                        <td class="text-end">
                                            <span class="badge-status {{ $product->is_active ? 'badge-status--success' : 'badge-status--danger' }}">
                                                {{ $product->is_active ? 'Actif' : 'Inactif' }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <div class="col-lg-4">
            <div class="sp-card mb-3">
                <h2 class="h5">Actions rapides</h2>
                <p class="text-muted-soft small">Les prochaines briques du produit arrivent sprint après sprint.</p>
                <div class="d-grid gap-2">
                    @can('products.create')
                        <a href="{{ route('products.create') }}" class="btn btn-primary text-start">
                            <i class="bi bi-plus-lg me-2"></i> Nouveau produit
                        </a>
                    @endcan
                    @can('products.view')
                        <a href="{{ route('products.index') }}" class="btn btn-outline-primary text-start">
                            <i class="bi bi-search me-2"></i> Consulter le catalogue
                        </a>
                        <a href="{{ route('stocks.index') }}" class="btn btn-outline-primary text-start">
                            <i class="bi bi-boxes me-2"></i> Suivre le stock
                        </a>
                    @endcan
                    @can('purchases.manage')
                        <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary text-start">
                            <i class="bi bi-building me-2"></i> Fournisseurs
                        </a>
                        <a href="{{ route('purchase-orders.index') }}" class="btn btn-outline-secondary text-start">
                            <i class="bi bi-file-earmark-plus me-2"></i> Bons de commande
                        </a>
                    @endcan
                    @can('pos.sell')
                        <a href="{{ route('pos.index') }}" class="btn btn-primary text-start">
                            <i class="bi bi-cash-register me-2"></i> Ouvrir la caisse
                        </a>
                    @endcan
                </div>
            </div>

            {{-- Conteneur "Prochaines étapes" masqué : toutes les étapes sont terminées. --}}
        </div>
    </div>
@endsection