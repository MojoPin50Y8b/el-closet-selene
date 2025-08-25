<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryMovement extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = ['variant_id', 'change_qty', 'reason', 'reference_type', 'reference_id', 'user_id', 'note', 'created_at'];
    protected $casts = ['created_at' => 'datetime'];
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

