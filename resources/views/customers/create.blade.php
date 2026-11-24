@extends('layouts.app')

@section('title', 'Nouveau client')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="sp-card">
                <h1 class="h4 mb-3">Nouveau client</h1>
                <form method="POST" action="{{ route('customers.store') }}">
                    @csrf
                    @include('customers._form', ['customer' => null])
                    <div class="d-flex gap-2 mt-3">
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                        <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection