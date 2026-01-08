<?php

namespace App\Http\Controllers;

use App\Models\DiaryFood;
use Illuminate\Http\RedirectResponse;


class DiaryFoodController extends Controller
{
    // Сохранение продуктов из дневника по типу приёма пищи
    public function update($Diary) : RedirectResponse {
        $diary = DiaryFood::forDiary($Diary['callorages_id'], $Diary['eat_type'])->first();

        if (!$diary) {

            $food[$Diary['name']] = [
                'calories' => $Diary['calories'],
                'proteins' => $Diary['proteins'],
                'fats' => $Diary['fats'],
                'carbohydrates' => $Diary['carbohydrates'],
            ];

            DiaryFood::create([
                'callorages_id' => $Diary['callorages_id'],
                'food' => $food,
                'eat_type' => $Diary['eat_type'],
            ]);
        }
        else {
            $food = $diary->food;

            if(isset($food[$Diary['name']])) {
                $food[$Diary['name']] = [
                    'calories' => $food[$Diary['name']]['calories'] + $Diary['calories'],
                    'proteins' => $food[$Diary['name']]['proteins'] + $Diary['proteins'],
                    'fats' => $food[$Diary['name']]['fats'] + $Diary['fats'],
                    'carbohydrates' => $food[$Diary['name']]['carbohydrates'] + $Diary['carbohydrates'],
                ];
            }
            else{
                $food[$Diary['name']] = [
                    'calories' => $Diary['calories'],
                    'proteins' => $Diary['proteins'],
                    'fats' => $Diary['fats'],
                    'carbohydrates' => $Diary['carbohydrates'],
                ];
            }

            $diary->update([
                'food' => $food,
            ]);
        }

        return redirect()->intended(route('diary', absolute: false));
    }
}
