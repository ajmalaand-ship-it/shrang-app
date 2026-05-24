<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\LanguageHintController;

Route::prefix('admin')->name('admin.')->middleware(['web', 'auth'])->group(function () {

    Route::get('/language-hints', [LanguageHintController::class, 'index'])
        ->name('language-hints.index');

    Route::post('/language-hints', [LanguageHintController::class, 'store'])
        ->name('language-hints.store');

    Route::delete('/language-hints/{languageHint}', [LanguageHintController::class, 'destroy'])
        ->name('language-hints.destroy');

});
