<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stock extends Model
{
    protected $fillable = ['product_id', 'warehouse_id', 'stock'];
    
    // Указываем составной первичный ключ
    protected $primaryKey = null;
    public $incrementing = false;
    
    // Указываем поля для составного ключа
    protected function setKeysForSaveQuery($query)
    {
        $query->where('product_id', '=', $this->product_id)
              ->where('warehouse_id', '=', $this->warehouse_id);
        return $query;
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function hasEnoughStock(int $quantity): bool
    {
        return $this->stock >= $quantity;
    }

    public function decreaseStock(int $quantity): bool
    {
        if (!$this->hasEnoughStock($quantity)) {
            return false;
        }

        $this->stock -= $quantity;
        return $this->update(['stock' => $this->stock]);
    }

    public function increaseStock(int $quantity): bool
    {
        $this->stock += $quantity;
        return $this->update(['stock' => $this->stock]);
    }

    public static function findByProductAndWarehouse(int $productId, int $warehouseId): ?self
    {
        return self::where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->first();
    }

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

    public static function safeIncreaseStock(int $productId, int $warehouseId, int $quantity): bool
    {
        $stock = self::findOrCreate($productId, $warehouseId, 0);
        return $stock->increaseStock($quantity);
    }

    public static function safeDecreaseStock(int $productId, int $warehouseId, int $quantity): bool
    {
        $stock = self::findOrCreate($productId, $warehouseId, 0);
        return $stock->decreaseStock($quantity);
    }
}
