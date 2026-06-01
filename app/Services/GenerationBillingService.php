<?php

namespace App\Services;

use App\Models\User;

class GenerationBillingService
{
    public function __construct(
        private readonly AdminSettingsService $settings,
        private readonly CreditService $credits,
    ) {}

    /**
     * Check spendable balance and reserve credits for a generation action.
     *
     * Returns ['ok' => true, 'cost' => int] on success.
     * Returns ['ok' => false, 'message' => string] on failure.
     */
    public function checkAndReserve(User $user, string $jobType, string $generationJobId): array
    {
        $cost      = $this->settings->creditCost($jobType);
        $spendable = $this->credits->spendableBalance($user);

        if ($spendable < $cost) {
            return [
                'ok'      => false,
                'message' => "Insufficient credits. You need {$cost} credits but have {$spendable}.",
            ];
        }

        $reserved = $this->credits->checkAndReserve($user, $jobType, $generationJobId);

        if (!$reserved) {
            return [
                'ok'      => false,
                'message' => 'Could not reserve credits. Please try again.',
            ];
        }

        return [
            'ok'   => true,
            'cost' => $cost,
        ];
    }
}
