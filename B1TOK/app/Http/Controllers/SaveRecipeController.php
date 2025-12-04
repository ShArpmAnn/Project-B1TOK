<?php

namespace App\Http\Controllers;

use App\Models\SaveRecipe;
use Illuminate\Http\Request;
use App\Models\Callorage;
use App\Models\Weight;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class SaveRecipeController extends Controller
{
    public function create(Request $request) : RedirectResponse
    {
        $food = explode(" ", $request->food);

        SaveRecipe::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'callorage' => $request->callorage,
            'proteins' => $request->proteins,
            'fats' => $request->fats,
            'carbohydrates' => $request->carbohydrates,
            'food' => $food,
        ]);

        return redirect()->route('save_recipes');
    }

    public function update(Request $request) : RedirectResponse {
        $food = explode(" ", $request->food);

        SaveRecipe::forUser(Auth::id())->where('id', $request->id)->first()->update([
            'title' => $request->title,
            'callorage' => $request->callorage,
            'proteins' => $request->proteins,
            'fats' => $request->fats,
            'carbohydrates' => $request->carbohydrates,
            'food' => $food,
        ]);

        return redirect()->route('save_recipes');
    }

}

