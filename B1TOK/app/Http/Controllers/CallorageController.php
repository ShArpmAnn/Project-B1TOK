<?php

namespace App\Http\Controllers;
use App\Models\Callorage;
use App\Models\Weight;
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
            Callorage::forUser(Auth::id())->date($foodData['date'])->first()->update([
                'to_do_callorage' => 0,
            ]);
        }
        else{
            Callorage::forUser(Auth::id())->date($foodData['date'])->first()->update([
                'to_do_callorage' => $currentCallorage->to_do_callorage - $foodData['calories'],
            ]);
        }

        Callorage::forUser(Auth::id())->date($foodData['date'])->first()->update([
            'now_callorage' => $currentCallorage->now_callorage + $foodData['calories'],
            'proteins' => $currentCallorage->proteins + $foodData['protein'],
            'fats' => $currentCallorage->fats + $foodData['fat'],
            'carbohydrates' => $currentCallorage->carbohydrates + $foodData['carbohydrates'],
        ]);


        return redirect()->intended(route('diary', absolute: false));
    }

}
