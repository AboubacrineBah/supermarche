@extends('layouts.app')

@section('title', $supplier->name)

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <h1 class="h4 mb-1">{{ $supplier->name }}</h1>
            <div class="text-muted small">
                {{ $supplier->nif ? 'NIF : '.$supplier->nif : 'Fournisseur' }}
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Retour
            </a>
            @can('purchases.manage')
                <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-primary">
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
                        <dt class="col-sm-3">Téléphone</dt>
                        <dd class="col-sm-9">{{ $supplier->phone ?? '—' }}</dd>

                        <dt class="col-sm-3">WhatsApp</dt>
                        <dd class="col-sm-9">{{ $supplier->whatsapp ?? '—' }}</dd>

                        <dt class="col-sm-3">Email</dt>
                        <dd class="col-sm-9">{{ $supplier->email ?? '—' }}</dd>

                        <dt class="col-sm-3">Adresse</dt>
                        <dd class="col-sm-9">{{ $supplier->address ?? '—' }}</dd>

                        <dt class="col-sm-3">Conditions</dt>
                        <dd class="col-sm-9">{{ $supplier->paymentTermsLabel() }}</dd>

                        <dt class="col-sm-3">Plafond de dette</dt>
                        <dd class="col-sm-9">
                            {{ $supplier->debt_limit !== null ? format_fcfa($supplier->debt_limit) : '—' }}
                        </dd>

                        <dt class="col-sm-3">Statut</dt>
                        <dd class="col-sm-9">
                            <span class="badge {{ $supplier->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">
                                {{ $supplier->is_active ? 'Actif' : 'Inactif' }}
                            </span>
                        </dd>

                        <dt class="col-sm-3">Notes</dt>
                        <dd class="col-sm-9">{{ $supplier->notes ?: '—' }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-3">
                <div class="card-body">
                    <h2 class="h5 mb-3">Activité</h2>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Commandes</span>
                        <span class="fw-semibold">{{ $stats['orders_count'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">En cours</span>
                        <span class="fw-semibold">{{ $stats['orders_pending'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Dette fournisseur</span>
                        <span class="fw-semibold">{{ format_fcfa($stats['debt']) }}</span>
                    </div>
                </div>
            </div>

            @can('purchases.manage')
                <a href="{{ route('purchase-orders.create') }}" class="btn btn-outline-primary w-100">
                    <i class="bi bi-file-earmark-plus me-1"></i> Nouveau bon de commande
                </a>
            @endcan
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-body">
            <h2 class="h5 mb-3">Derniers bons de commande</h2>

            @if ($supplier->purchaseOrders->isEmpty())
                <p class="text-muted mb-0">Aucun bon de commande pour ce fournisseur.</p>
            @else
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Référence</th>
                                <th>Date</th>
                                <th class="text-end">Total</th>
                                <th class="text-end">Statut</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($supplier->purchaseOrders as $order)
                                <tr>
                                    <td class="fw-semibold">{{ $order->reference }}</td>
                                    <td>{{ $order->ordered_at?->format('d/m/Y') ?? '—' }}</td>
                                    <td class="text-end amount">{{ format_fcfa($order->total) }}</td>
                                    <td class="text-end">
                                        <span class="badge {{ $order->status->badgeClass() }}">
                                            {{ $order->status->label() }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('purchase-orders.show', $order) }}"
                                            class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection
