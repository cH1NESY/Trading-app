@extends('layouts.app')

@section('title', 'Редактировать заказ')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Редактировать заказ #{{ $order->id }}</h4>
                    <a href="{{ route('orders.index') }}" class="btn btn-secondary">← Назад к списку</a>
                </div>
                <div class="card-body">
                    <form id="editOrderForm">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="customer" class="form-label">Покупатель *</label>
                                    <input type="text" class="form-control" id="customer" name="customer" 
                                           value="{{ $order->customer }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="warehouse_id" class="form-label">Склад *</label>
                                    <select class="form-select" id="warehouse_id" name="warehouse_id" required>
                                        @foreach($warehouses as $warehouse)
                                            <option value="{{ $warehouse->id }}" 
                                                    {{ $order->warehouse_id == $warehouse->id ? 'selected' : '' }}>
                                                {{ $warehouse->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Позиции заказа *</label>
                            <div id="orderItems">
                                @foreach($order->items as $index => $item)
                                <div class="row mb-2 order-item">
                                    <div class="col-md-4">
                                        <select class="form-select product-select" name="items[{{ $index }}][product_id]" required>
                                            <option value="">Выберите товар</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}" 
                                                        {{ $item->product_id == $product->id ? 'selected' : '' }}
                                                        data-stocks='@json($product->stocks)'>
                                                    {{ $product->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="number" class="form-control quantity-input" 
                                               name="items[{{ $index }}][count]" 
                                               value="{{ $item->count }}" min="1" required 
                                               placeholder="Количество">
                                    </div>
                                    <div class="col-md-3">
                                        <span class="stock-info text-muted"></span>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-danger btn-sm remove-item">Удалить</button>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <button type="button" class="btn btn-success btn-sm" id="addItem">+ Добавить товар</button>
                        </div>

                        <div class="mb-3">
                            <strong>Статус заказа:</strong> 
                            <span class="badge bg-{{ $order->status_color }}">{{ $order->status_text }}</span>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary">Сохранить изменения</button>
                            <a href="{{ route('orders.show', $order) }}" class="btn btn-info">Просмотр заказа</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('editOrderForm');
    const itemsContainer = document.getElementById('orderItems');
    const addItemBtn = document.getElementById('addItem');
    const warehouseSelect = document.getElementById('warehouse_id');
    let itemIndex = {{ count($order->items) }};

    // Добавление новой позиции
    addItemBtn.addEventListener('click', function() {
        const itemHtml = `
            <div class="row mb-2 order-item">
                <div class="col-md-4">
                    <select class="form-select product-select" name="items[${itemIndex}][product_id]" required>
                        <option value="">Выберите товар</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" data-stocks='@json($product->stocks)'>
                                {{ $product->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="number" class="form-control quantity-input" 
                           name="items[${itemIndex}][count]" min="1" required placeholder="Количество">
                </div>
                <div class="col-md-3">
                    <span class="stock-info text-muted"></span>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger btn-sm remove-item">Удалить</button>
                </div>
            </div>
        `;
        itemsContainer.insertAdjacentHTML('beforeend', itemHtml);
        itemIndex++;
        updateRemoveButtons();
    });

    // Удаление позиции
    function updateRemoveButtons() {
        document.querySelectorAll('.remove-item').forEach(btn => {
            btn.addEventListener('click', function() {
                if (document.querySelectorAll('.order-item').length > 1) {
                    this.closest('.order-item').remove();
                    reindexItems();
                }
            });
        });
    }

    // Переиндексация позиций
    function reindexItems() {
        document.querySelectorAll('.order-item').forEach((item, index) => {
            const productSelect = item.querySelector('.product-select');
            const quantityInput = item.querySelector('.quantity-input');
            
            productSelect.name = `items[${index}][product_id]`;
            quantityInput.name = `items[${index}][count]`;
        });
        itemIndex = document.querySelectorAll('.order-item').length;
    }

    // Обновление информации о наличии
    function updateStockInfo() {
        const warehouseId = warehouseSelect.value;
        const products = @json($products);
        
        document.querySelectorAll('.product-select').forEach(select => {
            const item = select.closest('.order-item');
            const stockInfo = item.querySelector('.stock-info');
            const quantityInput = item.querySelector('.quantity-input');
            
            if (select.value && warehouseId) {
                const product = products.find(p => p.id == select.value);
                if (product) {
                    const stock = product.stocks.find(s => s.warehouse_id == warehouseId);
                    const available = stock ? stock.stock : 0;
                    stockInfo.textContent = `Доступно: ${available}`;
                    
                    if (quantityInput.value && parseInt(quantityInput.value) > available) {
                        stockInfo.className = 'stock-info text-danger';
                    } else {
                        stockInfo.className = 'stock-info text-muted';
                    }
                }
            } else {
                stockInfo.textContent = '';
            }
        });
    }

    // Обработчики событий
    warehouseSelect.addEventListener('change', updateStockInfo);
    itemsContainer.addEventListener('change', function(e) {
        if (e.target.classList.contains('product-select')) {
            updateStockInfo();
        }
    });
    itemsContainer.addEventListener('input', function(e) {
        if (e.target.classList.contains('quantity-input')) {
            updateStockInfo();
        }
    });

    // Отправка формы
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(form);
        const data = {
            customer: formData.get('customer'),
            warehouse_id: parseInt(formData.get('warehouse_id')),
            items: []
        };

        // Собираем позиции
        document.querySelectorAll('.order-item').forEach(item => {
            const productId = item.querySelector('.product-select').value;
            const count = item.querySelector('.quantity-input').value;
            
            if (productId && count) {
                data.items.push({
                    product_id: parseInt(productId),
                    count: parseInt(count)
                });
            }
        });

        fetch('{{ route("orders.update", $order) }}', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        })
        .then(async response => {
            let result;
            try {
                result = await response.json();
            } catch (e) {
                throw new Error('Некорректный ответ сервера');
            }
            if (!response.ok) {
                throw new Error(result.message || 'Ошибка при обновлении заказа');
            }
            return result;
        })
        .then(result => {
            if (result.success) {
                alert('Заказ успешно обновлен!');
                window.location.href = '{{ route("orders.show", $order) }}';
            } else {
                alert('Ошибка: ' + (result.message || 'Неизвестная ошибка'));
            }
        })
        .catch(error => {
            alert('Произошла ошибка: ' + error.message);
        });
    });

    // Инициализация
    updateRemoveButtons();
    updateStockInfo();
});
</script>
@endsection 