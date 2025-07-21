<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stock extends Model
{
    /**
     * @var string[] Разрешённые для массового заполнения поля
     */
    protected $fillable = ['product_id', 'warehouse_id', 'stock'];

    // Указываем составной первичный ключ
    protected $primaryKey = null;
    public $incrementing = false;

    /**
     * Переопределяем метод для поддержки составного ключа
     */
    protected function setKeysForSaveQuery($query)
    {
        $query->where('product_id', '=', $this->product_id)
              ->where('warehouse_id', '=', $this->warehouse_id);
        return $query;
    }

    /**
     * Связь с продуктом
     * @return BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Связь со складом
     * @return BelongsTo
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * Проверить, достаточно ли товара на складе
     * @param int $quantity Требуемое количество
     * @return bool
     */
    public function hasEnoughStock(int $quantity): bool
    {
        return $this->stock >= $quantity;
    }

    /**
     * Списать товар со склада
     * @param int $quantity Количество для списания
     * @return bool
     */
    public function decreaseStock(int $quantity): bool
    {
        if (!$this->hasEnoughStock($quantity)) {
            return false;
        }
        $this->stock -= $quantity;
        return $this->update(['stock' => $this->stock]);
    }

    /**
     * Вернуть товар на склад
     * @param int $quantity Количество для возврата
     * @return bool
     */
    public function increaseStock(int $quantity): bool
    {
        $this->stock += $quantity;
        return $this->update(['stock' => $this->stock]);
    }

    /**
     * Найти остаток по продукту и складу
     * @param int $productId
     * @param int $warehouseId
     * @return self|null
     */
    public static function findByProductAndWarehouse(int $productId, int $warehouseId): ?self
    {
        return self::where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->first();
    }

    /**
     * Найти или создать остаток по продукту и складу
     * @param int $productId
     * @param int $warehouseId
     * @param int $initialStock
     * @return self
     */
    public static function findOrCreate(int $productId, int $warehouseId, int $initialStock = 0): self
    {
        $stock = self::where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->first();

        if (!$stock) {
            $stock = new self();
            $stock->product_id = $productId;
            $stock->warehouse_id = $warehouseId;
            $stock->stock = $initialStock;
            $stock->save();
        }

        return $stock;
    }

    /**
     * Безопасно вернуть товар на склад (создаёт запись при необходимости)
     * @param int $productId
     * @param int $warehouseId
     * @param int $quantity
     * @return bool
     */
    public static function safeIncreaseStock(int $productId, int $warehouseId, int $quantity): bool
    {
        $stock = self::findOrCreate($productId, $warehouseId, 0);
        return $stock->increaseStock($quantity);
    }

    /**
     * Безопасно списать товар со склада (создаёт запись при необходимости)
     * @param int $productId
     * @param int $warehouseId
     * @param int $quantity
     * @return bool
     */
    public static function safeDecreaseStock(int $productId, int $warehouseId, int $quantity): bool
    {
        $stock = self::findOrCreate($productId, $warehouseId, 0);
        return $stock->decreaseStock($quantity);
    }
}
