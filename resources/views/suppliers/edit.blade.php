@extends('layouts.app')

@section('title', 'Modifier '.$supplier->name)

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <h1 class="h4 mb-0">Modifier {{ $supplier->name }}</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('suppliers.show', $supplier) }}" class="btn btn-outline-secondary">
                <i class="bi bi-eye me-1"></i> Voir
            </a>
            <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Retour
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            @include('suppliers._form', ['supplier' => $supplier])
        </div>
    </div>
@endsection
