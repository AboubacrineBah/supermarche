@extends('layouts.app')

@section('title', 'Bons de commande')

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <h1 class="h4 mb-1">Bons de commande</h1>
            <p class="text-muted small mb-0">{{ $purchaseOrders->total() }} bon(s) de commande.</p>
        </div>
        @can('purchases.manage')
            <a href="{{ route('purchase-orders.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Nouveau bon de commande
            </a>
        @endcan
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('purchase-orders.index') }}" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label" for="search">Référence</label>
                    <input type="search" class="form-control" id="search" name="search" value="{{ $search }}"
                        placeholder="Référence du bon">
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="status">Statut</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Tous</option>
                        @foreach (\App\Enums\PurchaseOrderStatus::cases() as $s)
                            <option value="{{ $s->value }}" @selected($status === $s->value)>{{ $s->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="supplier_id">Fournisseur</label>
                    <select class="form-select" id="supplier_id" name="supplier_id">
                        <option value="">Tous</option>
                        @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" @selected($supplierId == $supplier->id)>{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label" for="date_from">Du</label>
                    <input type="date" class="form-control" id="date_from" name="date_from" value="{{ $dateFrom }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label" for="date_to">Au</label>
                    <input type="date" class="form-control" id="date_to" name="date_to" value="{{ $dateTo }}">
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
                        <th>Référence</th>
                        <th>Fournisseur</th>
                        <th>Date commande</th>
                        <th class="text-end">Total</th>
                        <th class="text-end">Statut</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($purchaseOrders as $po)
                        <tr>
                            <td class="fw-semibold">{{ $po->reference }}</td>
                            <td>{{ $po->supplier?->name ?? '—' }}</td>
                            <td>{{ $po->ordered_at?->format('d/m/Y') ?? '—' }}</td>
                            <td class="text-end amount">{{ format_fcfa($po->total) }}</td>
                            <td class="text-end">
                                <span class="badge {{ $po->status->badgeClass() }}">
                                    {{ $po->status->label() }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    @can('purchases.manage')
                                        <a href="{{ route('purchase-orders.show', $po) }}" class="btn btn-outline-secondary" title="Voir">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @if ($po->status === \App\Enums\PurchaseOrderStatus::Draft)
                                            <a href="{{ route('purchase-orders.edit', $po) }}" class="btn btn-outline-primary" title="Modifier">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form method="POST" action="{{ route('purchase-orders.destroy', $po) }}"
                                                onsubmit="return confirm('Supprimer ce bon de commande ?');">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-outline-danger" type="submit" title="Supprimer">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                        @if ($po->status->isReceivable())
                                            <a href="{{ route('purchase-orders.receive', $po) }}" class="btn btn-outline-success" title="Réceptionner">
                                                <i class="bi bi-truck"></i>
                                            </a>
                                        @endif
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Aucun bon de commande trouvé.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($purchaseOrders->hasPages())
            <div class="card-body border-top">
                {{ $purchaseOrders->links() }}
            </div>
        @endif
    </div>
@endsection