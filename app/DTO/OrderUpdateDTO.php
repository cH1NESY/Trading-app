<?php

namespace App\DTO;


class OrderUpdateDTO
{
    public ?string $customer;
    public ?int $warehouse_id;
    public array $items;

    public function __construct(array $data)
    {
        $this->customer = $data['customer'] ?? null;
        $this->warehouse_id = isset($data['warehouse_id']) ? (int)$data['warehouse_id'] : null;
        $this->items = array_map(fn($item) => new OrderItemDTO($item), $data['items'] ?? []);
    }
}
