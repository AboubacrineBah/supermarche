@extends('layouts.app')

@section('title', 'Produits')

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <h1 class="h4 mb-1">Produits</h1>
            <p class="text-muted small mb-0">{{ $products->total() }} référence(s) dans le catalogue.</p>
        </div>
        @can('create', App\Models\Product::class)
            <a href="{{ route('products.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Nouveau produit
            </a>
        @endcan
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('products.index') }}" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label" for="search">Recherche</label>
                    <input type="search" class="form-control" id="search" name="search" value="{{ $search }}"
                        placeholder="Nom, SKU ou code-barres">
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="category_id">Catégorie</label>
                    <select class="form-select" id="category_id" name="category_id">
                        <option value="">Toutes</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected($categoryId === $category->id)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="status">Statut</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Tous</option>
                        <option value="active" @selected($status === 'active')>Actifs</option>
                        <option value="inactive" @selected($status === 'inactive')>Inactifs</option>
                    </select>
                </div>
                <div class="col-md-2 d-grid">
                    <button class="btn btn-outline-primary" type="submit">
                        <i class="bi bi-funnel me-1"></i> Filtrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Produit</th>
                        @if ($isSuperAdmin)
                            <th>Magasin</th>
                        @endif
                        <th>Catégorie</th>
                        <th class="text-end">Prix détail</th>
                        <th class="text-end">Stock min.</th>
                        <th class="text-end">Statut</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $product->name }}</div>
                                <div class="text-muted small">
                                    {{ $product->sku }}
                                    @if ($product->barcode)
                                        · {{ $product->barcode }}
                                    @endif
                                </div>
                            </td>
                            @if ($isSuperAdmin)
                                <td>{{ $product->tenant?->name ?? '—' }}</td>
                            @endif
                            <td>{{ $product->category?->name ?? '—' }}</td>
                            <td class="text-end amount">{{ format_fcfa($product->retail_price) }}</td>
                            <td class="text-end">{{ number_format((float) $product->stock_min, 0, ',', ' ') }}</td>
                            <td class="text-end">
                                <span class="badge {{ $product->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">
                                    {{ $product->is_active ? 'Actif' : 'Inactif' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    @can('view', $product)
                                        <a href="{{ route('products.show', $product) }}" class="btn btn-outline-secondary"
                                            title="Voir">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    @endcan
                                    @can('update', $product)
                                        <a href="{{ route('products.edit', $product) }}" class="btn btn-outline-primary"
                                            title="Modifier">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    @endcan
                                    @can('delete', $product)
                                        <form method="POST" action="{{ route('products.destroy', $product) }}"
                                            onsubmit="return confirm('Supprimer ce produit ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-outline-danger" type="submit" title="Supprimer">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $isSuperAdmin ? 7 : 6 }}" class="text-center text-muted py-4">
                                Aucun produit trouvé.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($products->hasPages())
            <div class="card-body border-top">
                {{ $products->links() }}
            </div>
        @endif
    </div>
@endsection
