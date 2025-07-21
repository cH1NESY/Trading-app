<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    use HasFactory;
    /**
     * @var string[] Разрешённые для массового заполнения поля
     */
    protected $fillable = [
        'product_id',
        'warehouse_id',
        'quantity',
        'type',
        'description',
    ];

    /**
     * Связь с продуктом
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Связь со складом
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
} 