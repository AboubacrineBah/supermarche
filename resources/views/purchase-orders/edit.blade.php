@extends('layouts.app')

@section('title', 'Modifier ' . $purchaseOrder->reference)

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <h1 class="h4 mb-0">Modifier {{ $purchaseOrder->reference }}</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('purchase-orders.show', $purchaseOrder) }}" class="btn btn-outline-secondary">
                <i class="bi bi-eye me-1"></i> Voir
            </a>
            <a href="{{ route('purchase-orders.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Retour
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            @include('purchase-orders._form', ['purchaseOrder' => $purchaseOrder])
        </div>
    </div>
@endsection