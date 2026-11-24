@extends('layouts.app')

@section('title', 'Tableau de bord des ventes')

@section('content')
    <h1 class="h4 mb-4"><i class="bi bi-graph-up me-2"></i>Tableau de bord des ventes</h1>

    {{-- KPIs --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-3">
            <div class="sp-kpi h-100">
                <div class="sp-kpi__icon bg-primary-subtle text-primary"><i class="bi bi-receipt"></i></div>
                <div>
                    <div class="text-muted-soft small">Ventes aujourd'hui</div>
                    <div class="sp-kpi__value">{{ number_format($stats['today_sales']) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="sp-kpi h-100">
                <div class="sp-kpi__icon bg-success-subtle text-success"><i class="bi bi-currency-exchange"></i></div>
                <div>
                    <div class="text-muted-soft small">CA aujourd'hui</div>
                    <div class="sp-kpi__value">{{ format_fcfa($stats['today_revenue']) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="sp-kpi h-100">
                <div class="sp-kpi__icon bg-info-subtle text-info"><i class="bi bi-calendar-week"></i></div>
                <div>
                    <div class="text-muted-soft small">CA cette semaine</div>
                    <div class="sp-kpi__value">{{ format_fcfa($stats['week_revenue']) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="sp-kpi h-100">
                <div class="sp-kpi__icon bg-warning-subtle text-warning-emphasis"><i class="bi bi-calendar-month"></i></div>
                <div>
                    <div class="text-muted-soft small">CA ce mois</div>
                    <div class="sp-kpi__value">{{ format_fcfa($stats['month_revenue']) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-3">
            <div class="sp-kpi h-100">
                <div class="sp-kpi__icon bg-secondary-subtle text-secondary"><i class="bi bi-receipt-cutoff"></i></div>
                <div>
                    <div class="text-muted-soft small">Total ventes</div>
                    <div class="sp-kpi__value">{{ number_format($stats['total_sales']) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="sp-kpi h-100">
                <div class="sp-kpi__icon bg-success-subtle text-success"><i class="bi bi-cash-stack"></i></div>
                <div>
                    <div class="text-muted-soft small">CA total</div>
                    <div class="sp-kpi__value">{{ format_fcfa($stats['total_revenue']) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="sp-kpi h-100">
                <div class="sp-kpi__icon bg-danger-subtle text-danger"><i class="bi bi-credit-card"></i></div>
                <div>
                    <div class="text-muted-soft small">Crédits en cours</div>
                    <div class="sp-kpi__value">{{ format_fcfa($stats['credit_outstanding']) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        {{-- Top produits --}}
        <div class="col-lg-6">
            <div class="sp-card h-100">
                <h2 class="h5 mb-3"><i class="bi bi-trophy me-1"></i> Top 10 produits</h2>
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Produit</th>
                                <th class="text-end">Qté vendue</th>
                                <th class="text-end">CA</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($topProducts as $item)
                                <tr>
                                    <td>{{ $item->product?->name ?? '—' }}</td>
                                    <td class="text-end">{{ format_quantity($item->total_qty) }}</td>
                                    <td class="text-end fw-semibold">{{ format_fcfa($item->total_revenue) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center text-muted-soft">Aucune vente.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Modes de paiement --}}
        <div class="col-lg-6">
            <div class="sp-card h-100">
                <h2 class="h5 mb-3"><i class="bi bi-credit-card me-1"></i> Modes de paiement</h2>
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Mode</th>
                                <th class="text-end">Nombre</th>
                                <th class="text-end">CA</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($paymentMethods as $pm)
                                <tr>
                                    <td>{{ ucfirst(str_replace('_', ' ', $pm->payment_method)) }}</td>
                                    <td class="text-end">{{ $pm->count }}</td>
                                    <td class="text-end fw-semibold">{{ format_fcfa($pm->revenue) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Dernières ventes --}}
    <div class="sp-card mt-3">
        <h2 class="h5 mb-3"><i class="bi bi-clock-history me-1"></i> Dernières ventes</h2>
        <div class="table-responsive">
            <table class="table table-sm align-middle mb-0">
                <thead>
                    <tr>
                        <th>Réf.</th>
                        <th>Date</th>
                        <th>Client</th>
                        <th>Caissier</th>
                        <th class="text-end">Total</th>
                        <th>Mode</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recentSales as $sale)
                        <tr>
                            <td class="fw-semibold">{{ $sale->reference }}</td>
                            <td>{{ $sale->sold_at->format('d/m H:i') }}</td>
                            <td>{{ $sale->customer?->name ?? '—' }}</td>
                            <td>{{ $sale->user?->name ?? '—' }}</td>
                            <td class="text-end fw-semibold">{{ format_fcfa($sale->total) }}</td>
                            <td><span class="badge text-bg-secondary">{{ $sale->payment_method }}</span></td>
                            <td class="text-end">
                                <a href="{{ route('pos.receipt', $sale) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-receipt"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted-soft">Aucune vente.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection