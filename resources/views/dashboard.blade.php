@extends('layouts.app')

@section('title', 'Дашборд - Trading App')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Дашборд</h1>
</div>

<!-- Статистика -->
<div class="row">
    <div class="col-md-2">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">{{ $stats['total_warehouses'] }}</h5>
                <p class="card-text">Складов</p>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">{{ $stats['total_products'] }}</h5>
                <p class="card-text">Товаров</p>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">{{ $stats['total_orders'] }}</h5>
                <p class="card-text">Всего заказов</p>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title text-success">{{ $stats['active_orders'] }}</h5>
                <p class="card-text">Активных</p>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title text-primary">{{ $stats['completed_orders'] }}</h5>
                <p class="card-text">Завершенных</p>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title text-danger">{{ $stats['canceled_orders'] }}</h5>
                <p class="card-text">Отмененных</p>
            </div>
        </div>
    </div>
</div>

<!-- Последние заказы -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Последние заказы</h5>
            </div>
            <div class="card-body">
                @if($recent_orders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Покупатель</th>
                                    <th>Склад</th>
                                    <th>Статус</th>
                                    <th>Товары</th>
                                    <th>Дата</th>
                                    <th>Действия</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recent_orders as $order)
                                <tr>
                                    <td>{{ $order->id }}</td>
                                    <td>{{ $order->customer }}</td>
                                    <td>{{ $order->warehouse->name }}</td>
                                    <td>
                                        <span class="badge status-{{ $order->status }}">
                                            @switch($order->status)
                                                @case('active')
                                                    Активный
                                                    @break
                                                @case('completed')
                                                    Завершен
                                                    @break
                                                @case('canceled')
                                                    Отменен
                                                    @break
                                            @endswitch
                                        </span>
                                    </td>
                                    <td>
                                        @foreach($order->items as $item)
                                            <small>{{ $item->product->name }} ({{ $item->count }})</small><br>
                                        @endforeach
                                    </td>
                                    <td>{{ $order->created_at->format('d.m.Y H:i') }}</td>
                                    <td>
                                        <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted">Заказов пока нет</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 