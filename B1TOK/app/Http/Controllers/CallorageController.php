<?php

namespace App\Http\Controllers;
use App\Models\Callorage;
use App\Models\Weight;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class CallorageController extends Controller
{
    // Добавление продуктов в дневник
    public function update(Request $request) : RedirectResponse{
        $currentCallorage = Callorage::forUser(Auth::id())->date($request->date)->first();

        if(! $currentCallorage){
            Callorage::create([
                'user_id' => Auth::id(),
                'to_do_callorage' => Weight::forUser(Auth::id())->current()->callorage,
                'date' => $request->date,
            ]);
        }

        Callorage::forUser(Auth::id())->date($request->date)->first()->update([
           // Доделать после подключения базы продуктов
        ]);

        return redirect()->intended(route('diary', absolute: false));
    }

}
