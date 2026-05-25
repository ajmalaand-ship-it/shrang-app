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

    Route::get("/packages", [\App\Http\Controllers\Admin\CreditPackageController::class, "index"])->name("packages.index");
    Route::post("/packages", [\App\Http\Controllers\Admin\CreditPackageController::class, "store"])->name("packages.store");
    Route::patch("/packages/{package}", [\App\Http\Controllers\Admin\CreditPackageController::class, "update"])->name("packages.update");
    Route::patch("/packages/{package}/toggle", [\App\Http\Controllers\Admin\CreditPackageController::class, "toggle"])->name("packages.toggle");
    Route::get("/discover", [App\Http\Controllers\Admin\DiscoverController::class, "index"])->name("discover.index");
    Route::post("/discover/{clip}/feature", [App\Http\Controllers\Admin\DiscoverController::class, "feature"])->name("discover.feature");
    Route::delete("/discover/{clip}/unfeature", [App\Http\Controllers\Admin\DiscoverController::class, "unfeature"])->name("discover.unfeature");
    Route::patch("/discover/{clip}/pin", [App\Http\Controllers\Admin\DiscoverController::class, "pin"])->name("discover.pin");
    Route::patch("/discover/{clip}/block", [App\Http\Controllers\Admin\DiscoverController::class, "block"])->name("discover.block");
    Route::get("/payments", [\App\Http\Controllers\Admin\PaymentController::class, "index"])->name("payments.index");
});
