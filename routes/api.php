<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\JobStatusController;
use App\Http\Controllers\Api\DownloadController;

Route::middleware(["auth:sanctum,web"])->group(function () {
    Route::get("/clips/{clip}/status", [App\Http\Controllers\Api\ClipStatusController::class, "show"])->name("api.clips.status");
    Route::get("/jobs/{job}/status", [JobStatusController::class, "show"])->name("api.jobs.status");
    Route::get("/download/{asset}", [DownloadController::class, "show"])->name("api.download");
});
