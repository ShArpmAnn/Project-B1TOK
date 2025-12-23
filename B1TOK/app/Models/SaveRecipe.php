<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaveRecipe extends Model
{
    protected $fillable = [
        'user_id',
        'calories',
        'proteins',
        'fats',
        'carbohydrates',
        'food',
        'title',
    ];

    protected $casts = [
        'food' => 'array', // Автоматическая конвертация JSON в массив PHP
        'title' => 'string',
        'calories' => 'unsingedInteger',
        'proteins' => 'unsingedInteger',
        'fats' => 'unsingedInteger',
        'carbohydrates' => 'unsingedInteger',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scope для записей конкретного пользователя
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
