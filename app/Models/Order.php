<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Order extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'number', 'status', 'subtotal', 'discount_total', 'shipping_total', 'tax_total', 'total', 'currency', 'payment_status', 'shipping_status', 'payment_method', 'shipping_method', 'notes', 'placed_at'];
    protected $casts = ['placed_at' => 'datetime'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
    public function addresses()
    {
        return $this->hasMany(OrderAddress::class);
    }
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
    public function shipments()
    {
        return $this->hasMany(Shipment::class);
    }
    public function statusHistory()
    {
        return $this->hasMany(OrderStatusHistory::class);
    }
}

