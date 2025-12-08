<?php

namespace App\Http\Controllers;

use App\Models\DiaryFood;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DiaryFoodController extends Controller
{
    // Сохранение продуктов из дневника по типу приёма пищи
    public function update($Diary) : RedirectResponse {
        $diary = DiaryFood::forDiary($Diary['callorages_id'], $Diary['eat_type'])->first();
        $food = $diary->food;

        $food[$Diary['name']] = [
            'calories' => $Diary['calories'],
            'proteins' => $Diary['proteins'],
            'fats' => $Diary['fats'],
            'carbohydrates' => $Diary['carbohydrates'],
        ];

        if (!$diary) {
            DiaryFood::create([
                'callorages_id' => $Diary['callorages_id'],
                'food' => $food,
                'eat_type' => $Diary['eat_type'],
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
