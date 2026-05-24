<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Payments\WebhookController;

Route::post("/stripe", [WebhookController::class, "handle"])->name("webhooks.stripe");
