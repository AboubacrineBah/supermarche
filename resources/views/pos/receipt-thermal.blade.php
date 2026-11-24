<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Ticket {{ $sale->reference }}</title>
    <style>
        @page { margin: 0; size: 80mm auto; }
        body { font-family: DejaVu Sans, monospace; font-size: 9pt; width: 72mm; margin: 4mm; color: #000; }
        .center { text-align: center; }
        .right { text-align: right; }
        .bold { font-weight: bold; }
        .line { border-top: 1px dashed #000; margin: 4px 0; }
        .double { border-top: 2px solid #000; margin: 4px 0; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 1px 0; vertical-align: top; }
        .item-name { width: 40%; }
        .item-qty { width: 15%; text-align: center; }
        .item-price { width: 22%; text-align: right; }
        .item-total { width: 23%; text-align: right; }
        .total-row td { font-weight: bold; padding-top: 4px; }
    </style>
</head>
<body>
    <div class="center bold" style="font-size: 12pt;">{{ config('app.name', 'SuguPro') }}</div>
    <div class="center" style="font-size: 8pt;">Supermarché — Bamako, Mali</div>
    <div class="center" style="font-size: 8pt;">Tél: +223 XX XX XX XX</div>
    <div class="line"></div>

    <div style="font-size: 8pt;">
        <div>Réf: {{ $sale->reference }}</div>
        <div>Date: {{ $sale->sold_at->format('d/m/Y H:i') }}</div>
        <div>Caissier: {{ $sale->user?->name ?? '—' }}</div>
        @if ($sale->customer)
            <div>Client: {{ $sale->customer->name }}</div>
        @endif
    </div>
    <div class="line"></div>

    <table>
        <tr>
            <td class="item-name bold">Article</td>
            <td class="item-qty bold">Qté</td>
            <td class="item-price bold">P.U.</td>
            <td class="item-total bold">Total</td>
        </tr>
        @foreach ($sale->items as $item)
            <tr>
                <td class="item-name">{{ $item->product?->name ?? '—' }}</td>
                <td class="item-qty">{{ format_quantity($item->quantity) }}</td>
                <td class="item-price">{{ number_format($item->unit_price, 0, ',', ' ') }}</td>
                <td class="item-total">{{ number_format($item->line_total, 0, ',', ' ') }}</td>
            </tr>
        @endforeach
    </table>
    <div class="line"></div>

    <table>
        <tr>
            <td>Sous-total</td>
            <td class="right">{{ number_format($sale->subtotal, 0, ',', ' ') }}</td>
        </tr>
        @if ($sale->discount > 0)
            <tr>
                <td>Remise</td>
                <td class="right">-{{ number_format($sale->discount, 0, ',', ' ') }}</td>
            </tr>
        @endif
        <tr class="total-row">
            <td>TOTAL</td>
            <td class="right">{{ number_format($sale->total, 0, ',', ' ') }} FCFA</td>
        </tr>
        <tr>
            <td>Payé ({{ $sale->payment_method }})</td>
            <td class="right">{{ number_format($sale->paid_amount, 0, ',', ' ') }}</td>
        </tr>
        <tr>
            <td>Monnaie</td>
            <td class="right">{{ number_format($sale->change_amount, 0, ',', ' ') }}</td>
        </tr>
    </table>
    <div class="double"></div>

    <div class="center" style="font-size: 8pt; margin-top: 6px;">
        Merci de votre visite !<br>
        À bientôt chez SuguPro
    </div>
    <div class="center" style="font-size: 7pt; margin-top: 4px; color: #555;">
        Reçu généré le {{ now()->format('d/m/Y H:i:s') }}
    </div>
</body>
</html>