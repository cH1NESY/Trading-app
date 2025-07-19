<?php

namespace App\Http\Requests;

class OrderUpdateRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'customer' => 'sometimes|string|max:255',
            'warehouse_id' => 'sometimes|exists:warehouses,id',
            'items' => 'sometimes|array|min:1',
            'items.*.product_id' => 'required_with:items|exists:products,id',
            'items.*.count' => 'required_with:items|integer|min:1',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'customer.string' => 'Имя покупателя должно быть строкой',
            'customer.max' => 'Имя покупателя не может быть длиннее 255 символов',
            'warehouse_id.exists' => 'Выбранный склад не существует',
            'items.array' => 'Список товаров должен быть массивом',
            'items.min' => 'Должен быть выбран хотя бы один товар',
            'items.*.product_id.required_with' => 'Товар обязателен при указании списка товаров',
            'items.*.product_id.exists' => 'Выбранный товар не существует',
            'items.*.count.required_with' => 'Количество товара обязательно при указании списка товаров',
            'items.*.count.integer' => 'Количество должно быть целым числом',
            'items.*.count.min' => 'Количество должно быть больше 0',
        ];
    }
} 