<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('credit_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('payment_order_id')->nullable();
            $table->enum('type', ['debit', 'credit', 'refund', 'manual_adjust']);
            $table->integer('amount');
            $table->string('reason');
            $table->uuid('reference_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamp('created_at');
        });
    }

    public function down(): void {
        Schema::dropIfExists('credit_transactions');
    }
};
