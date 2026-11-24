<?php

namespace App\Http\Controllers;

use App\Models\CreditPayment;
use App\Models\Customer;
use App\Services\Notification\WhatsAppService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class CreditPaymentController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('credit.grant');

        $customers = Customer::query()
            ->where('credit_balance', '>', 0)
            ->orderBy('name')
            ->paginate(20);

        return view('credit-payments.index', compact('customers'));
    }

    public function create(Customer $customer): View
    {
        Gate::authorize('credit.grant');

        return view('credit-payments.create', compact('customer'));
    }

    public function remind(Customer $customer, WhatsAppService $whatsapp): RedirectResponse
    {
        Gate::authorize('credit.grant');

        $phone = $customer->whatsapp ?: $customer->phone;

        if (blank($phone)) {
            return redirect()->route('credit-payments.index')->with('error', 'Aucun numéro pour ce client.');
        }

        $message = "Bonjour {$customer->name}, votre solde crédit chez ".config('app.name').' est de '.format_fcfa($customer->credit_balance).'. Merci de régulariser.';

        $result = $whatsapp->send($phone, $message);

        if ($result['success']) {
            return redirect()->route('credit-payments.index')->with('success', 'Relance envoyée.');
        }

        return redirect()->route('credit-payments.index')->with('error', 'Échec de l\'envoi.');
    }

    public function store(Request $request, Customer $customer): RedirectResponse
    {
        Gate::authorize('credit.grant');

        $data = $request->validate([
            'amount' => ['required', 'integer', 'min:1', 'max:'.max(1, $customer->credit_balance)],
            'method' => ['required', 'in:cash,orange_money,wave,moov_money,card'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($request, $customer, $data): void {
            CreditPayment::create([
                'customer_id' => $customer->id,
                'user_id' => $request->user()->id,
                'amount' => (int) $data['amount'],
                'method' => $data['method'],
                'notes' => $data['notes'] ?? null,
                'paid_at' => now(),
            ]);

            $customer->decrement('credit_balance', (int) $data['amount']);
        });

        return redirect()->route('credit-payments.index')->with('success', 'Paiement de crédit enregistré.');
    }
}
