@extends('layouts.app')

@section('title', 'Caisse (POS)')

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($session === null)
        <div class="sp-card text-center py-5">
            <i class="bi bi-cash-register" style="font-size: 4rem; color: #94a3b8;"></i>
            <h2 class="h4 mt-3">Aucune caisse ouverte</h2>
            <p class="text-muted-soft">Ouvrez une caisse pour commencer à vendre.</p>
            @can('cash.open')
                <a href="{{ route('pos.open') }}" class="btn btn-primary btn-lg">
                    <i class="bi bi-unlock me-1"></i> Ouvrir une caisse
                </a>
            @endcan
        </div>
    @else
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div>
                <h1 class="h4 mb-0">Caisse #{{ $session->id }}</h1>
                <small class="text-muted-soft">Ouverte le {{ $session->opened_at->format('d/m/Y H:i') }} — Fond : {{ format_fcfa($session->opening_amount) }}</small>
            </div>
            @can('cash.close')
                <a href="{{ route('pos.close', $session) }}" class="btn btn-outline-danger">
                    <i class="bi bi-lock me-1"></i> Fermer la caisse
                </a>
            @endcan
        </div>

        <form method="POST" action="{{ route('pos.sale.store') }}" id="posForm">
            @csrf
            <div class="row g-3">
                <div class="col-lg-7">
                    <div class="sp-card">
                        <h2 class="h5 mb-3"><i class="bi bi-grid me-1"></i> Produits</h2>
                        <div class="row g-2" id="productGrid">
                            @foreach ($products as $product)
                                @php $stockQty = (float) ($product->stock?->quantity ?? 0); @endphp
                                <div class="col-6 col-md-4 col-xl-3">
                                    <div class="card h-100 product-card {{ $stockQty <= 0 ? 'opacity-50' : '' }}"
                                         data-id="{{ $product->id }}"
                                         data-name="{{ $product->name }}"
                                         data-price="{{ $product->retail_price }}"
                                         data-stock="{{ $stockQty }}"
                                         style="cursor: pointer;">
                                        <div class="card-body p-2 text-center">
                                            <div class="fw-semibold small">{{ $product->name }}</div>
                                            <div class="text-muted-soft" style="font-size: 0.75rem;">{{ $product->sku }}</div>
                                            <div class="fw-bold text-primary mt-1">{{ format_fcfa($product->retail_price) }}</div>
                                            <span class="badge {{ $stockQty <= 0 ? 'text-bg-danger' : ($stockQty <= (float) $product->stock_min ? 'text-bg-warning' : 'text-bg-success') }}" style="font-size: 0.7rem;">
                                                Stock: {{ format_quantity($stockQty) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="sp-card">
                        <h2 class="h5 mb-3"><i class="bi bi-cart3 me-1"></i> Panier</h2>
                        <div id="cartItems" class="mb-3" style="min-height: 120px;">
                            <p class="text-muted-soft text-center py-4" id="emptyCart">Panier vide — cliquez sur un produit</p>
                        </div>
                        <table class="table table-sm mb-2">
                            <tr>
                                <td class="fw-semibold">Sous-total</td>
                                <td class="text-end" id="subtotal">0 FCFA</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Remise</td>
                                <td class="text-end">
                                    <input type="number" name="discount" id="discount" class="form-control form-control-sm text-end" min="0" value="0" style="width: 100px; display: inline-block;">
                                </td>
                            </tr>
                            <tr class="table-primary">
                                <td class="fw-bold">TOTAL</td>
                                <td class="text-end fw-bold fs-5" id="total">0 FCFA</td>
                            </tr>
                        </table>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Client (optionnel)</label>
                            <select name="customer_id" class="form-select form-select-sm">
                                <option value="">— Aucun —</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->name }} (crédit: {{ format_fcfa($customer->credit_balance) }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Mode de paiement</label>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach (['cash' => 'Espèces', 'orange_money' => 'Orange Money', 'wave' => 'Wave', 'moov_money' => 'Moov Money', 'card' => 'Carte', 'credit' => 'Crédit'] as $val => $label)
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" id="pm_{{ $val }}" value="{{ $val }}" {{ $val === 'cash' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="pm_{{ $val }}">{{ $label }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Montant reçu (FCFA)</label>
                            <input type="number" name="paid_amount" id="paidAmount" class="form-control form-control-lg text-end" min="0" value="0" required>
                        </div>

                        <div class="mb-3 text-end">
                            <span class="text-muted-soft">Monnaie : </span>
                            <span class="fw-bold fs-5" id="change">0 FCFA</span>
                        </div>

                        <div id="cartInputs"></div>
                        <button type="submit" class="btn btn-success btn-lg w-100" id="submitBtn" disabled>
                            <i class="bi bi-check2-circle me-1"></i> Valider la vente
                        </button>
                    </div>
                </div>
            </div>
        </form>
    @endif

    <script>
        (function() {
            const cart = {};
            const cartItemsEl = document.getElementById('cartItems');
            const cartInputsEl = document.getElementById('cartInputs');
            const emptyCartEl = document.getElementById('emptyCart');
            const subtotalEl = document.getElementById('subtotal');
            const totalEl = document.getElementById('total');
            const discountEl = document.getElementById('discount');
            const paidEl = document.getElementById('paidAmount');
            const changeEl = document.getElementById('change');
            const submitBtn = document.getElementById('submitBtn');

            function formatFcfa(n) {
                return new Intl.NumberFormat('fr-FR').format(Math.round(n)) + ' FCFA';
            }

            function renderCart() {
                const keys = Object.keys(cart);
                if (keys.length === 0) {
                    emptyCartEl.style.display = '';
                    cartItemsEl.innerHTML = '<p class="text-muted-soft text-center py-4" id="emptyCart">Panier vide — cliquez sur un produit</p>';
                    cartInputsEl.innerHTML = '';
                    submitBtn.disabled = true;
                } else {
                    let html = '<div class="list-group list-group-flush">';
                    keys.forEach(id => {
                        const c = cart[id];
                        html += '<div class="list-group-item d-flex justify-content-between align-items-center py-2">';
                        html += '<div><div class="fw-semibold small">' + c.name + '</div>';
                        html += '<small class="text-muted">' + formatFcfa(c.price) + ' × ' + c.qty + '</small></div>';
                        html += '<div class="d-flex align-items-center gap-2">';
                        html += '<button type="button" class="btn btn-sm btn-outline-secondary" onclick="changeQty(' + id + ', -1)">−</button>';
                        html += '<span class="fw-bold">' + c.qty + '</span>';
                        html += '<button type="button" class="btn btn-sm btn-outline-secondary" onclick="changeQty(' + id + ', 1)">+</button>';
                        html += '<button type="button" class="btn btn-sm btn-outline-danger" onclick="removeItem(' + id + ')"><i class="bi bi-trash"></i></button>';
                        html += '</div></div>';
                    });
                    html += '</div>';
                    cartItemsEl.innerHTML = html;

                    // Hidden inputs
                    let inputs = '';
                    keys.forEach(id => {
                        const c = cart[id];
                        inputs += '<input type="hidden" name="items[' + id + '][product_id]" value="' + id + '">';
                        inputs += '<input type="hidden" name="items[' + id + '][quantity]" value="' + c.qty + '">';
                    });
                    cartInputsEl.innerHTML = inputs;
                    submitBtn.disabled = false;
                }

                let subtotal = 0;
                keys.forEach(id => { subtotal += cart[id].price * cart[id].qty; });
                const discount = parseFloat(discountEl.value) || 0;
                const total = Math.max(0, subtotal - discount);
                subtotalEl.textContent = formatFcfa(subtotal);
                totalEl.textContent = formatFcfa(total);

                const paid = parseFloat(paidEl.value) || 0;
                changeEl.textContent = formatFcfa(Math.max(0, paid - total));
            }

            window.changeQty = function(id, delta) {
                if (!cart[id]) return;
                cart[id].qty = Math.max(1, cart[id].qty + delta);
                if (cart[id].qty <= 0) delete cart[id];
                renderCart();
            };
            window.removeItem = function(id) {
                delete cart[id];
                renderCart();
            };

            document.querySelectorAll('.product-card').forEach(card => {
                card.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const name = this.dataset.name;
                    const price = parseFloat(this.dataset.price);
                    const stock = parseFloat(this.dataset.stock);
                    if (stock <= 0) return;
                    if (cart[id]) {
                        if (cart[id].qty < stock) cart[id].qty++;
                    } else {
                        cart[id] = { name, price, qty: 1 };
                    }
                    renderCart();
                });
            });

            discountEl.addEventListener('input', renderCart);
            paidEl.addEventListener('input', renderCart);
        })();
    </script>
@endsection