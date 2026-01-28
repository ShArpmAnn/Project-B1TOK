<?php

namespace App\Http\Controllers;
use App\Models\Callorage;
use App\Models\DiaryFood;
use App\Models\Weight;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class CallorageController extends Controller
{
    // Добавление продуктов в дневник
    public function update(array $foodData) : RedirectResponse{
        $currentCallorage = Callorage::forUser(Auth::id())->date($foodData['date'])->first();

        if(! $currentCallorage){
            Callorage::create([
                'user_id' => Auth::id(),
                'to_do_callorage' => Weight::forUser(Auth::id())->current()->first()->callorage,
                'date' => $foodData['date'],
            ]);
        }
        $currentCallorage = Callorage::forUser(Auth::id())->date($foodData['date'])->first();

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
            'proteins' => $currentCallorage->proteins + $foodData['proteins'],
            'fats' => $currentCallorage->fats + $foodData['fats'],
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

        $diaryController = new DiaryFoodController();
        $diaryController->update($Diary);


        return redirect()->intended(route('diary', absolute: false))
            ->with('success', 'Запись сохранена');
    }

    // Удаление продукта из дневника
    public function delete(Request $request) : RedirectResponse {
        Log::info('Delete request data:', $request->all());
        $currentCallorage = Callorage::forUser(Auth::id())->date($request->date)->first();
        Log::info('Current callorage:', $currentCallorage ? $currentCallorage->toArray() : ['message' => 'not found']);

        $diaryfood = DiaryFood::forDiary($currentCallorage->id, $request->eat_type)->first();

        $food = $diaryfood->food;
        $food_now = $food[$request->name];

        if ($currentCallorage->to_do_callorage + $food_now['calories'] < 0){
            $currentCallorage->update([
                'to_do_callorage' => 0,
            ]);
        }
        else{
            $currentCallorage->update([
                'to_do_callorage' => $currentCallorage->to_do_callorage + $food_now['calories'],
            ]);
        }

        $currentCallorage->update([
            'now_callorage' => $currentCallorage->now_callorage - $food_now['calories'],
            'proteins' => $currentCallorage->proteins - $food_now['proteins'],
            'fats' => $currentCallorage->fats - $food_now['fats'],
            'carbohydrates' => $currentCallorage->carbohydrates - $food_now['carbohydrates'],
        ]);
        unset($food[$request->name]);

        if (count($food) > 0) {
            $diaryfood->update([
                'food' => $food,
            ]);
        } else {
            $diaryfood->delete();
        }

        return redirect()->intended(route('diary', absolute: false))
            ->with('success', 'Запись удалена');
    }
}
