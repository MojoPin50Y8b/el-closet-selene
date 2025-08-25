<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WishlistItem extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = ['wishlist_id', 'product_id', 'variant_id', 'created_at'];
    protected $casts = ['created_at' => 'datetime'];
    public function wishlist()
    {
        return $this->belongsTo(Wishlist::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
}

