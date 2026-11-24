@extends('layouts.app')

@section('title', 'Encaisser un crédit')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="sp-card">
                <h1 class="h4 mb-3">Encaisser le crédit de {{ $customer->name }}</h1>
                <p class="text-muted-soft">Solde dû : <strong class="text-danger">{{ format_fcfa($customer->credit_balance) }}</strong></p>

                <form method="POST" action="{{ route('credit-payments.store', $customer) }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Montant (FCFA)</label>
                        <input type="number" name="amount" class="form-control" min="1" max="{{ $customer->credit_balance }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mode de paiement</label>
                        <select name="method" class="form-select" required>
                            <option value="cash">Espèces</option>
                            <option value="orange_money">Orange Money</option>
                            <option value="wave">Wave</option>
                            <option value="moov_money">Moov Money</option>
                            <option value="card">Carte</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success">Encaisser</button>
                        <a href="{{ route('credit-payments.index') }}" class="btn btn-outline-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection