@extends('layouts.app')

@section('title', 'Заказ #' . $order->id . ' - Trading App')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Заказ #{{ $order->id }}</h1>
    <div>
        <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Назад к списку
        </a>
        @if($order->isActive())
            <a href="{{ route('orders.edit', $order) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Редактировать
            </a>
        @endif
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <!-- Информация о заказе -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Информация о заказе</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Покупатель:</strong> {{ $order->customer }}</p>
                        <p><strong>Склад:</strong> {{ $order->warehouse->name }}</p>
                        <p><strong>Статус:</strong> 
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
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Дата создания:</strong> {{ $order->created_at->format('d.m.Y H:i') }}</p>
                        @if($order->completed_at)
                            <p><strong>Дата завершения:</strong> {{ $order->completed_at->format('d.m.Y H:i') }}</p>
                        @endif
                        <p><strong>Общая сумма:</strong> <span class="h5 text-primary">{{ number_format($order->getTotalAmount(), 2) }} ₽</span></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Товары в заказе -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Товары в заказе</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Товар</th>
                                <th>Цена</th>
                                <th>Количество</th>
                                <th>Сумма</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                            <tr>
                                <td>{{ $item->product->name }}</td>
                                <td>{{ number_format($item->product->price, 2) }} ₽</td>
                                <td>{{ $item->count }}</td>
                                <td>{{ number_format($item->product->price * $item->count, 2) }} ₽</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end">Итого:</th>
                                <th>{{ number_format($order->getTotalAmount(), 2) }} ₽</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Действия с заказом -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Действия</h5>
            </div>
            <div class="card-body">
                @if($order->isActive())
                    <div class="d-grid gap-2">
                        <button class="btn btn-success order-action" data-action="complete" data-order-id="{{ $order->id }}">
                            <i class="fas fa-check"></i> Завершить заказ
                        </button>
                        <button class="btn btn-danger order-action" data-action="cancel" data-order-id="{{ $order->id }}">
                            <i class="fas fa-times"></i> Отменить заказ
                        </button>
                    </div>
                @elseif($order->isCanceled())
                    <div class="d-grid gap-2">
                        <button class="btn btn-info order-action" data-action="resume" data-order-id="{{ $order->id }}">
                            <i class="fas fa-redo"></i> Возобновить заказ
                        </button>
                    </div>
                @else
                    <p class="text-muted">Заказ завершен</p>
                @endif
            </div>
        </div>

        <!-- История статусов -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">История</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <small class="text-muted">{{ $order->created_at->format('d.m.Y H:i') }}</small><br>
                        <strong>Заказ создан</strong>
                    </li>
                    @if($order->completed_at)
                        <li class="mb-2">
                            <small class="text-muted">{{ $order->completed_at->format('d.m.Y H:i') }}</small><br>
                            <strong>Заказ завершен</strong>
                        </li>
                    @endif
                    @if($order->isCanceled())
                        <li class="mb-2">
                            <small class="text-muted">{{ $order->updated_at->format('d.m.Y H:i') }}</small><br>
                            <strong>Заказ отменен</strong>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('.order-action').click(function() {
        const action = $(this).data('action');
        const orderId = $(this).data('order-id');
        const button = $(this);
        
        let confirmMessage = '';
        switch(action) {
            case 'complete':
                confirmMessage = 'Вы уверены, что хотите завершить заказ?';
                break;
            case 'cancel':
                confirmMessage = 'Вы уверены, что хотите отменить заказ? Товары будут возвращены на склад.';
                break;
            case 'resume':
                confirmMessage = 'Вы уверены, что хотите возобновить заказ? Товары будут снова списаны со склада.';
                break;
        }
        
        if (!confirm(confirmMessage)) {
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
                alert('Действие выполнено успешно!');
                location.reload();
            } else {
                alert('Ошибка: ' + data.message);
            }
        })
        .catch(error => {
            alert('Ошибка: ' + error.message);
        })
        .finally(() => {
            button.prop('disabled', false);
        });
    });
});
</script>
@endpush 