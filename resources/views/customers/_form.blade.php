<div class="mb-3">
    <label class="form-label">Nom <span class="text-danger">*</span></label>
    <input type="text" name="name" class="form-control" required maxlength="150"
           value="{{ old('name', $customer?->name) }}">
</div>
<div class="row g-3">
    <div class="col-md-6 mb-3">
        <label class="form-label">Téléphone</label>
        <input type="text" name="phone" class="form-control" maxlength="30"
               value="{{ old('phone', $customer?->phone) }}">
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">WhatsApp</label>
        <input type="text" name="whatsapp" class="form-control" maxlength="30"
               value="{{ old('whatsapp', $customer?->whatsapp) }}">
    </div>
</div>
<div class="mb-3">
    <label class="form-label">Email</label>
    <input type="email" name="email" class="form-control" maxlength="255"
           value="{{ old('email', $customer?->email) }}">
</div>
<div class="mb-3">
    <label class="form-label">Adresse</label>
    <input type="text" name="address" class="form-control" maxlength="255"
           value="{{ old('address', $customer?->address) }}">
</div>
<div class="mb-3">
    <label class="form-label">Plafond de crédit (FCFA)</label>
    <input type="number" name="credit_limit" class="form-control" min="0"
           value="{{ old('credit_limit', $customer?->credit_limit ?? 0) }}">
</div>
<div class="form-check mb-3">
    <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active"
           {{ old('is_active', $customer?->is_active ?? true) ? 'checked' : '' }}>
    <label class="form-check-label" for="is_active">Actif</label>
</div>