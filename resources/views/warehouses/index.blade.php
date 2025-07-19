@extends('layouts.app')

@section('title', 'Склады - Trading App')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Склады</h1>
</div>

<div class="row">
    @foreach($warehouses as $warehouse)
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">
                    <i class="fas fa-warehouse text-primary"></i>
                    {{ $warehouse->name }}
                </h5>
                <p class="card-text">
                    <strong>Заказов:</strong> {{ $warehouse->orders_count }}
                </p>
                <p class="card-text">
                    <small class="text-muted">
                        Создан: {{ $warehouse->created_at->format('d.m.Y') }}
                    </small>
                </p>
            </div>
        </div>
    </div>
    @endforeach
</div>

@if($warehouses->count() == 0)
<div class="text-center">
    <p class="text-muted">Склады не найдены</p>
</div>
@endif
@endsection 