<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\Creation\CreateController;
use App\Http\Controllers\Creation\SongController;
use App\Http\Controllers\Creation\BedMusicController;
use App\Http\Controllers\Creation\UploadController;
use App\Http\Controllers\Studio\ClipController;
use App\Http\Controllers\Studio\CoverController;
use App\Http\Controllers\Studio\ReelController;
use App\Http\Controllers\Payments\CheckoutController;

Route::get("/", function () {
    return view("welcome");
});

Route::get("/lang/{code}", [LanguageController::class, "switch"])->name("lang.switch");

Route::get("/login", function () {
    return view("auth.login");
})->name("login");

Route::middleware(["auth"])->group(function () {
    Route::get("/create", [CreateController::class, "index"])->name("create");
    Route::post("/create/song", [SongController::class, "store"])->name("create.song");
    Route::post("/create/bed", [BedMusicController::class, "store"])->name("create.bed");
    Route::post("/create/upload", [UploadController::class, "store"])->name("create.upload");

    Route::get("/studio/{clip}", [ClipController::class, "show"])->name("studio.show");
    Route::patch("/studio/{clip}/visibility", [ClipController::class, "updateVisibility"])->name("studio.visibility");
    Route::post("/studio/{clip}/cover", [CoverController::class, "store"])->name("studio.cover");
    Route::post("/studio/{clip}/reel", [ReelController::class, "store"])->name("studio.reel");

    Route::get("/credits", [CheckoutController::class, "index"])->name("credits");
    Route::post("/credits/checkout", [CheckoutController::class, "createIntent"])->name("credits.checkout");
});
