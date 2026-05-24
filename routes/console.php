<?php
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command("inspire", function () {
    $this->comment(Inspiring::quote());
})->purpose("Display an inspiring quote");

// Release expired credit reservations every 5 minutes
Schedule::command("credits:release-expired")->everyFiveMinutes();

// Purge orphaned temp media files weekly
Schedule::job(new \App\Jobs\PurgeOrphanedMediaJob)->weekly();

// Prune failed jobs daily
Schedule::command("queue:prune-failed")->daily();
