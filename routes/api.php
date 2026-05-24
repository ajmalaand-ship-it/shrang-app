<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\JobStatusController;
use App\Http\Controllers\Api\DownloadController;

Route::middleware(["auth:sanctum"])->group(function () {
    Route::get("/jobs/{job}/status", [JobStatusController::class, "show"])->name("api.jobs.status");
    Route::get("/download/{asset}", [DownloadController::class, "show"])->name("api.download");
});
