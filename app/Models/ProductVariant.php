<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;
    protected $fillable = ['product_id', 'sku', 'barcode', 'price', 'sale_price', 'sale_starts_at', 'sale_ends_at', 'stock', 'low_stock_threshold', 'weight', 'width', 'height', 'length'];
    protected $casts = ['sale_starts_at' => 'datetime', 'sale_ends_at' => 'datetime'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function values()
    {
        return $this->hasMany(VariantValue::class, 'variant_id');
    }
    public function inventory()
    {
        return $this->hasMany(InventoryMovement::class, 'variant_id');
    }
    public function cartItems()
    {
        return $this->hasMany(CartItem::class, 'variant_id');
    }
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'variant_id');
    }
}

