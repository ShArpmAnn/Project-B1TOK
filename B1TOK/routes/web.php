<?php

use App\Http\Controllers\CallorageController;
use App\Http\Controllers\FatSecretAPIController;
use App\Http\Controllers\WeightController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

Route::get('/', function () {
    return view('home');
})->name('home');

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

    // Работа с целями
    Route::get('/create_weight', function (){
        return view('weight/create_weight');
    })->name('create_weight');

    Route::post('/create_weight', [WeightController::class, 'store']
    )->name('create_weight');


    Route::get('/update_weight', function (){
        return view('weight/update_weight');
    })->name('update_weight');

    Route::post('/update_weight', [WeightController::class, 'update_all']
    )->name('update_weight');


    Route::get('/update_now_weight', function (){
        return view('weight/update_now_weight');
    })->name('update_now_weight');

    Route::post('/update_now_weight', [WeightController::class, 'update_now_weight']
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


});

