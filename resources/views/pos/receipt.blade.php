@extends('layouts.app')

@section('title', 'Ticket de caisse')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="sp-card">
                <div class="text-center mb-3">
                    <h1 class="h4 mb-0">Ticket de caisse</h1>
                    <small class="text-muted-soft">Réf. {{ $sale->reference }} — {{ $sale->sold_at->format('d/m/Y H:i') }}</small>
                </div>

                <table class="table table-sm mb-3">
                    <thead>
                        <tr>
                            <th>Produit</th>
                            <th class="text-end">Qté</th>
                            <th class="text-end">P.U.</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sale->items as $item)
                            <tr>
                                <td>{{ $item->product?->name ?? '—' }}</td>
                                <td class="text-end">{{ format_quantity($item->quantity) }}</td>
                                <td class="text-end">{{ format_fcfa($item->unit_price) }}</td>
                                <td class="text-end">{{ format_fcfa($item->line_total) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" class="text-end">Sous-total</th>
                            <th class="text-end">{{ format_fcfa($sale->subtotal) }}</th>
                        </tr>
                        @if ($sale->discount > 0)
                            <tr>
                                <th colspan="3" class="text-end">Remise</th>
                                <th class="text-end">-{{ format_fcfa($sale->discount) }}</th>
                            </tr>
                        @endif
                        <tr>
                            <th colspan="3" class="text-end">Total</th>
                            <th class="text-end">{{ format_fcfa($sale->total) }}</th>
                        </tr>
                        <tr>
                            <th colspan="3" class="text-end">Payé</th>
                            <th class="text-end">{{ format_fcfa($sale->paid_amount) }}</th>
                        </tr>
                        <tr>
                            <th colspan="3" class="text-end">Monnaie</th>
                            <th class="text-end">{{ format_fcfa($sale->change_amount) }}</th>
                        </tr>
                    </tfoot>
                </table>

                <p class="text-muted-soft small mb-3">Mode de paiement : {{ $sale->payment_method }}</p>

                <div class="d-flex gap-2 justify-content-center flex-wrap">
                    <a href="{{ route('pos.index') }}" class="btn btn-primary">
                        <i class="bi bi-arrow-left me-1"></i> Nouvelle vente
                    </a>
                    <a href="{{ route('pos.receipt.pdf', $sale) }}" target="_blank" class="btn btn-outline-primary">
                        <i class="bi bi-file-earmark-pdf me-1"></i> Ticket PDF
                    </a>
                    @if ($sale->customer)
                        <form method="POST" action="{{ route('pos.receipt.whatsapp', $sale) }}" class="d-inline">
                            @csrf
                            <button class="btn btn-outline-success">
                                <i class="bi bi-whatsapp me-1"></i> WhatsApp
                            </button>
                        </form>
                        <form method="POST" action="{{ route('pos.receipt.sms', $sale) }}" class="d-inline">
                            @csrf
                            <button class="btn btn-outline-info">
                                <i class="bi bi-chat-dots me-1"></i> SMS
                            </button>
                        </form>
                    @endif
                    <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
                        <i class="bi bi-printer me-1"></i> Imprimer
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection