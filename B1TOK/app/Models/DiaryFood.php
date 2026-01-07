<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiaryFood extends Model
{

    protected $table = 'diary_food';

    protected $fillable = [
        'callorages_id',
        'food',
        'eat_type',
    ];

    protected $casts = [
        'food' => 'array', // Автоматическая конвертация JSON в массив PHP
        'eat_type' => 'string',
    ];

    public function user()
    {
        return $this->belongsTo(Callorage::class);
    }

    // Scope для конкретного дня и типа приёма пищи
    public function scopeForDiary($query, $calloragesId, $type)
    {
        return $query->where('callorages_id', $calloragesId)->where('eat_type', $type);
    }
}
