<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LanguageController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/lang/{code}', [LanguageController::class, 'switch'])->name('lang.switch');
