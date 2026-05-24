<?php
namespace App\Services;
use App\Models\CreditReservation;
use App\Models\CreditTransaction;
use App\Models\GenerationJob;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
class CreditService
{
    public function __construct(private readonly AdminSettingsService $settings) {}
    public function spendableBalance(User $user): int
    {
        $held = CreditReservation::where("user_id", $user->id)
            ->where("status", "held")
            ->where("expires_at", ">", now())
            ->sum("amount");
        return max(0, $user->credit_balance - $held);
    }
    public function checkAndReserve(User $user, string $jobType, string $generationJobId): bool
    {
        $cost = $this->settings->creditCost($jobType);
        return DB::transaction(function () use ($user, $cost, $generationJobId) {
            $freshUser = User::where("id", $user->id)->lockForUpdate()->first();
            $held = CreditReservation::where("user_id", $freshUser->id)
                ->where("status", "held")
                ->where("expires_at", ">", now())
                ->sum("amount");
            $spendable = max(0, $freshUser->credit_balance - $held);
            if ($spendable < $cost) {
                return false;
            }
            CreditReservation::create([
                "user_id"           => $freshUser->id,
                "generation_job_id" => $generationJobId,
                "amount"            => $cost,
                "status"            => "held",
                "expires_at"        => now()->addMinutes(config("credits.reservation_ttl_minutes", 10)),
            ]);
            return true;
        });
    }
    public function commitReservation(string $generationJobId): void
    {
        DB::transaction(function () use ($generationJobId) {
            $reservation = CreditReservation::where("generation_job_id", $generationJobId)
                ->where("status", "held")
                ->lockForUpdate()
                ->first();
            if (!$reservation) return;
            $reservation->update(["status" => "committed", "committed_at" => now()]);
            User::where("id", $reservation->user_id)
                ->decrement("credit_balance", $reservation->amount);
            CreditTransaction::create([
                "user_id"      => $reservation->user_id,
                "type"         => "debit",
                "amount"       => $reservation->amount,
                "reason"       => "song_generation",
                "reference_id" => $generationJobId,
            ]);
        });
    }
    public function releaseReservation(string $generationJobId): void
    {
        CreditReservation::where("generation_job_id", $generationJobId)
            ->where("status", "held")
            ->update(["status" => "released", "released_at" => now()]);
    }
    public function manualAdjust(User $user, int $amount, string $reason, string $adminId): void
    {
        DB::transaction(function () use ($user, $amount, $reason, $adminId) {
            $user->increment("credit_balance", $amount);
            CreditTransaction::create([
                "user_id" => $user->id,
                "type"    => $amount >= 0 ? "credit" : "debit",
                "amount"  => abs($amount),
                "reason"  => $reason,
            ]);
        });
    }
    public function history(User $user, int $perPage = 20)
    {
        return CreditTransaction::where("user_id", $user->id)
            ->orderByDesc("created_at")
            ->paginate($perPage);
    }
}
