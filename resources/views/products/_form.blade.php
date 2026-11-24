@php
    $isEdit = $product !== null;
    $action = $isEdit ? route('products.update', $product) : route('products.store');
@endphp

<form method="POST" action="{{ $action }}">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    <div class="row g-3">
        @if ($isSuperAdmin)
            <div class="col-md-6">
                <label for="tenant_id" class="form-label">Magasin</label>
                @if ($isEdit)
                    <input type="text" class="form-control" value="{{ $product->tenant?->name ?? '—' }}" disabled>
                @else
                    <select class="form-select @error('tenant_id') is-invalid @enderror" id="tenant_id" name="tenant_id" required>
                        <option value="">Sélectionner un magasin</option>
                        @foreach ($tenants as $tenant)
                            <option value="{{ $tenant->id }}" @selected((string) old('tenant_id') === (string) $tenant->id)>
                                {{ $tenant->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('tenant_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                @endif
            </div>
        @endif

        <div class="col-md-6">
            <label for="name" class="form-label">Nom du produit</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                value="{{ old('name', $product?->name) }}" required maxlength="150">
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-3">
            <label for="sku" class="form-label">SKU</label>
            <input type="text" class="form-control @error('sku') is-invalid @enderror" id="sku" name="sku"
                value="{{ old('sku', $product?->sku) }}" maxlength="50" placeholder="Généré si vide">
            @error('sku')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-3">
            <label for="barcode" class="form-label">Code-barres</label>
            <input type="text" class="form-control @error('barcode') is-invalid @enderror" id="barcode" name="barcode"
                value="{{ old('barcode', $product?->barcode) }}" maxlength="50">
            @error('barcode')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-4">
            <label for="category_id" class="form-label">Catégorie</label>
            <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id">
                <option value="">Sans catégorie</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected((string) old('category_id', $product?->category_id) === (string) $category->id)>
                        {{ $category->name }}{{ $isSuperAdmin && ! $isEdit ? ' — '.($category->tenant?->name ?? '') : '' }}
                    </option>
                @endforeach
            </select>
            @error('category_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-4">
            <label for="unit_id" class="form-label">Unité</label>
            <select class="form-select @error('unit_id') is-invalid @enderror" id="unit_id" name="unit_id">
                <option value="">Sans unité</option>
                @foreach ($units as $unit)
                    <option value="{{ $unit->id }}" @selected((string) old('unit_id', $product?->unit_id) === (string) $unit->id)>
                        {{ $unit->name }}{{ $isSuperAdmin && ! $isEdit ? ' — '.($unit->tenant?->name ?? '') : '' }}
                    </option>
                @endforeach
            </select>
            @error('unit_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-4">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                rows="1">{{ old('description', $product?->description) }}</textarea>
            @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-3">
            <label for="cost_price" class="form-label">Prix d'achat (FCFA)</label>
            <input type="number" class="form-control @error('cost_price') is-invalid @enderror" id="cost_price"
                name="cost_price" value="{{ old('cost_price', $product?->cost_price ?? 0) }}" min="0" step="1" required>
            @error('cost_price')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-3">
            <label for="retail_price" class="form-label">Prix de détail (FCFA)</label>
            <input type="number" class="form-control @error('retail_price') is-invalid @enderror" id="retail_price"
                name="retail_price" value="{{ old('retail_price', $product?->retail_price ?? 0) }}" min="0" step="1" required>
            @error('retail_price')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-3">
            <label for="wholesale_price" class="form-label">Prix de gros (FCFA)</label>
            <input type="number" class="form-control @error('wholesale_price') is-invalid @enderror" id="wholesale_price"
                name="wholesale_price" value="{{ old('wholesale_price', $product?->wholesale_price) }}" min="0" step="1">
            @error('wholesale_price')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-3">
            <label for="wholesale_min_qty" class="form-label">Quantité min. gros</label>
            <input type="number" class="form-control @error('wholesale_min_qty') is-invalid @enderror"
                id="wholesale_min_qty" name="wholesale_min_qty"
                value="{{ old('wholesale_min_qty', $product?->wholesale_min_qty) }}" min="0" step="0.01">
            @error('wholesale_min_qty')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-3">
            <label for="stock_min" class="form-label">Stock minimum</label>
            <input type="number" class="form-control @error('stock_min') is-invalid @enderror" id="stock_min"
                name="stock_min" value="{{ old('stock_min', $product?->stock_min ?? 0) }}" min="0" step="0.01" required>
            @error('stock_min')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-3">
            <label for="stock_max" class="form-label">Stock maximum</label>
            <input type="number" class="form-control @error('stock_max') is-invalid @enderror" id="stock_max"
                name="stock_max" value="{{ old('stock_max', $product?->stock_max) }}" min="0" step="0.01">
            @error('stock_max')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-12">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="track_lots" name="track_lots" value="1"
                    @checked(old('track_lots', $product?->track_lots ?? false))>
                <label class="form-check-label" for="track_lots">Suivre les lots et dates de péremption</label>
            </div>
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                    @checked(old('is_active', $product?->is_active ?? true))>
                <label class="form-check-label" for="is_active">Produit actif</label>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mt-4">
        <a href="{{ $isEdit ? route('products.show', $product) : route('products.index') }}"
            class="btn btn-outline-secondary">Annuler</a>
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-check-lg me-1"></i> {{ $isEdit ? 'Mettre à jour' : 'Enregistrer' }}
        </button>
    </div>
</form>
