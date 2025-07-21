<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    /**
     * @var string[] Разрешённые для массового заполнения поля
     */
    protected $fillable = ['customer', 'warehouse_id', 'status'];

    /**
     * @var array Кастинг полей
     */
    protected $casts = [
        'completed_at' => 'datetime',
    ];

    /** Статус заказа: активен */
    const STATUS_ACTIVE = 'active';
    /** Статус заказа: завершён */
    const STATUS_COMPLETED = 'completed';
    /** Статус заказа: отменён */
    const STATUS_CANCELED = 'canceled';

    /**
     * Связь с моделью склада
     * @return BelongsTo
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * Связь с позициями заказа
     * @return HasMany
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Проверка: заказ активен?
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Проверка: заказ отменён?
     */
    public function isCanceled(): bool
    {
        return $this->status === self::STATUS_CANCELED;
    }

    /**
     * Можно ли завершить заказ?
     */
    public function canBeCompleted(): bool
    {
        return $this->isActive();
    }

    /**
     * Можно ли отменить заказ?
     */
    public function canBeCanceled(): bool
    {
        return $this->isActive();
    }

    /**
     * Можно ли возобновить заказ?
     */
    public function canBeResumed(): bool
    {
        return $this->isCanceled();
    }

}
