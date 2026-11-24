@extends('layouts.app')

@section('title', 'Stock')

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <h1 class="h4 mb-1">Stock</h1>
            <p class="text-muted small mb-0">Suivi des quantités disponibles et des seuils d'alerte.</p>
        </div>
        @can('products.view')
            <a href="{{ route('products.index') }}" class="btn btn-outline-primary">
                <i class="bi bi-box-seam me-1"></i> Catalogue
            </a>
        @endcan
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="sp-kpi h-100">
                <div class="sp-kpi__icon bg-primary-subtle text-primary"><i class="bi bi-boxes"></i></div>
                <div>
                    <div class="text-muted-soft small">Produits suivis</div>
                    <div class="sp-kpi__value">{{ number_format($stats['products'], 0, ',', ' ') }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <a href="{{ route('stocks.index', ['status' => 'low']) }}" class="text-decoration-none text-reset">
                <div class="sp-kpi h-100">
                    <div class="sp-kpi__icon bg-warning-subtle text-warning-emphasis"><i class="bi bi-exclamation-triangle"></i></div>
                    <div>
                        <div class="text-muted-soft small">Alertes de stock</div>
                        <div class="sp-kpi__value">{{ number_format($stats['low_stock'], 0, ',', ' ') }}</div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('stocks.index', ['status' => 'out']) }}" class="text-decoration-none text-reset">
                <div class="sp-kpi h-100">
                    <div class="sp-kpi__icon bg-danger-subtle text-danger"><i class="bi bi-x-octagon"></i></div>
                    <div>
                        <div class="text-muted-soft small">Ruptures</div>
                        <div class="sp-kpi__value">{{ number_format($stats['out_of_stock'], 0, ',', ' ') }}</div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('stocks.index') }}" class="row g-2 align-items-end">
                <div class="col-md-6">
                    <label class="form-label" for="search">Recherche</label>
                    <input type="search" class="form-control" id="search" name="search" value="{{ $search }}"
                        placeholder="Nom, SKU ou code-barres">
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="status">État du stock</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Tous les produits</option>
                        <option value="low" @selected($status === 'low')>Seuil atteint</option>
                        <option value="out" @selected($status === 'out')>En rupture</option>
                        <option value="ok" @selected($status === 'ok')>Disponibles</option>
                    </select>
                </div>
                <div class="col-md-2 d-grid">
                    <button class="btn btn-outline-primary" type="submit">
                        <i class="bi bi-funnel me-1"></i> Filtrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Produit</th>
                        <th>Catégorie</th>
                        <th>Unité</th>
                        <th class="text-end">Quantité</th>
                        <th class="text-end">Seuil min.</th>
                        <th class="text-end">Statut</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        @php
                            $quantity = (float) ($product->stock?->quantity ?? 0);
                            $minimum = (float) $product->stock_min;
                            $isOut = $quantity <= 0;
                            $isLow = ! $isOut && $quantity <= $minimum;
                            $badge = $isOut ? 'text-bg-danger' : ($isLow ? 'text-bg-warning' : 'text-bg-success');
                            $label = $isOut ? 'Rupture' : ($isLow ? 'Seuil bas' : 'Disponible');
                        @endphp
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $product->name }}</div>
                                <div class="text-muted small">{{ $product->sku }}</div>
                            </td>
                            <td>{{ $product->category?->name ?? '—' }}</td>
                            <td>{{ $product->unit?->symbol ?? '—' }}</td>
                            <td class="text-end fw-semibold">{{ format_quantity($quantity) }}</td>
                            <td class="text-end text-muted">{{ format_quantity($minimum) }}</td>
                            <td class="text-end">
                                <span class="badge {{ $badge }}">{{ $label }}</span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('stocks.movements', $product) }}" class="btn btn-outline-secondary"
                                        title="Historique">
                                        <i class="bi bi-clock-history"></i>
                                    </a>
                                    @can('stock.adjust')
                                        <a href="{{ route('stocks.adjust', $product) }}" class="btn btn-outline-primary"
                                            title="Ajuster">
                                            <i class="bi bi-sliders"></i>
                                        </a>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Aucun produit ne correspond à ce filtre.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($products->hasPages())
            <div class="card-body border-top">
                {{ $products->links() }}
            </div>
        @endif
    </div>
@endsection
