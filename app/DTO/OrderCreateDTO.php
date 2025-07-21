<?php

namespace App\DTO;

class OrderCreateDTO
{
    public string $customer;
    public int $warehouse_id;
    public array $items;

    public function __construct(array $data)
    {
        $this->customer = $data['customer'];
        $this->warehouse_id = (int)$data['warehouse_id'];
        $this->items = array_map(fn($item) => new OrderItemDTO($item), $data['items'] ?? []);
    }
}
