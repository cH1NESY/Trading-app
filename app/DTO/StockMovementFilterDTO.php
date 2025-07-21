<?php

namespace App\DTO;

class StockMovementFilterDTO
{

    public ?int $product_id;
    public ?int $warehouse_id;
    public ?string $date_from;
    public ?string $date_to;

    public function __construct(array $data)
    {
        $this->product_id = isset($data['product_id']) ? (int)$data['product_id'] : null;
        $this->warehouse_id = isset($data['warehouse_id']) ? (int)$data['warehouse_id'] : null;
        $this->date_from = $data['date_from'] ?? null;
        $this->date_to = $data['date_to'] ?? null;
    }
}
