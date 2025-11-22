<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Weight extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'start_weight',
        'end_weight',
        'now_weight',
        'to_do_weight',
        'used_now'
    ];

    protected $casts = [
        'start_weight' => 'decimal:2',
        'end_weight' => 'decimal:2',
        'now_weight' => 'decimal:2',
        'to_do_weight' => 'decimal:2',
        'used_now' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scope для быстрого получения текущей активной записи
    public function scopeCurrent($query)
    {
        return $query->where('used_now', true);
    }

    // Scope для записей конкретного пользователя
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
