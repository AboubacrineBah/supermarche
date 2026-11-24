@extends('layouts.app')

@section('title', $purchaseOrder->reference)

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <h1 class="h4 mb-1">{{ $purchaseOrder->reference }}</h1>
            <div class="text-muted small">
                Fournisseur : {{ $purchaseOrder->supplier?->name ?? '—' }} •
                Commandé le {{ $purchaseOrder->ordered_at?->format('d/m/Y') ?? '—' }}
                @if ($purchaseOrder->expected_at)
                    • Prévu le {{ $purchaseOrder->expected_at->format('d/m/Y') }}
                @endif
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('purchase-orders.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Retour
            </a>
            @can('purchases.manage')
                @if ($purchaseOrder->status === \App\Enums\PurchaseOrderStatus::Draft)
                    <a href="{{ route('purchase-orders.edit', $purchaseOrder) }}" class="btn btn-primary">
                        <i class="bi bi-pencil me-1"></i> Modifier
                    </a>
                @endif
                @if ($purchaseOrder->status->isReceivable())
                    <a href="{{ route('purchase-orders.receive', $purchaseOrder) }}" class="btn btn-success">
                        <i class="bi bi-truck me-1"></i> Réceptionner
                    </a>
                @endif
            @endcan
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-body">
                    <h2 class="h5 mb-3">Lignes de commande</h2>
                    @if ($purchaseOrder->items->isEmpty())
                        <p class="text-muted mb-0">Aucune ligne pour ce bon de commande.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Produit</th>
                                        <th class="text-end">Qté commandée</th>
                                        <th class="text-end">Qté reçue</th>
                                        <th class="text-end">Prix unitaire</th>
                                        <th class="text-end">Total ligne</th>
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
                                                @php $received = (float) $item->received_quantity; @endphp
                                                <span class="badge {{ $received >= (float) $item->quantity ? 'text-bg-success' : 'text-bg-warning' }}">
                                                    {{ format_quantity($received) }}
                                                </span>
                                            </td>
                                            <td class="text-end amount">{{ format_fcfa($item->unit_cost) }}</td>
                                            <td class="text-end amount fw-semibold">{{ format_fcfa(round((float) $item->quantity * (float) $item->unit_cost, 0)) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4" class="text-end fw-bold">Total :</td>
                                        <td class="text-end amount fw-bold fs-5">{{ format_fcfa($purchaseOrder->total) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-3">
                <div class="card-body">
                    <h2 class="h5 mb-3">Informations</h2>
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Statut</dt>
                        <dd class="col-sm-8">
                            <span class="badge {{ $purchaseOrder->status->badgeClass() }} fs-6">
                                {{ $purchaseOrder->status->label() }}
                            </span>
                        </dd>

                        <dt class="col-sm-4">Date de commande</dt>
                        <dd class="col-sm-8">{{ $purchaseOrder->ordered_at?->format('d/m/Y') ?? '—' }}</dd>

                        <dt class="col-sm-4">Date prévue</dt>
                        <dd class="col-sm-8">{{ $purchaseOrder->expected_at?->format('d/m/Y') ?? '—' }}</dd>

                        <dt class="col-sm-4">Créé par</dt>
                        <dd class="col-sm-8">{{ $purchaseOrder->user?->name ?? '—' }}</dd>

                        <dt class="col-sm-4">Notes</dt>
                        <dd class="col-sm-8">{{ $purchaseOrder->notes ?: '—' }}</dd>
                    </dl>
                </div>
            </div>

            @if ($purchaseOrder->receipts->isNotEmpty())
                <div class="card">
                    <div class="card-body">
                        <h2 class="h5 mb-3">Réceptions</h2>
                        @foreach ($purchaseOrder->receipts as $receipt)
                            <div class="border-bottom pb-2 mb-2">
                                <div class="fw-semibold">{{ $receipt->reference }}</div>
                                <div class="text-muted small">
                                    {{ $receipt->received_at?->format('d/m/Y H:i') }} •
                                    Par {{ $receipt->user?->name ?? '—' }}
                                </div>
                                @if ($receipt->notes)
                                    <div class="small">{{ $receipt->notes }}</div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection