<?php

namespace App\Http\Controllers;
use App\Models\Callorage;
use App\Models\DiaryFood;
use App\Models\Weight;
use GuzzleHttp\Psr7\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;


class CallorageController extends Controller
{
    // Добавление продуктов в дневник
    public function update(array $foodData) : RedirectResponse{
        $currentCallorage = Callorage::forUser(Auth::id())->date($foodData['date'])->first();

        if(! $currentCallorage){
            Callorage::create([
                'user_id' => Auth::id(),
                'to_do_callorage' => Weight::forUser(Auth::id())->current()->callorage,
                'date' => $foodData['date'],
            ]);
        }

        if ($currentCallorage->to_do_callorage - $foodData['calories'] < 0){
            $currentCallorage->update([
                'to_do_callorage' => 0,
            ]);
        }
        else{
            $currentCallorage->update([
                'to_do_callorage' => $currentCallorage->to_do_callorage - $foodData['calories'],
            ]);
        }

        $currentCallorage->update([
            'now_callorage' => $currentCallorage->now_callorage + $foodData['calories'],
            'proteins' => $currentCallorage->proteins + $foodData['protein'],
            'fats' => $currentCallorage->fats + $foodData['fat'],
            'carbohydrates' => $currentCallorage->carbohydrates + $foodData['carbohydrates'],
        ]);

        $Diary = [
            'callorages_id' => $currentCallorage->id,
            'eat_type' => $foodData['eat_type'],
            'name' => $foodData['name'],
            'calories' => $foodData['calories'],
            'proteins' => $foodData['proteins'],
            'fats' => $foodData['fats'],
            'carbohydrates' => $foodData['carbohydrates'],
        ];

        $diary = new DiaryFood;
        $diary->update($Diary);


        return redirect()->intended(route('diary', absolute: false))->with('success', 'Запись сохранена');
    }

    // Удаление продукта из дневника
    public function delete(Request $request) : RedirectResponse {
        $currentCallorage = Callorage::forUser(Auth::id())->date($request->date)->first();

        $diaryfood = DiaryFood::forDiary($currentCallorage->callorages_id, $request->eat_type)->first();

        $food = $diaryfood->food;
        $food = $food[$request->name];

        if ($currentCallorage->to_do_callorage + $food['calories'] < 0){
            $currentCallorage->update([
                'to_do_callorage' => 0,
            ]);
        }
        else{
            $currentCallorage->update([
                'to_do_callorage' => $currentCallorage->to_do_callorage + $food['calories'],
            ]);
        }

        $currentCallorage->update([
            'now_callorage' => $currentCallorage->now_callorage - $food['calories'],
            'proteins' => $currentCallorage->proteins - $food['proteins'],
            'fats' => $currentCallorage->fats - $food['fats'],
            'carbohydrates' => $currentCallorage->carbohydrates - $food['carbohydrates'],
        ]);
        unset($food[$request->name]);

        $diaryfood->update([
            'food' => $food,
        ]);

        return redirect()->intended(route('diary', absolute: false))->with('success', 'Запись удалена');
    }

}
