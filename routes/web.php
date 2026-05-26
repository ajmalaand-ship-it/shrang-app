<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Creation\CreateController;
use App\Http\Controllers\Creation\SongController;
use App\Http\Controllers\Creation\BedMusicController;
use App\Http\Controllers\Creation\UploadController;
use App\Http\Controllers\Studio\ClipController;
use App\Http\Controllers\Studio\CoverController;
use App\Http\Controllers\Studio\ReelController;
use App\Http\Controllers\Payments\CheckoutController;
use App\Http\Controllers\DashboardController;

// Public
Route::get("/", [App\Http\Controllers\HomeController::class, "index"]);
Route::get("/lang/{code}", [LanguageController::class, "switch"])->name("lang.switch");
Route::get("/about", fn() => view("pages.static.about"))->name("about");
Route::get("/how-it-works", fn() => view("pages.static.how-it-works"))->name("how-it-works");
Route::get("/pricing", fn() => view("pages.static.pricing"))->name("pricing");
Route::get("/faq", fn() => view("pages.static.faq"))->name("faq");
Route::get("/terms", fn() => view("pages.static.terms"))->name("terms");
Route::get("/privacy", fn() => view("pages.static.privacy"))->name("privacy");
Route::get("/support", fn() => view("pages.static.support"))->name("support");
Route::post("/support", [App\Http\Controllers\SupportController::class, "store"])->name("support.store");
Route::middleware(["auth"])->group(function () {
    Route::get("/studio/job-status/{job}", [App\Http\Controllers\Api\JobStatusController::class, "show"])->name("studio.job.status");
    Route::get("/studio/clip-status/{clip}", [App\Http\Controllers\Api\ClipStatusController::class, "show"])->name("studio.clip.status");
});
Route::get("/discover", [App\Http\Controllers\DiscoverController::class, "index"])->name("discover");
Route::post("/discover/{slug}/play", [App\Http\Controllers\DiscoverController::class, "trackPlay"])->name("discover.play");
Route::post("/discover/{slug}/like", [App\Http\Controllers\DiscoverController::class, "like"])->name("discover.like");
Route::post("/discover/{slug}/unlike", [App\Http\Controllers\DiscoverController::class, "unlike"])->name("discover.unlike");
Route::post("/discover/{slug}/download", [App\Http\Controllers\DiscoverController::class, "download"])->name("discover.download");
Route::get("/player/{slug}", [PlayerController::class, "show"])->name("player.show");

// Auth — guests only
Route::middleware("guest")->group(function () {
    Route::get("/login", [LoginController::class, "show"])->name("login");
    Route::post("/login", [LoginController::class, "store"])->name("login.store");
    Route::get("/register", [RegisterController::class, "show"])->name("register");
    Route::post("/register", [RegisterController::class, "store"])->name("register.store");
    Route::get("/forgot-password", [ForgotPasswordController::class, "show"])->name("password.request");
    Route::post("/forgot-password", [ForgotPasswordController::class, "store"])->name("password.email");
    Route::get("/reset-password/{token}", [ResetPasswordController::class, "show"])->name("password.reset");
    Route::post("/reset-password", [ResetPasswordController::class, "store"])->name("password.update");
    Route::get("/auth/google", [GoogleController::class, "redirect"])->name("auth.google");
    Route::get("/auth/google/callback", [GoogleController::class, "callback"])->name("auth.google.callback");
});

// Logout
Route::post("/logout", [LogoutController::class, "store"])->name("logout")->middleware("auth");

// Authenticated
Route::middleware(["auth"])->group(function () {
    Route::get("/dashboard", [DashboardController::class, "index"])->name("dashboard");
    Route::get("/dashboard", [DashboardController::class, "index"])->name("dashboard");
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
    Route::post("/credits/buy", [CheckoutController::class, "checkout"])->name("credits.buy");
    Route::get("/account", [App\Http\Controllers\AccountController::class, "show"])->name("account");
    Route::patch("/account/profile", [App\Http\Controllers\AccountController::class, "updateProfile"])->name("account.profile");
    Route::patch("/account/preferences", [App\Http\Controllers\AccountController::class, "updatePreferences"])->name("account.preferences");
    Route::patch("/account/password", [App\Http\Controllers\AccountController::class, "updatePassword"])->name("account.password");
    Route::delete("/account", [App\Http\Controllers\AccountController::class, "destroy"])->name("account.destroy");
});
