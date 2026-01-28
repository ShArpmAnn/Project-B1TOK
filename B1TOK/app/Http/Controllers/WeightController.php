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

        $request->validate([
            'gender' => 'required|in:male,female',
            'start_weight' => 'required|numeric|min:20',
            'height' => 'required|numeric|min:50',
            'old' => 'required|numeric|min:5',
            'end_weight' => 'required|numeric|min:1',
            'activity' => 'required|in:min,light,medium,big,very_big',
            'choose' => 'required|in:drop,increase',
            'temp' => 'required|in:slow,fast',
        ]);

        $BMR = $this->find_BMR($request->gender, $request->start_weight, $request->height, $request->old,
            $request->activity, $request->choose, $request->temp);

        $currentWeight = Weight::forUser(Auth::id())->current()->first();

        if($currentWeight){
            $currentWeight->update(['used_now' => False]);
        }

        Weight::create([
            'user_id' => Auth::id(),
            'start_weight' => $request->start_weight,
            'end_weight' => $request->end_weight,
            'now_weight' => $request->start_weight,
            'to_do_weight' => $request->end_weight-$request->start_weight,
            'used_now' => True,
            'callorage' => $BMR,
        ]);

        return redirect()->intended(route('personal_cabinet', absolute: false))
            ->with('success', 'Цель создана');
    }

    // Обновление всех значений цели
    public function update_all(Request $request): RedirectResponse{

        $request->validate([
            'gender' => 'required|in:male,female',
            'start_weight' => 'required|numeric|min:1',
            'now_weight' => 'required|numeric|min:1',
            'height' => 'required|numeric|min:1',
            'old' => 'required|numeric|min:1',
            'end_weight' => 'required|numeric|min:1',
            'activity' => 'required|in:min,light,medium,big,very_big',
            'choose' => 'required|in:drop,increase',
            'temp' => 'required|in:slow,fast',
        ]);

        $BMR = $this->find_BMR($request->gender, $request->start_weight, $request->height, $request->old,
            $request->activity, $request->choose, $request->temp);

        Weight::forUser(Auth::id())->current()->first()->update([
            'start_weight' => $request->start_weight,
            'end_weight' => $request->end_weight,
            'now_weight' => $request->now_weight,
            'to_do_weight' => $request->end_weight-$request->now_weight,
            'callorage' => $BMR,
        ]);

        return redirect()->intended(route('personal_cabinet', absolute: false))
            ->with('success', 'Цель успешно обновлена');
    }

    // Обновление нынешнего веса
    public function update_now_weight(Request $request): RedirectResponse{

        $request->validate([
            'now_weight' => 'required|numeric|min:1',
        ]);

        $Weight = Weight::forUser(Auth::id())->current()->first();

        Weight::forUser(Auth::id())->current()->first()->update([
            'now_weight' => $request->now_weight,
            'to_do_weight' => $Weight->end_weight-$request->now_weight,
        ]);

        return redirect()->intended(route('personal_cabinet', absolute: false))
            ->with('success', 'Вес успешно обновлён');
    }

    private function find_BMR($gender, $start_weight, $height, $old, $activity, $choose, $temp)
    {
        if ($gender == 'male') {
            $BMR = (10 * $start_weight) + (6.25 * $height) - (5 * $old) + 5;
        }
        if ($gender == 'female'){
            $BMR = (10 * $start_weight) + (6.25 * $height) - (5 * $old) - 161;
        }

        switch ($activity){
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

        switch ($choose){
            case 'drop':
                switch ($temp){
                    case 'slow':
                        $BMR -= 300;
                        break;
                    case 'fast':
                        $BMR -= 500;
                        break;
                }
                break;
            case 'increase':
                switch ($temp){
                    case 'slow':
                        $BMR += 300;
                        break;
                    case 'fast':
                        $BMR += 500;
                        break;
                }
        }

        $BMR = round($BMR);

        return $BMR;
    }
}
