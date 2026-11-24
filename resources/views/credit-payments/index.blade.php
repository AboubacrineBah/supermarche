@extends('layouts.app')

@section('title', 'Crédits clients')

@section('content')
    <h1 class="h4 mb-3">Crédits clients à encaisser</h1>

    <div class="sp-card">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Client</th>
                        <th>Téléphone</th>
                        <th class="text-end">Solde crédit</th>
                        <th class="text-end">Points fidélité</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($customers as $customer)
                        <tr>
                            <td class="fw-semibold">{{ $customer->name }}</td>
                            <td>{{ $customer->phone ?? '—' }}</td>
                            <td class="text-end text-danger fw-bold">{{ format_fcfa($customer->credit_balance) }}</td>
                            <td class="text-end">{{ number_format($customer->loyalty_points) }}</td>
                            <td class="text-end">
                                @can('credit.grant')
                                    <a href="{{ route('credit-payments.create', $customer) }}" class="btn btn-sm btn-outline-success">
                                        <i class="bi bi-cash-coin me-1"></i> Encaisser
                                    </a>
                                    <form method="POST" action="{{ route('credit-payments.remind', $customer) }}" class="d-inline">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-warning" title="Relancer">
                                            <i class="bi bi-whatsapp"></i>
                                        </button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted-soft py-4">Aucun crédit en cours.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $customers->links() }}
    </div>
@endsection