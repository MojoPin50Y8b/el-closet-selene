<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Banner extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'image_url', 'link_url', 'position', 'starts_at', 'ends_at', 'is_active'];
    protected $casts = ['starts_at' => 'datetime', 'ends_at' => 'datetime', 'is_active' => 'boolean'];
}

