<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    /**
     * @var string[] Разрешённые для массового заполнения поля
     */
    protected $fillable = ['name'];

    /**
     * Связь с заказами
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Связь с остатками товаров
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }
}
