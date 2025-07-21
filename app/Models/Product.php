<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /**
     * @var string[] Разрешённые для массового заполнения поля
     */
    protected $fillable = ['name', 'price'];

    /**
     * Связь с остатками по складам
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    /**
     * Связь с позициями в заказах
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
