@extends('layouts.app')

@section('title', 'Réceptionner '.$purchaseOrder->reference)

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <h1 class="h4 mb-1">Réceptionner {{ $purchaseOrder->reference }}</h1>
            <p class="text-muted small mb-0">
                Fournisseur : {{ $purchaseOrder->supplier?->name ?? '—' }} • Bon de commande du
                {{ $purchaseOrder->ordered_at?->format('d/m/Y') ?? '—' }}
            </p>
        </div>
        <a href="{{ route('purchase-orders.show', $purchaseOrder) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Retour
        </a>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <h2 class="h5 mb-3">Articles à réceptionner</h2>

            @if ($purchaseOrder->items->isEmpty())
                <div class="sp-empty">
                    <i class="bi bi-info-circle"></i>
                    <p class="mb-2">Ce bon de commande ne contient aucun article.</p>
                </div>
            @else
                <form method="POST" action="{{ route('purchase-orders.receive.store', $purchaseOrder) }}">
                    @csrf

                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Produit</th>
                                    <th class="text-end">Quantité commandée</th>
                                    <th class="text-end">Quantité déjà reçue</th>
                                    <th class="text-end" style="width: 20%">Quantité à réceptionner</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($purchaseOrder->items as $item)
                                    <tr>
                                        <td>
                                            <div class="fw-semibold">{{ $item->product?->name ?? 'Produit supprimé' }}</div>
                                            <div class="text-muted small">{{ $item->product?->sku ?? '' }}</div>
                                        </td>
                                        <td class="text-end">{{ format_quantity($item->quantity) }}</td>
                                        <td class="text-end">
                                            <span class="badge {{ (float) $item->received_quantity >= (float) $item->quantity ? 'text-bg-success' : 'text-bg-warning' }}">
                                                {{ format_quantity($item->received_quantity) }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <input type="number"
                                                name="items[{{ $item->id }}][quantity]"
                                                class="form-control text-end qty @error('items.*') is-invalid @enderror"
                                                value="{{ (float) ($item->quantity - $item->received_quantity) }}"
                                                step="0.01"
                                                min="0"
                                                max="{{ max(0, round((float) $item->quantity - (float) $item->received_quantity, 2)) }"
                                                required>
                                            @error('items.*')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="row g-3 mt-3">
                        <div class="col-md-6">
                            <label for="received_at" class="form-label">Date de réception</label>
                            <input type="datetime-local" class="form-control @error('received_at') is-invalid @enderror" id="received_at"
                                name="received_at" value="{{ old('received_at', now()->toDateTimeString()) }}" required>
                            @error('received_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="2"
                                maxlength="2000">{{ old('notes', $purchaseOrder->notes ?? '') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-truck me-1"></i> Valider la réception
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
@endsection