<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes;

    protected $fillable = ['name', 'email', 'phone', 'password', 'is_active'];
    protected $hidden = ['password', 'remember_token'];
    protected $casts = ['email_verified_at' => 'datetime', 'is_active' => 'boolean'];

    // Relaciones
    public function addresses()
    {
        return $this->hasMany(Address::class);
    }
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    public function wishlist()
    {
        return $this->hasOne(Wishlist::class);
    }
    public function inventoryMovements()
    {
        return $this->hasMany(InventoryMovement::class);
    }
    public function orderStatusChanges()
    {
        return $this->hasMany(OrderStatusHistory::class);
    }
    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }
}
