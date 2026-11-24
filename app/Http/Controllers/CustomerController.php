<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('customers.manage');

        $search = $request->string('search')->trim()->toString();
        $escaped = str_replace(['%', '_'], ['\\%', '\\_'], $search);

        $customers = Customer::query()
            ->when($search, fn ($q) => $q->where('name', 'like', '%'.$escaped.'%')
                ->orWhere('phone', 'like', '%'.$escaped.'%'))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('customers.index', compact('customers', 'search'));
    }

    public function create(): View
    {
        Gate::authorize('customers.manage');

        return view('customers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('customers.manage');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:30'],
            'whatsapp' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'credit_limit' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        Customer::create($data);

        return redirect()->route('customers.index')->with('success', 'Client créé.');
    }

    public function edit(Customer $customer): View
    {
        Gate::authorize('customers.manage');

        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer): RedirectResponse
    {
        Gate::authorize('customers.manage');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:30'],
            'whatsapp' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'credit_limit' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        $customer->update($data);

        return redirect()->route('customers.index')->with('success', 'Client mis à jour.');
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        Gate::authorize('customers.manage');

        if ($customer->sales()->exists()) {
            return redirect()->route('customers.index')->with('error', 'Ce client a des ventes : désactivez-le.');
        }

        $customer->delete();

        return redirect()->route('customers.index')->with('success', 'Client supprimé.');
    }
}
