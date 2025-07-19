@extends('layouts.app')

@section('title', 'Заказы - Trading App')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Заказы</h1>
    <a href="{{ route('orders.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Создать заказ
    </a>
</div>

<!-- Фильтры -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('orders.index') }}" class="row g-3">
            <div class="col-md-3">
                <label for="status" class="form-label">Статус</label>
                <select name="status" id="status" class="form-select">
                    <option value="">Все статусы</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Активные</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Завершенные</option>
                    <option value="canceled" {{ request('status') == 'canceled' ? 'selected' : '' }}>Отмененные</option>
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
            <div class="col-md-3">
                <label for="customer" class="form-label">Покупатель</label>
                <input type="text" name="customer" id="customer" class="form-control" value="{{ request('customer') }}" placeholder="Поиск по имени">
            </div>
            <div class="col-md-3">
                <label class="form-label">&nbsp;</label>
                <div>
                    <button type="submit" class="btn btn-primary">Фильтровать</button>
                    <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">Сбросить</a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Список заказов -->
<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Покупатель</th>
                <th>Склад</th>
                <th>Статус</th>
                <th>Товары</th>
                <th>Сумма</th>
                <th>Дата</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
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
                <td>{{ number_format($order->getTotalAmount(), 2) }} ₽</td>
                <td>{{ $order->created_at->format('d.m.Y H:i') }}</td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <a href="{{ route('orders.show', $order) }}" class="btn btn-outline-primary" title="Просмотр">
                            <i class="fas fa-eye"></i>
                        </a>
                        @if($order->isActive())
                            <a href="{{ route('orders.edit', $order) }}" class="btn btn-outline-warning" title="Редактировать">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button class="btn btn-outline-success order-action" data-action="complete" data-order-id="{{ $order->id }}" title="Завершить">
                                <i class="fas fa-check"></i>
                            </button>
                            <button class="btn btn-outline-danger order-action" data-action="cancel" data-order-id="{{ $order->id }}" title="Отменить">
                                <i class="fas fa-times"></i>
                            </button>
                        @endif
                        @if($order->isCanceled())
                            <button class="btn btn-outline-info order-action" data-action="resume" data-order-id="{{ $order->id }}" title="Возобновить">
                                <i class="fas fa-redo"></i>
                            </button>
                        @endif
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Пагинация -->
@if($orders->hasPages())
    <div class="d-flex justify-content-center">
        {{ $orders->links() }}
    </div>
@endif

@if($orders->count() == 0)
<div class="text-center">
    <p class="text-muted">Заказы не найдены</p>
</div>
@endif
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('.order-action').click(function() {
        const action = $(this).data('action');
        const orderId = $(this).data('order-id');
        const button = $(this);
        
        if (!confirm('Вы уверены, что хотите выполнить это действие?')) {
            return;
        }
        
        button.prop('disabled', true);
        
        // Отправляем запрос на изменение статуса
        fetch(`/web/orders/${orderId}/${action}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Ошибка: ' + data.message);
            }
        })
        .catch(error => {
            alert('Ошибка: ' + (error.message || 'Неизвестная ошибка'));
        })
        .finally(() => {
            button.prop('disabled', false);
        });
    });
});
</script>
@endpush 