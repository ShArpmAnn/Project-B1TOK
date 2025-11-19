<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/register', function (){
    return view('auth/register');
}
)->name('register-home');

Route::post('/register', 'App\Http\Controllers\RegisteredUserController@register'
)->name('register');
