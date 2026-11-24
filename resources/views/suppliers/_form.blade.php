@php
    $isEdit = isset($supplier) && $supplier !== null;
    $action = $isEdit ? route('suppliers.update', $supplier) : route('suppliers.store');
@endphp

<form method="POST" action="{{ $action }}">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    <div class="row g-3">
        <div class="col-md-6">
            <label for="name" class="form-label">Nom du fournisseur</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                value="{{ old('name', $supplier->name ?? '') }}" required maxlength="150">
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-3">
            <label for="nif" class="form-label">NIF</label>
            <input type="text" class="form-control @error('nif') is-invalid @enderror" id="nif" name="nif"
                value="{{ old('nif', $supplier->nif ?? '') }}" maxlength="50">
            @error('nif')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-3">
            <label for="payment_terms" class="form-label">Conditions de paiement</label>
            <select class="form-select @error('payment_terms') is-invalid @enderror" id="payment_terms" name="payment_terms">
                <option value="cash" @selected(old('payment_terms', $supplier->payment_terms ?? 'cash') === 'cash')>Comptant</option>
                <option value="15_days" @selected(old('payment_terms', $supplier->payment_terms ?? '') === '15_days')>15 jours</option>
                <option value="30_days" @selected(old('payment_terms', $supplier->payment_terms ?? '') === '30_days')>30 jours</option>
                <option value="45_days" @selected(old('payment_terms', $supplier->payment_terms ?? '') === '45_days')>45 jours</option>
                <option value="60_days" @selected(old('payment_terms', $supplier->payment_terms ?? '') === '60_days')>60 jours</option>
            </select>
            @error('payment_terms')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-4">
            <label for="phone" class="form-label">Téléphone</label>
            <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone"
                value="{{ old('phone', $supplier->phone ?? '') }}" maxlength="30">
            @error('phone')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-4">
            <label for="whatsapp" class="form-label">WhatsApp</label>
            <input type="text" class="form-control @error('whatsapp') is-invalid @enderror" id="whatsapp" name="whatsapp"
                value="{{ old('whatsapp', $supplier->whatsapp ?? '') }}" maxlength="30">
            @error('whatsapp')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-4">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email"
                value="{{ old('email', $supplier->email ?? '') }}" maxlength="255">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-8">
            <label for="address" class="form-label">Adresse</label>
            <input type="text" class="form-control @error('address') is-invalid @enderror" id="address" name="address"
                value="{{ old('address', $supplier->address ?? '') }}" maxlength="255">
            @error('address')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-4">
            <label for="debt_limit" class="form-label">Plafond de dette (FCFA)</label>
            <input type="number" class="form-control @error('debt_limit') is-invalid @enderror" id="debt_limit"
                name="debt_limit" value="{{ old('debt_limit', $supplier->debt_limit ?? '') }}" min="0" step="1">
            @error('debt_limit')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-12">
            <label for="notes" class="form-label">Notes</label>
            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="2"
                maxlength="2000">{{ old('notes', $supplier->notes ?? '') }}</textarea>
            @error('notes')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-12">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                    @checked(old('is_active', $supplier->is_active ?? true))>
                <label class="form-check-label" for="is_active">Fournisseur actif</label>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mt-4">
        <a href="{{ $isEdit ? route('suppliers.show', $supplier) : route('suppliers.index') }}"
            class="btn btn-outline-secondary">Annuler</a>
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-check-lg me-1"></i> {{ $isEdit ? 'Mettre à jour' : 'Enregistrer' }}
        </button>
    </div>
</form>
