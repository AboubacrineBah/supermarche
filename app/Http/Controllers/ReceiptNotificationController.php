<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Services\Notification\SmsService;
use App\Services\Notification\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class ReceiptNotificationController extends Controller
{
    public function sendWhatsApp(Sale $sale, WhatsAppService $whatsapp): RedirectResponse
    {
        Gate::authorize('pos.sell');

        $sale->load(['customer', 'items.product']);

        $customer = $sale->customer instanceof Customer ? $sale->customer : null;
        $phone = $customer?->whatsapp ?: $customer?->phone;

        if (blank($phone)) {
            return redirect()->route('pos.receipt', $sale)->with('error', 'Aucun numéro WhatsApp pour ce client.');
        }

        $lines = [];
        $foundSale = Sale::query()->find($sale->id);
        $items = $foundSale !== null ? $foundSale->items : collect();

        foreach ($items as $item) {
            $product = Product::find($item->product_id);
            $productName = $product !== null ? $product->name : '—';
            $lines[] = "- {$productName} x{$item->quantity} = ".format_fcfa($item->line_total);
        }

        $message = "🧾 Ticket de caisse SuguPro\n";
        $message .= "Réf: {$sale->reference}\n";
        $message .= 'Date: '.Carbon::parse($sale->sold_at)->format('d/m/Y H:i')."\n";
        $message .= "-------------------\n";
        $message .= implode("\n", $lines)."\n";
        $message .= "-------------------\n";
        $message .= 'Total: '.format_fcfa($sale->total)."\n";
        $message .= 'Payé: '.format_fcfa($sale->paid_amount)."\n";
        $message .= 'Monnaie: '.format_fcfa($sale->change_amount)."\n";
        $message .= 'Merci de votre visite !';

        $result = $whatsapp->send($phone, $message);

        if ($result['success']) {
            return redirect()->route('pos.receipt', $sale)->with('success', 'Reçu envoyé par WhatsApp.');
        }

        return redirect()->route('pos.receipt', $sale)->with('error', 'Échec de l\'envoi WhatsApp.');
    }

    public function sendSms(Sale $sale, SmsService $sms): RedirectResponse
    {
        Gate::authorize('pos.sell');

        $sale->load(['customer']);

        $customer = $sale->customer instanceof Customer ? $sale->customer : null;
        $phone = $customer?->phone;

        if (blank($phone)) {
            return redirect()->route('pos.receipt', $sale)->with('error', 'Aucun numéro pour ce client.');
        }

        $message = "SuguPro - Ticket {$sale->reference} - Total: ".format_fcfa($sale->total).' - Merci!';

        $result = $sms->send($phone, $message);

        if ($result['success']) {
            return redirect()->route('pos.receipt', $sale)->with('success', 'SMS envoyé.');
        }

        return redirect()->route('pos.receipt', $sale)->with('error', 'Échec de l\'envoi SMS.');
    }
}
