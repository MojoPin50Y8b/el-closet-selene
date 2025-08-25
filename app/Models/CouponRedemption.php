<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CouponRedemption extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = ['coupon_id', 'user_id', 'order_id', 'amount', 'created_at'];
    protected $casts = ['created_at' => 'datetime'];
    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}

