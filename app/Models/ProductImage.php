<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    use HasFactory;
    protected $fillable = ['product_id', 'url', 'is_cover', 'sort_order'];
    protected $casts = ['is_cover' => 'boolean'];
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

