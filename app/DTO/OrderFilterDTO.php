<?php

namespace App\DTO;

class OrderFilterDTO
{
    public ?string $status;
    public ?int $warehouse_id;
    public ?string $customer;

    public function __construct(array $data)
    {
        $this->status = $data['status'] ?? null;
        $this->warehouse_id = isset($data['warehouse_id']) ? (int)$data['warehouse_id'] : null;
        $this->customer = $data['customer'] ?? null;
    }
} 