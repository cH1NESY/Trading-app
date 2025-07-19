<?php

namespace App\Http\Requests;

class OrderCreateRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
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
     * Get custom messages for validator errors.
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