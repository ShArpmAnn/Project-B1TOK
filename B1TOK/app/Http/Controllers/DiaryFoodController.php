<?php

namespace App\Http\Controllers;

use App\Models\DiaryFood;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DiaryFoodController extends Controller
{
    # Сохранение продуктов из дневника по типу приёма пищи
    public function update($callorages_id, $eat_type, $food_name) : RedirectResponse {
        $diary = DiaryFood::forDiary($callorages_id, $eat_type)->first();

        $food[] = $food_name;

        if (!$diary) {
            DiaryFood::create([
                'callorages_id' => $callorages_id,
                'food' => $food,
                'eat_type' => $eat_type,
            ]);
        }
        else {
            $diary->update([
                'food' => $food,
            ]);
        }

        return redirect()->intended(route('diary', absolute: false));
    }


}
