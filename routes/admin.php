<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\JobMonitorController;
use App\Http\Controllers\Admin\AiUsageController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\LanguageHintController;

Route::prefix("admin")->name("admin.")->middleware(["auth", "admin", "audit"])->group(function () {

    Route::get("/", [DashboardController::class, "index"])->name("dashboard");

    Route::get("/users", [UserController::class, "index"])->name("users.index");
    Route::get("/users/{user}", [UserController::class, "show"])->name("users.show");
    Route::post("/users/{user}/ban", [UserController::class, "toggleBan"])->name("users.ban");
    Route::post("/users/{user}/credits", [UserController::class, "adjustCredits"])->name("users.credits");

    Route::get("/jobs", [JobMonitorController::class, "index"])->name("jobs.index");

    Route::get("/ai-usage", [AiUsageController::class, "index"])->name("ai-usage.index");

    Route::get("/settings", [SettingsController::class, "index"])->name("settings.index");
    Route::post("/settings", [SettingsController::class, "update"])->name("settings.update");

    Route::get("/audit-log", [AuditLogController::class, "index"])->name("audit-log.index");

    Route::get("/language-hints", [LanguageHintController::class, "index"])->name("language-hints.index");
    Route::post("/language-hints", [LanguageHintController::class, "store"])->name("language-hints.store");
    Route::delete("/language-hints/{languageHint}", [LanguageHintController::class, "destroy"])->name("language-hints.destroy");

});
