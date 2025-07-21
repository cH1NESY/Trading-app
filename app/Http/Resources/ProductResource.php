<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Преобразовать ресурс в массив для ответа API
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'price' => $this->price,
            'warehouses' => $this->stocks->map(function ($stock) {
                return [
                    'warehouse_id' => $stock->warehouse->id,
                    'warehouse_name' => $stock->warehouse->name,
                    'stock' => $stock->stock
                ];
            })
        ];
    }
} 