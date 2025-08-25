<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    protected $fillable = ['order_id', 'provider', 'provider_ref', 'amount', 'currency', 'status', 'received_at'];
    protected $casts = ['received_at' => 'datetime'];
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}

