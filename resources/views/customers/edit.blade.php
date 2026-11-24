@extends('layouts.app')

@section('title', 'Modifier le client')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="sp-card">
                <h1 class="h4 mb-3">Modifier le client</h1>
                <form method="POST" action="{{ route('customers.update', $customer) }}">
                    @csrf
                    @method('PUT')
                    @include('customers._form', ['customer' => $customer])
                    <div class="d-flex gap-2 mt-3">
                        <button type="submit" class="btn btn-primary">Mettre à jour</button>
                        <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection