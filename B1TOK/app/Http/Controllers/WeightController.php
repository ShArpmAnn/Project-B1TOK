<?php

namespace App\Http\Controllers;

use App\Models\Weight;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WeightController extends Controller
{
    // Создание новой цели
    public function store(Request $request): RedirectResponse{

        switch ($request->gender){
            case 'male':
                $BMR = (10 * $request->start_weight) + (6.25 * $request->hight) - (5 * $request->old) + 5;
                break;
            case 'female':
                $BMR = (10 * $request->start_weight) + (6.25 * $request->hight) - (5 * $request->old) - 161;
                break;
        }

        switch ($request->activity){
            case 'min':
                $BMR *= 1.2;
                break;
            case 'light':
                $BMR *= 1.375;
                break;
            case 'medium':
                $BMR *= 1.55;
                break;
            case 'big':
                $BMR *= 1.725;
                break;
            case 'very_big':
                $BMR *= 1.9;
                break;
        }

        switch ($request->choose){
            case 'drop':
                switch ($request->temp){
                    case 'slow':
                        $BMR -= 300;
                        break;
                    case 'fast':
                        $BMR -= 500;
                        break;
                }
                break;
            case 'increase':
                switch ($request->temp){
                    case 'slow':
                        $BMR += 300;
                        break;
                    case 'fast':
                        $BMR += 500;
                        break;
                }
        }

        $currentWeight = Weight::forUser(Auth::id())->current()->first();

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
            'callorage' => $BMR,
        ]);

        return redirect()->intended(route('personal-cabinet', absolute: false));
    }

    // Обновление всех значений цели
    public function update_all(Request $request): RedirectResponse{
        switch ($request->gender){
            case 'male':
                $BMR = (10 * $request->start_weight) + (6.25 * $request->hight) - (5 * $request->old) + 5;
                break;
            case 'female':
                $BMR = (10 * $request->start_weight) + (6.25 * $request->hight) - (5 * $request->old) - 161;
                break;
        }

        switch ($request->activity){
            case 'min':
                $BMR *= 1.2;
                break;
            case 'light':
                $BMR *= 1.375;
                break;
            case 'medium':
                $BMR *= 1.55;
                break;
            case 'big':
                $BMR *= 1.725;
                break;
            case 'very_big':
                $BMR *= 1.9;
                break;
        }

        switch ($request->choose){
            case 'drop':
                switch ($request->temp){
                    case 'slow':
                        $BMR -= 300;
                        break;
                    case 'fast':
                        $BMR -= 500;
                        break;
                }
                break;
            case 'increase':
                switch ($request->temp){
                    case 'slow':
                        $BMR += 300;
                        break;
                    case 'fast':
                        $BMR += 500;
                        break;
                }
        }

        Weight::forUser(Auth::id())->current()->first()->update([
            'start_weight' => $request->start_weight,
            'end_weight' => $request->end_weight,
            'now_weight' => $request->now_weight,
            'to_do_weight' => $request->to_do_weight,
            'callorage' => $BMR,
        ]);

        return redirect()->intended(route('personal-cabinet', absolute: false));
    }

    // Обновление нынешнего веса
    public function update_now_weight(Request $request): RedirectResponse{
        Weight::forUser(Auth::id())->current()->first()->update([
            'now_weight' => $request->now_weight,
        ]);

        return redirect()->intended(route('personal-cabinet', absolute: false));
    }
}
