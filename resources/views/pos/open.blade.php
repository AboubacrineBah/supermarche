@extends('layouts.app')

@section('title', 'Ouvrir une caisse')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="sp-card">
                <h1 class="h4 mb-3">Ouvrir une caisse</h1>
                <form method="POST" action="{{ route('pos.open.store') }}">
                    @csrf
                    @if (auth()->user()?->hasRole('super-admin'))
                        <div class="mb-3">
                            <label class="form-label">Magasin <span class="text-danger">*</span></label>
                            <select name="tenant_id" class="form-select" required>
                                <option value="">Choisir un magasin</option>
                                @foreach ($tenants as $tenant)
                                    <option value="{{ $tenant->id }}">{{ $tenant->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    <div class="mb-3">
                        <label class="form-label">Fond de caisse initial (FCFA)</label>
                        <input type="number" name="opening_amount" class="form-control" min="0" value="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Ouvrir</button>
                        <a href="{{ route('pos.index') }}" class="btn btn-outline-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection