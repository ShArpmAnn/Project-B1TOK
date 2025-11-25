<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Callorage extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'to_do_callorage',
        'now_callorage',
        'proteins',
        'fats',
        'carbohydrates'
    ];

    protected $casts = [
        'to_do_callorage' => 'unsignedInteger',
        'now_callorage' => 'unsignedInteger',
        'proteins' => 'unsignedInteger',
        'fats' => 'unsignedInteger',
        'carbohydrates' => 'unsignedInteger',
        'date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scope для поиска записей по датам
    public function scopeDate($query, $date){
        return $query->whereDate('date', $date);
    }

    // Scope для записей конкретного пользователя
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

}
