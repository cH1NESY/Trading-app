@extends('layouts.app')

@section('title', 'История движений товаров')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">История движений товаров</h1>
</div>

<!-- Фильтры -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('stock_movements.index') }}" class="row g-3">
            <div class="col-md-3">
                <label for="product_id" class="form-label">Товар</label>
                <select name="product_id" id="product_id" class="form-select">
                    <option value="">Все товары</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                            {{ $product->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="warehouse_id" class="form-label">Склад</label>
                <select name="warehouse_id" id="warehouse_id" class="form-select">
                    <option value="">Все склады</option>
                    @foreach($warehouses as $warehouse)
                        <option value="{{ $warehouse->id }}" {{ request('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                            {{ $warehouse->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="date_from" class="form-label">Дата от</label>
                <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-2">
                <label for="date_to" class="form-label">Дата до</label>
                <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary">Фильтровать</button>
                <a href="{{ route('stock_movements.index') }}" class="btn btn-outline-secondary ms-2">Сбросить</a>
            </div>
        </form>
    </div>
</div>

<!-- Таблица движений -->
<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Дата</th>
                <th>Товар</th>
                <th>Склад</th>
                <th>Изменение</th>
                <th>Тип</th>
                <th>Описание</th>
            </tr>
        </thead>
        <tbody>
            @foreach($movements as $movement)
            <tr>
                <td>{{ $movement->id }}</td>
                <td>{{ $movement->created_at->format('d.m.Y H:i') }}</td>
                <td>{{ $movement->product->name ?? '-' }}</td>
                <td>{{ $movement->warehouse->name ?? '-' }}</td>
                <td class="fw-bold {{ $movement->quantity > 0 ? 'text-success' : 'text-danger' }}">
                    {{ $movement->quantity > 0 ? '+' : '' }}{{ $movement->quantity }}
                </td>
                <td><span class="badge bg-secondary">{{ $movement->type }}</span></td>
                <td>{{ $movement->description }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Пагинация -->
@if($movements->hasPages())
    <div class="d-flex justify-content-center">
        {{ $movements->links() }}
    </div>
@endif
@endsection 