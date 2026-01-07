<?php

use App\Http\Controllers\CallorageController;
use App\Http\Controllers\FatSecretAPIController;
use App\Http\Controllers\SaveRecipeController;
use App\Http\Controllers\WeightController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index')->middleware(['web']);

// Адреса доступные для гостя
Route::middleware('guest')->group(function () {
    Route::get('/register', function (){
        return view('auth/register');
    })->name('register-home');

    Route::post('/register', [RegisteredUserController::class, 'register']
    )->name('register');


    Route::get('/login', function (){
        return view('auth/login');
    })->name('login-home');

    Route::post('/login', [AuthenticatedSessionController::class, 'login']
    )->name('login');
});

// Адреса доступные авторизованным
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'logout']
    )->name('logout');

    Route::get('/personal_cabinet', function (){
        return view('personal_cabinet');
    })->name('personal_cabinet');

    // Работа с целями
    Route::get('/weight/create', function (){
        return view('weight/create_weight');
    })->name('create_weight');

    Route::post('/weight/create', [WeightController::class, 'store']
    )->name('create_weight');


    Route::get('/weight/update', function (){
        return view('weight/update_weight');
    })->name('update_weight');

    Route::post('/weight/update', [WeightController::class, 'update_all']
    )->name('update_weight');


    Route::get('/weight/update_now', function (){
        return view('weight/update_now_weight');
    })->name('update_now_weight');

    Route::post('/weight/update_now', [WeightController::class, 'update_now_weight']
    )->name('update_now_weight');

//  Работа с дневником

    Route::get('/diary', function (){
        return view('diary/diary');
    })->name('diary');


    Route::get('/diary/new', function (){
        return view('diary/new');
    })->name('diary_new');

    Route::post('diary/new', [FatSecretAPIController::class, 'AddToDiary']
    )->name('diary_new');


    Route::post('diary/delete', [CallorageController::class, 'delete']
    )->name('diary_delete');


    // Работа с рецептами
    Route::get('/recipes', function (){
        return view('recipes/recipes');
    })->name('save_recipes');

    Route::get('/recipes/new', function (){
        return view('recipes/new');
    })->name('new_recipes');

    Route::post('recipes/new', [SaveRecipeController::class, 'create']
    )->name('new_recipes');

    Route::get('/recipes/update', function (){
        return view('recipes/update');
    })->name('update_recipes');

    Route::post('/recipes/update', [SaveRecipeController::class, 'update']
    )->name('update_recipes');

    Route::get('/recipes/delete', function (){
        return view('recipes/delete');
    })->name('delete_recipes');

    Route::post('/recipes/delete', [SaveRecipeController::class, 'delete']
    )->name('delete_recipes');


});

