@extends('layouts.app')

@section('title', 'Ajuster le stock')

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <h1 class="h4 mb-1">Ajuster le stock</h1>
            <p class="text-muted small mb-0">{{ $product->name }} · {{ $product->sku }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('stocks.movements', $product) }}" class="btn btn-outline-secondary">
                <i class="bi bi-clock-history me-1"></i> Historique
            </a>
            <a href="{{ route('stocks.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Retour
            </a>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('stocks.adjust.store', $product) }}">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-5">
                                <label for="quantity" class="form-label">Nouvelle quantité réelle</label>
                                <div class="input-group">
                                    <input type="number" class="form-control @error('quantity') is-invalid @enderror"
                                        id="quantity" name="quantity"
                                        value="{{ old('quantity', $product->stock?->quantity ?? 0) }}" min="0"
                                        step="0.01" required>
                                    <span class="input-group-text">{{ $product->unit?->symbol ?? 'u' }}</span>
                                    @error('quantity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-7">
                                <label for="reason" class="form-label">Motif de l'ajustement</label>
                                <input type="text" class="form-control @error('reason') is-invalid @enderror" id="reason"
                                    name="reason" value="{{ old('reason') }}" maxlength="255" required
                                    placeholder="Inventaire, casse, correction de saisie...">
                                @error('reason')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="alert alert-info mt-3 mb-0 small">
                            <i class="bi bi-info-circle me-1"></i>
                            L'écart entre la quantité actuelle et la quantité saisie sera enregistré comme mouvement
                            d'ajustement, avec votre nom et le motif indiqué.
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('stocks.index') }}" class="btn btn-outline-secondary">Annuler</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i> Enregistrer l'ajustement
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h2 class="h6 text-uppercase text-muted-soft mb-3">Situation actuelle</h2>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Stock actuel</span>
                        <span class="fw-semibold">{{ format_quantity($product->stock?->quantity ?? 0) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Seuil minimum</span>
                        <span>{{ format_quantity($product->stock_min) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Catégorie</span>
                        <span>{{ $product->category?->name ?? '—' }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Unité</span>
                        <span>{{ $product->unit?->name ?? '—' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
