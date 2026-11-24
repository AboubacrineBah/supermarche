@extends('layouts.app')

@section('title', 'Nouveau bon de commande')

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <h1 class="h4 mb-0">Nouveau bon de commande</h1>
        <a href="{{ route('purchase-orders.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Retour
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            @include('purchase-orders._form')
        </div>
    </div>
@endsection