@php
    $purchaseOrder = $purchaseOrder ?? null;
    $isEdit = isset($purchaseOrder) && $purchaseOrder !== null;
    $action = $isEdit ? route('purchase-orders.update', $purchaseOrder) : route('purchase-orders.store');
@endphp

<form method="POST" action="{{ $action }}" id="purchaseOrderForm">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    @if (auth()->user()?->hasRole('super-admin'))
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label for="tenant_id" class="form-label">Magasin <span class="text-danger">*</span></label>
                <select class="form-select @error('tenant_id') is-invalid @enderror" id="tenant_id" name="tenant_id" required>
                    <option value="">Choisir un magasin</option>
                    @foreach ($tenants ?? [] as $tenant)
                        <option value="{{ $tenant->id }}" @selected(old('tenant_id', $purchaseOrder?->tenant_id ?? '') == $tenant->id)>
                            {{ $tenant->name }}
                        </option>
                    @endforeach
                </select>
                @error('tenant_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <label for="reference" class="form-label">Référence</label>
            <input type="text" class="form-control @error('reference') is-invalid @enderror" id="reference" name="reference"
                value="{{ old('reference', $purchaseOrder?->reference ?? '') }}" required maxlength="30">
            @error('reference')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-3">
            <label for="supplier_id" class="form-label">Fournisseur</label>
            <select class="form-select @error('supplier_id') is-invalid @enderror" id="supplier_id" name="supplier_id" required>
                <option value="">Choisir un fournisseur</option>
                @foreach ($suppliers as $supplier)
                    <option value="{{ $supplier->id }}" @selected(old('supplier_id', $purchaseOrder?->supplier_id ?? '') == $supplier->id)>
                        {{ $supplier->name }}
                    </option>
                @endforeach
            </select>
            @error('supplier_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-3">
            <label for="ordered_at" class="form-label">Date de commande</label>
            <input type="date" class="form-control @error('ordered_at') is-invalid @enderror" id="ordered_at" name="ordered_at"
                value="{{ old('ordered_at', $purchaseOrder?->ordered_at?->format('Y-m-d') ?? now()->format('Y-m-d')) }}" required>
            @error('ordered_at')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-3">
            <label for="expected_at" class="form-label">Date prévue</label>
            <input type="date" class="form-control @error('expected_at') is-invalid @enderror" id="expected_at" name="expected_at"
                value="{{ old('expected_at', $purchaseOrder?->expected_at?->format('Y-m-d') ?? '') }}">
            @error('expected_at')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-3">
            <label for="status" class="form-label">Statut</label>
            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                @foreach (\App\Enums\PurchaseOrderStatus::cases() as $status)
                    <option value="{{ $status->value }}" @selected(old('status', $purchaseOrder?->status?->value ?? \App\Enums\PurchaseOrderStatus::Draft->value) === $status->value)>
                        {{ $status->label() }}
                    </option>
                @endforeach
            </select>
            @error('status')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-12">
            <label for="notes" class="form-label">Notes</label>
            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="2"
                maxlength="2000">{{ old('notes', $purchaseOrder?->notes ?? '') }}</textarea>
            @error('notes')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <h2 class="h5 mb-3">Lignes de commande</h2>

    <div class="table-responsive mb-3">
        <table class="table align-middle" id="itemsTable">
            <thead>
                <tr>
                    <th style="width: 40%">Produit</th>
                    <th style="width: 15%">Quantité</th>
                    <th style="width: 20%">Prix unitaire (FCFA)</th>
                    <th style="width: 15%" class="text-end">Total ligne</th>
                    <th style="width: 10%"></th>
                </tr>
            </thead>
            <tbody>
                @php
                    $items = old('items', []);
                    if ($isEdit && isset($purchaseOrder) && $purchaseOrder && empty($items)) {
                        $items = $purchaseOrder->items->map(function ($item) {
                            return [
                                'product_id' => $item->product_id,
                                'quantity' => $item->quantity,
                                'unit_cost' => $item->unit_cost,
                            ];
                        })->toArray();
                    }
                    if (empty($items)) {
                        $items = [['product_id' => '', 'quantity' => '', 'unit_cost' => '']];
                    }
                @endphp
                @foreach ($items as $index => $item)
                    <tr class="item-row">
                        <td>
                            <select name="items[{{ $index }}][product_id]" class="form-select product-select @error("items.{$index}.product_id") is-invalid @enderror" required>
                                <option value="">Choisir un produit</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}" @selected(($item['product_id'] ?? '') == $product->id)>
                                        {{ $product->name }} ({{ $product->sku }})
                                    </option>
                                @endforeach
                            </select>
                            @error("items.{$index}.product_id")
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </td>
                        <td>
                            <input type="number" name="items[{{ $index }}][quantity]" class="form-control qty @error("items.{$index}.quantity") is-invalid @enderror"
                                value="{{ old("items.{$index}.quantity", $item['quantity'] ?? '') }}" step="0.01" min="0.01" required>
                            @error("items.{$index}.quantity")
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </td>
                        <td>
                            <input type="number" name="items[{{ $index }}][unit_cost]" class="form-control price @error("items.{$index}.unit_cost") is-invalid @enderror"
                                value="{{ old("items.{$index}.unit_cost", $item['unit_cost'] ?? '') }}" step="1" min="0" required>
                            @error("items.{$index}.unit_cost")
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </td>
                        <td class="text-end line-total fw-semibold">0</td>
                        <td>
                            <button type="button" class="btn btn-sm btn-outline-danger remove-row" @if(count($items) === 1) disabled @endif>
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="text-end fw-semibold">Total :</td>
                    <td class="text-end fw-bold fs-5" id="grandTotal">0</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="d-flex gap-2">
        <button type="button" class="btn btn-outline-secondary" id="addItemBtn">
            <i class="bi bi-plus-lg me-1"></i> Ajouter une ligne
        </button>
        <a href="{{ $isEdit && isset($purchaseOrder) ? route('purchase-orders.show', $purchaseOrder) : route('purchase-orders.index') }}"
            class="btn btn-outline-secondary">Annuler</a>
        <button type="submit" class="btn btn-primary ms-auto">
            <i class="bi bi-check-lg me-1"></i> {{ $isEdit ? 'Mettre à jour' : 'Enregistrer' }}
        </button>
    </div>
</form>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const table = document.getElementById('itemsTable');
        const tbody = table.querySelector('tbody');
        const addItemBtn = document.getElementById('addItemBtn');
        const grandTotalEl = document.getElementById('grandTotal');

        function recalc() {
            let total = 0;
            document.querySelectorAll('.item-row').forEach(row => {
                const qty = parseFloat(row.querySelector('.qty').value) || 0;
                const price = parseFloat(row.querySelector('.price').value) || 0;
                const lineTotal = qty * price;
                row.querySelector('.line-total').textContent = formatFcfa(lineTotal);
                total += lineTotal;
            });
            grandTotalEl.textContent = formatFcfa(total);
        }

        function formatFcfa(amount) {
            return new Intl.NumberFormat('fr-FR', { minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(amount) + ' FCFA';
        }

        tbody.addEventListener('input', recalc);

        addItemBtn.addEventListener('click', function () {
            const rows = tbody.querySelectorAll('.item-row');
            const newIndex = rows.length;
            const row = document.createElement('tr');
            row.className = 'item-row';
            row.innerHTML = `
                <td>
                    <select name="items[${newIndex}][product_id]" class="form-select product-select" required>
                        <option value="">Choisir un produit</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->sku }})</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <input type="number" name="items[${newIndex}][quantity]" class="form-control qty" step="0.01" min="0.01" required>
                </td>
                <td>
                    <input type="number" name="items[${newIndex}][unit_cost]" class="form-control price" step="1" min="0" required>
                </td>
                <td class="text-end line-total fw-semibold">0</td>
                <td>
                    <button type="button" class="btn btn-sm btn-outline-danger remove-row">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(row);
            recalc();
        });

        tbody.addEventListener('click', function (e) {
            const btn = e.target.closest('.remove-row');
            if (btn) {
                const rows = tbody.querySelectorAll('.item-row');
                if (rows.length > 1) {
                    btn.closest('tr').remove();
                    // Renommer les index
                    tbody.querySelectorAll('.item-row').forEach((r, i) => {
                        r.querySelectorAll('[name]').forEach(el => {
                            const name = el.getAttribute('name').replace(/items\[\d+\]/, `items[${i}]`);
                            el.setAttribute('name', name);
                        });
                    });
                    recalc();
                }
            }
        });

        recalc();
    });
</script>
@endpush