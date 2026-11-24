@extends('layouts.app')

@section('title', $product->name)

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <h1 class="h4 mb-1">{{ $product->name }}</h1>
            <div class="text-muted small">
                {{ $product->sku }}
                @if ($product->barcode)
                    · {{ $product->barcode }}
                @endif
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Retour
            </a>
            @can('update', $product)
                <a href="{{ route('products.edit', $product) }}" class="btn btn-primary">
                    <i class="bi bi-pencil me-1"></i> Modifier
                </a>
            @endcan
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-body">
                    <h2 class="h5 mb-3">Informations</h2>
                    <dl class="row mb-0">
                        <dt class="col-sm-3">Magasin</dt>
                        <dd class="col-sm-9">{{ $product->tenant?->name ?? '—' }}</dd>

                        <dt class="col-sm-3">Catégorie</dt>
                        <dd class="col-sm-9">{{ $product->category?->name ?? '—' }}</dd>

                        <dt class="col-sm-3">Unité</dt>
                        <dd class="col-sm-9">{{ $product->unit?->name ?? '—' }}</dd>

                        <dt class="col-sm-3">Suivi des lots</dt>
                        <dd class="col-sm-9">{{ $product->track_lots ? 'Oui' : 'Non' }}</dd>

                        <dt class="col-sm-3">Statut</dt>
                        <dd class="col-sm-9">
                            <span class="badge {{ $product->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">
                                {{ $product->is_active ? 'Actif' : 'Inactif' }}
                            </span>
                        </dd>

                        <dt class="col-sm-3">Stock minimum</dt>
                        <dd class="col-sm-9">{{ number_format((float) $product->stock_min, 0, ',', ' ') }}</dd>

                        <dt class="col-sm-3">Stock maximum</dt>
                        <dd class="col-sm-9">
                            {{ $product->stock_max !== null ? number_format((float) $product->stock_max, 0, ',', ' ') : '—' }}
                        </dd>

                        <dt class="col-sm-3">Description</dt>
                        <dd class="col-sm-9">{{ $product->description ?: '—' }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-body">
                    <h2 class="h5 mb-3">Tarifs</h2>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Prix d'achat</span>
                        <span class="amount">{{ format_fcfa($product->cost_price) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Prix de détail</span>
                        <span class="amount">{{ format_fcfa($product->retail_price) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Prix de gros</span>
                        <span class="amount">{{ $product->wholesale_price !== null ? format_fcfa($product->wholesale_price) : '—' }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Quantité min. gros</span>
                        <span>{{ $product->wholesale_min_qty !== null ? number_format((float) $product->wholesale_min_qty, 2, ',', ' ') : '—' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
