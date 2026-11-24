@extends('layouts.app')

@section('title', 'Fermer la caisse')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="sp-card">
                <h1 class="h4 mb-3">Fermer la caisse #{{ $cashSession->id }}</h1>
                <p class="text-muted-soft">Montant attendu : <strong>{{ format_fcfa($expected) }}</strong></p>
                <form method="POST" action="{{ route('pos.close.store', $cashSession) }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Montant compté en caisse (FCFA)</label>
                        <input type="number" name="closing_amount" class="form-control" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-danger">Fermer</button>
                        <a href="{{ route('pos.index') }}" class="btn btn-outline-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection