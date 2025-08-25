<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Product extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['name', 'slug', 'description', 'brand_id', 'status', 'main_category_id', 'seo_title', 'seo_description'];
    protected $casts = ['created_at' => 'datetime', 'updated_at' => 'datetime', 'deleted_at' => 'datetime'];
    protected $appends = ['cover_image_url', 'from_price', 'from_sale_price', 'has_sale'];

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
    public function mainCategory()
    {
        return $this->belongsTo(Category::class, 'main_category_id');
    }
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'product_categories');
    }
    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }
    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function getCoverImageUrlAttribute()
    {
        return $this->images()->where('is_cover', 1)->value('url')
            ?? $this->images()->orderBy('sort_order')->value('url')
            ?? '/images/placeholder.png';
    }
    public function getFromPriceAttribute()
    {
        return (float) $this->variants()->min('price') ?: 0;
    }
    public function getFromSalePriceAttribute()
    {
        return (float) $this->variants()->min('sale_price');
    }
    public function getHasSaleAttribute()
    {
        return $this->variants()->whereNotNull('sale_price')->exists();
    }
}

