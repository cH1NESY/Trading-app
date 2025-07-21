<?php

namespace App\Http\Requests;

/**
 * FormRequest для создания заказа
 */
class OrderCreateRequest extends BaseFormRequest
{
    /**
     * Правила валидации для создания заказа
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'customer' => 'required|string|max:255',
            'warehouse_id' => 'required|exists:warehouses,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.count' => 'required|integer|min:1',
        ];
    }

    /**
     * Кастомные сообщения об ошибках валидации
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'customer.required' => 'Покупатель обязателен',
            'customer.max' => 'Имя покупателя не может быть длиннее 255 символов',
            'warehouse_id.required' => 'Склад обязателен',
            'warehouse_id.exists' => 'Выбранный склад не существует',
            'items.required' => 'Список товаров обязателен',
            'items.min' => 'Должен быть выбран хотя бы один товар',
            'items.*.product_id.required' => 'Товар обязателен',
            'items.*.product_id.exists' => 'Выбранный товар не существует',
            'items.*.count.required' => 'Количество товара обязательно',
            'items.*.count.integer' => 'Количество должно быть целым числом',
            'items.*.count.min' => 'Количество должно быть больше 0',
        ];
    }
} 