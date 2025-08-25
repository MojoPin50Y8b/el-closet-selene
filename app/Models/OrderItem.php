<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;
    protected $fillable = ['order_id', 'product_id', 'variant_id', 'name', 'sku', 'attributes_json', 'qty', 'unit_price', 'discount_total', 'tax_total', 'total'];
    protected $casts = ['attributes_json' => 'array'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }          // nullable
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class);
    }   // nullable
}

