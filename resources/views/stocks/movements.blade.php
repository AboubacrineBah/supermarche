@extends('layouts.app')

@section('title', 'Mouvements de stock')

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <h1 class="h4 mb-1">Mouvements de stock</h1>
            <p class="text-muted small mb-0">
                {{ $product->name }} · {{ $product->sku }} ·
                stock actuel : <strong>{{ format_quantity($product->stock?->quantity ?? 0) }}</strong>
                {{ $product->unit?->symbol }}
            </p>
        </div>
        <div class="d-flex gap-2">
            @can('stock.adjust')
                <a href="{{ route('stocks.adjust', $product) }}" class="btn btn-primary">
                    <i class="bi bi-sliders me-1"></i> Ajuster
                </a>
            @endcan
            <a href="{{ route('stocks.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Retour
            </a>
        </div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th class="text-end">Quantité</th>
                        <th>Motif</th>
                        <th>Utilisateur</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($movements as $movement)
                        @php
                            $quantity = (float) $movement->quantity;
                        @endphp
                        <tr>
                            <td class="text-nowrap">{{ $movement->occurred_at?->format('d/m/Y H:i') ?? '—' }}</td>
                            <td>{{ $movement->type->label() }}</td>
                            <td class="text-end">
                                <span class="fw-semibold {{ $quantity >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $quantity > 0 ? '+' : '' }}{{ format_quantity($quantity) }}
                                </span>
                            </td>
                            <td>{{ $movement->reason ?: '—' }}</td>
                            <td>{{ $movement->user?->name ?? 'Système' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                Aucun mouvement enregistré pour ce produit.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($movements->hasPages())
            <div class="card-body border-top">
                {{ $movements->links() }}
            </div>
        @endif
    </div>
@endsection
