@extends('layouts.app')

@section('title', 'Товары - Trading App')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Товары</h1>
</div>

<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Название</th>
                <th>Цена</th>
                <th>Остатки по складам</th>
                <th>Общий остаток</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
            <tr>
                <td>{{ $product->id }}</td>
                <td>{{ $product->name }}</td>
                <td>{{ number_format($product->price, 2) }} ₽</td>
                <td>
                    @foreach($product->stocks as $stock)
                        <span class="badge bg-secondary me-1">
                            {{ $stock->warehouse->name }}: {{ $stock->stock }}
                        </span>
                    @endforeach
                </td>
                <td>
                    <span class="badge bg-primary">
                        {{ $product->stocks->sum('stock') }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@if($products->count() == 0)
<div class="text-center">
    <p class="text-muted">Товары не найдены</p>
</div>
@endif
@endsection 