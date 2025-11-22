<?php

namespace App\Http\Controllers;

use App\Models\Weight;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WeightController extends Controller
{
    # Создание новой цели
    public function store(Request $request): RedirectResponse{

        $currentWeight = Weight::where('user_id', Auth::id())->current()->first();

        if($currentWeight){
            $currentWeight->update(['used_now' => False]);
        }

        Weight::create([
            'user_id' => Auth::id(),
            'start_weight' => $request->start_weight,
            'end_weight' => $request->end_weight,
            'now_weight' => $request->now_weight,
            'to_do_weight' => $request->to_do_weight,
            'used_now' => True,
        ]);

        return redirect()->intended(route('personal-cabinet', absolute: false));
    }

    # Обновление всех значений цели
    public function update_all(Request $request): RedirectResponse{
        Weight::where('user_id', Auth::id())->current()->first()->update([
            'start_weight' => $request->start_weight,
            'end_weight' => $request->end_weight,
            'now_weight' => $request->now_weight,
            'to_do_weight' => $request->to_do_weight,
        ]);

        return redirect()->intended(route('personal-cabinet', absolute: false));
    }

    # Обновление нынешнего веса
    public function update_weight_now(Request $request): RedirectResponse{
        Weight::where('user_id', Auth::id())->current()->first()->update([
            'now_weight' => $request->now_weight,
        ]);

        return redirect()->intended(route('personal-cabinet', absolute: false));
    }
}
