<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_orders', function (Blueprint $table) {
            $table->string('refund_status')->nullable()->after('paid_at');
            $table->unsignedInteger('refund_amount_cents')->nullable()->after('refund_status');
            $table->string('stripe_refund_id')->nullable()->after('refund_amount_cents');
            $table->text('refund_reason')->nullable()->after('stripe_refund_id');
            $table->timestamp('refunded_at')->nullable()->after('refund_reason');
        });
    }

    public function down(): void
    {
        Schema::table('payment_orders', function (Blueprint $table) {
            $table->dropColumn([
                'refund_status',
                'refund_amount_cents',
                'stripe_refund_id',
                'refund_reason',
                'refunded_at',
            ]);
        });
    }
};
