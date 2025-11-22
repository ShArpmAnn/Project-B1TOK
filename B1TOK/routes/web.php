<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

# Адреса доступные для гостя
Route::middleware('guest')->group(function () {
    Route::get('/register', function (){
        return view('auth/register');
    })->name('register-home');

    Route::post('/register', 'App\Http\Controllers\Auth\RegisteredUserController@register'
    )->name('register');


    Route::get('/login', function (){
        return view('auth/login');
    })->name('login-home');

    Route::post('/login', 'App\Http\Controllers\Auth\AuthenticatedSessionController@login'
    )->name('login');
});

# Адреса доступные авторизованным
Route::middleware('auth')->group(function () {
    Route::post('/logout', 'App\Http\Controllers\Auth\AuthenticatedSessionController@logout'
    )->name('logout');


});

