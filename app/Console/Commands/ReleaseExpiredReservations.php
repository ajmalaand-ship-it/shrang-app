<?php
namespace App\Console\Commands;
use App\Models\CreditReservation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
class ReleaseExpiredReservations extends Command
{
    protected $signature   = "credits:release-expired";
    protected $description = "Release credit reservations whose expires_at has passed";
    public function handle(): void
    {
        $released = CreditReservation::where("status", "held")
            ->where("expires_at", "<", now())
            ->update([
                "status"      => "released",
                "released_at" => now(),
            ]);
        if ($released > 0) {
            Log::info("ReleaseExpiredReservations: released {$released} reservations");
        }
        $this->info("Released {$released} expired reservations.");
    }
}
