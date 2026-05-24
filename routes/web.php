<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\Creation\CreateController;
use App\Http\Controllers\Creation\SongController;
use App\Http\Controllers\Creation\BedMusicController;
use App\Http\Controllers\Creation\UploadController;

Route::get("/", function () {
    return view("welcome");
});

Route::get("/lang/{code}", [LanguageController::class, "switch"])->name("lang.switch");

// Temporary login stub — will be replaced in Phase 7
Route::get("/login", function () {
    return view("auth.login");
})->name("login");

Route::middleware(["auth"])->group(function () {
    Route::get("/create", [CreateController::class, "index"])->name("create");
    Route::post("/create/song", [SongController::class, "store"])->name("create.song");
    Route::post("/create/bed", [BedMusicController::class, "store"])->name("create.bed");
    Route::post("/create/upload", [UploadController::class, "store"])->name("create.upload");
});
