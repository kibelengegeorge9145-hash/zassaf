<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->string('transaction_reference', 64)->unique();
            $table->string('provider_reference', 64)->nullable()->index();
            $table->decimal('amount', 12, 2);
            $table->string('payment_type', 20)->index();
            $table->string('payment_method', 30);
            $table->string('status', 20)->default('pending')->index();
            $table->string('failure_reason')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'payment_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
