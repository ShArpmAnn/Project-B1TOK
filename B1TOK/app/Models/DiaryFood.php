<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiaryFood extends Model
{
    protected $fillable = [
        'callorages_id',
        'food',
        'eat_type',
    ];

    protected $casts = [
        'food' => 'array', // Автоматическая конвертация JSON в массив PHP
        'eat_type' => 'string',
    ];

    // Scope для конкретного дня и типа приёма пищи
    public function scopeForDiary($query, $calloragesId, $type)
    {
        return $query->where('callorages_id', $calloragesId)->where('eat_type', $type);
    }
}
