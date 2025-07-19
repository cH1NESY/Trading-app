<?php

namespace App\DTO;

class OrderItemDTO
{
    public int $product_id;
    public int $count;

    public function __construct(array $data)
    {
        $this->product_id = (int) $data['product_id'];
        $this->count = (int) $data['count'];
    }
} 