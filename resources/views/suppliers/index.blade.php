@extends('layouts.app')

@section('title', 'Fournisseurs')

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <h1 class="h4 mb-1">Fournisseurs</h1>
            <p class="text-muted small mb-0">{{ $suppliers->total() }} fournisseur(s) enregistré(s).</p>
        </div>
        @can('purchases.manage')
            <a href="{{ route('suppliers.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Nouveau fournisseur
            </a>
        @endcan
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('suppliers.index') }}" class="row g-2 align-items-end">
                <div class="col-md-6">
                    <label class="form-label" for="search">Recherche</label>
                    <input type="search" class="form-control" id="search" name="search" value="{{ $search }}"
                        placeholder="Nom du fournisseur">
                </div>
                <div class="col-md-4">
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
                        <th>Fournisseur</th>
                        <th>Contact</th>
                        <th>Conditions</th>
                        <th class="text-end">Statut</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($suppliers as $supplier)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $supplier->name }}</div>
                                @if ($supplier->nif)
                                    <div class="text-muted small">NIF : {{ $supplier->nif }}</div>
                                @endif
                            </td>
                            <td>
                                @if ($supplier->phone)
                                    <div><i class="bi bi-telephone me-1"></i>{{ $supplier->phone }}</div>
                                @endif
                                @if ($supplier->whatsapp)
                                    <div class="text-muted small"><i class="bi bi-whatsapp me-1"></i>{{ $supplier->whatsapp }}</div>
                                @endif
                                @if (! $supplier->phone && ! $supplier->whatsapp)
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>{{ $supplier->paymentTermsLabel() }}</td>
                            <td class="text-end">
                                <span class="badge {{ $supplier->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">
                                    {{ $supplier->is_active ? 'Actif' : 'Inactif' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    @can('purchases.manage')
                                        <a href="{{ route('suppliers.show', $supplier) }}" class="btn btn-outline-secondary"
                                            title="Voir">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-outline-primary"
                                            title="Modifier">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form method="POST" action="{{ route('suppliers.destroy', $supplier) }}"
                                            onsubmit="return confirm('Supprimer ce fournisseur ?');">
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
                            <td colspan="5" class="text-center text-muted py-4">Aucun fournisseur trouvé.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($suppliers->hasPages())
            <div class="card-body border-top">
                {{ $suppliers->links() }}
            </div>
        @endif
    </div>
@endsection
