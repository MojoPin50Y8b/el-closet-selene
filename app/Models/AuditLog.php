<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = ['user_id', 'action', 'entity_type', 'entity_id', 'meta', 'created_at'];
    protected $casts = ['meta' => 'array', 'created_at' => 'datetime'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

