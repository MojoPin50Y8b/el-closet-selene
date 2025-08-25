<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderAddress extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = ['order_id', 'type', 'full_name', 'phone', 'line1', 'line2', 'city', 'state', 'country', 'postal_code'];
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}

