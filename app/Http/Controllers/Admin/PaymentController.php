<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\PaymentOrder;
use App\Services\CreditService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(private readonly CreditService $creditService) {}

    public function index()
    {
        $payments = PaymentOrder::with(['user', 'creditPackage'])
            ->latest()
            ->paginate(30);
        return view('pages.admin.payments.index', compact('payments'));
    }

    public function show(PaymentOrder $payment)
    {
        $payment->load(['user', 'creditPackage']);
        return view('pages.admin.payments.show', compact('payment'));
    }

    public function refund(Request $request, PaymentOrder $payment)
    {
        $validated = $request->validate([
            'refund_status'       => ['required', 'in:refunded,partially_refunded,disputed,manually_corrected'],
            'refund_amount_cents' => ['nullable', 'integer', 'min:0'],
            'stripe_refund_id'    => ['nullable', 'string', 'max:100'],
            'refund_reason'       => ['required', 'string', 'min:5', 'max:1000'],
            'credits_adjustment'  => ['nullable', 'integer'],
        ]);

        $before = [
            'refund_status'       => $payment->refund_status,
            'refund_amount_cents' => $payment->refund_amount_cents,
            'status'              => $payment->status,
        ];

        $payment->update([
            'refund_status'       => $validated['refund_status'],
            'refund_amount_cents' => $validated['refund_amount_cents'] ?? null,
            'stripe_refund_id'    => $validated['stripe_refund_id'] ?? null,
            'refund_reason'       => $validated['refund_reason'],
            'refunded_at'         => now(),
            'status'              => in_array($validated['refund_status'], ['refunded', 'partially_refunded'])
                                        ? $validated['refund_status']
                                        : $payment->status,
        ]);

        if (!empty($validated['credits_adjustment']) && $validated['credits_adjustment'] !== 0) {
            $this->creditService->manualAdjust(
                $payment->user,
                $validated['credits_adjustment'],
                'Admin refund/correction: ' . $validated['refund_reason'],
                $request->user()->id,
            );
        }

        AuditLog::create([
            'actor_id'    => $request->user()->id,
            'actor_type'  => 'admin',
            'action'      => 'payment.refund_correction',
            'target_type' => 'payment_order',
            'target_id'   => $payment->id,
            'before'      => $before,
            'after'       => [
                'refund_status'       => $validated['refund_status'],
                'refund_amount_cents' => $validated['refund_amount_cents'] ?? null,
                'credits_adjustment'  => $validated['credits_adjustment'] ?? 0,
                'reason'              => $validated['refund_reason'],
            ],
            'ip_address'  => $request->ip(),
            'user_agent'  => $request->userAgent(),
        ]);

        return redirect()->route('admin.payments.show', $payment)
            ->with('success', 'Refund/correction recorded successfully.');
    }
}
