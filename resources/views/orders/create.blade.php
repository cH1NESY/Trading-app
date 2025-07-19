@extends('layouts.app')

@section('title', 'Создать заказ - Trading App')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Создать заказ</h1>
    <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left"></i> Назад к списку
    </a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form id="orderForm" method="POST" action="{{ route('orders.store') }}">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="customer" class="form-label">Покупатель *</label>
                        <input type="text" class="form-control" id="customer" name="customer" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="warehouse_id" class="form-label">Склад *</label>
                        <select class="form-select" id="warehouse_id" name="warehouse_id" required>
                            <option value="">Выберите склад</option>
                            @foreach($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Товары *</label>
                        <div id="items-container">
                            <div class="item-row row mb-2">
                                <div class="col-md-6">
                                    <select class="form-select product-select" name="items[0][product_id]" required>
                                        <option value="">Выберите товар</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}" data-price="{{ $product->price }}">
                                                {{ $product->name }} ({{ number_format($product->price, 2) }} ₽)
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <input type="number" class="form-control quantity-input" name="items[0][count]" placeholder="Количество" min="1" required>
                                </div>
                                <div class="col-md-2">
                                    <span class="item-total">0.00 ₽</span>
                                </div>
                                <div class="col-md-1">
                                    <button type="button" class="btn btn-outline-danger btn-sm remove-item">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-sm" id="add-item">
                            <i class="fas fa-plus"></i> Добавить товар
                        </button>
                    </div>
                    
                    <div class="mb-3">
                        <h5>Итого: <span id="total-amount">0.00 ₽</span></h5>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Создать заказ
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Остатки товаров</h5>
            </div>
            <div class="card-body">
                <div id="stock-info">
                    <p class="text-muted">Выберите склад для просмотра остатков</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let itemIndex = 1;
    
    // Добавление товара
    $('#add-item').click(function() {
        const newItem = `
            <div class="item-row row mb-2">
                <div class="col-md-6">
                    <select class="form-select product-select" name="items[${itemIndex}][product_id]" required>
                        <option value="">Выберите товар</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" data-price="{{ $product->price }}">
                                {{ $product->name }} ({{ number_format($product->price, 2) }} ₽)
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="number" class="form-control quantity-input" name="items[${itemIndex}][count]" placeholder="Количество" min="1" required>
                </div>
                <div class="col-md-2">
                    <span class="item-total">0.00 ₽</span>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-outline-danger btn-sm remove-item">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `;
        $('#items-container').append(newItem);
        itemIndex++;
    });
    
    // Удаление товара
    $(document).on('click', '.remove-item', function() {
        if ($('.item-row').length > 1) {
            $(this).closest('.item-row').remove();
            calculateTotal();
        }
    });
    
    // Расчет стоимости товара
    $(document).on('change', '.product-select, .quantity-input', function() {
        const row = $(this).closest('.item-row');
        const productSelect = row.find('.product-select');
        const quantityInput = row.find('.quantity-input');
        const totalSpan = row.find('.item-total');
        
        if (productSelect.val() && quantityInput.val()) {
            const price = parseFloat(productSelect.find('option:selected').data('price'));
            const quantity = parseInt(quantityInput.val());
            const total = price * quantity;
            totalSpan.text(total.toFixed(2) + ' ₽');
        } else {
            totalSpan.text('0.00 ₽');
        }
        
        calculateTotal();
    });
    
    // Расчет общей суммы
    function calculateTotal() {
        let total = 0;
        $('.item-row').each(function() {
            const productSelect = $(this).find('.product-select');
            const quantityInput = $(this).find('.quantity-input');
            
            if (productSelect.val() && quantityInput.val()) {
                const price = parseFloat(productSelect.find('option:selected').data('price'));
                const quantity = parseInt(quantityInput.val());
                total += price * quantity;
            }
        });
        
        $('#total-amount').text(total.toFixed(2) + ' ₽');
    }
    
    // Обновление остатков при выборе склада
    $('#warehouse_id').change(function() {
        const warehouseId = $(this).val();
        if (warehouseId) {
            updateStockInfo(warehouseId);
        } else {
            $('#stock-info').html('<p class="text-muted">Выберите склад для просмотра остатков</p>');
        }
    });
    
    function updateStockInfo(warehouseId) {
        fetch(`/web/warehouses/${warehouseId}/stocks`, {
            headers: {
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let html = '';
                data.data.forEach(function(stock) {
                    html += `
                        <div class="mb-2">
                            <strong>${stock.product.name}:</strong> ${stock.stock} шт.
                        </div>
                    `;
                });
                $('#stock-info').html(html);
            }
        });
    }
    
    // Отправка формы
    $('#orderForm').submit(function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const data = {
            customer: formData.get('customer'),
            warehouse_id: parseInt(formData.get('warehouse_id')),
            items: []
        };

        // Собираем позиции
        $('.item-row').each(function() {
            const productId = $(this).find('.product-select').val();
            const count = $(this).find('.quantity-input').val();
            
            if (productId && count) {
                data.items.push({
                    product_id: parseInt(productId),
                    count: parseInt(count)
                });
            }
        });

        fetch('{{ route("orders.store") }}', {
            method: 'POST',
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
                throw new Error(result.message || 'Ошибка при создании заказа');
            }
            return result;
        })
        .then(result => {
            if (result.success) {
                alert('Заказ успешно создан!');
                window.location.href = '{{ route("orders.index") }}';
            } else {
                alert('Ошибка: ' + (result.message || 'Неизвестная ошибка'));
            }
        })
        .catch(error => {
            alert('Произошла ошибка: ' + error.message);
        });
    });
});
</script>
@endpush 