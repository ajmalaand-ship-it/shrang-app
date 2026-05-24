<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\JobStatusController;

Route::middleware(["auth:sanctum"])->group(function () {
    Route::get("/jobs/{job}/status", [JobStatusController::class, "show"])->name("api.jobs.status");
});
