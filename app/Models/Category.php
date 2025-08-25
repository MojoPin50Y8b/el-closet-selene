<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Category extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'slug', 'description', 'image_url', 'parent_id', 'is_active', 'sort_order'];
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }
    // productos asignados vía pivot
    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_categories');
    }
    // productos cuya categoría principal es esta
    public function mainProducts()
    {
        return $this->hasMany(Product::class, 'main_category_id');
    }
}

