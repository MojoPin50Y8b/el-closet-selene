<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Coupon extends Model
{
    use HasFactory;
    protected $fillable = ['code', 'type', 'value', 'min_cart_total', 'max_uses', 'max_uses_per_user', 'starts_at', 'ends_at', 'status'];
    protected $casts = ['starts_at' => 'datetime', 'ends_at' => 'datetime', 'status' => 'boolean'];
    public function redemptions()
    {
        return $this->hasMany(CouponRedemption::class);
    }
}

