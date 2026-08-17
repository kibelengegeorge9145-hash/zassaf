<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sandbox_payments', function (Blueprint $table) {
            $table->id();
            $table->string('provider_reference', 64)->unique();
            $table->decimal('amount', 12, 2);
            $table->string('payment_method', 30);
            $table->string('status', 20)->default('pending')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sandbox_payments');
    }
};
