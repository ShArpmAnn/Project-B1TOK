<?php

namespace App\Http\Controllers;

use App\Models\SaveRecipe;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class SaveRecipeController extends Controller
{
    // Создание рецепта
    public function create(Request $request) : RedirectResponse
    {
        $food = explode("\r\n", $request->food);

        SaveRecipe::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'callorage' => $request->callorage,
            'proteins' => $request->proteins,
            'fats' => $request->fats,
            'carbohydrates' => $request->carbohydrates,
            'food' => $food,
        ]);

        return redirect(route('save_recipes'))
            ->with('success', 'Рецепт успешно сохранён');
    }

    // Изменениее рецепта
    public function update(Request $request) : RedirectResponse {
        $food = explode("\r\n", $request->food);

        SaveRecipe::forUser(Auth::id())->where('id', $request->id)->first()->update([
            'title' => $request->title,
            'callorage' => $request->callorage,
            'proteins' => $request->proteins,
            'fats' => $request->fats,
            'carbohydrates' => $request->carbohydrates,
            'food' => $food,
        ]);

        return redirect(route('save_recipes'))
            ->with('success', 'Рецепт успешно обновлён');
    }

    // Удаление рецепта
    public function delete(Request $request) : RedirectResponse {
        $recipe = SaveRecipe::forUser(Auth::id())->where('id', $request->id)->first();

        $recipe->delete();
        return redirect(route('save_recipes'))
            ->with('success', 'Рецепт успешно удален');

    }
}

