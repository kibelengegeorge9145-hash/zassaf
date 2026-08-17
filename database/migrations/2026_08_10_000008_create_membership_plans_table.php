<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('membership_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name_en');
            $table->string('name_sw')->nullable();
            $table->text('description_en')->nullable();
            $table->text('description_sw')->nullable();
            $table->decimal('price', 12, 2);
            $table->string('currency')->default('TZS');
            $table->string('billing_cycle')->default('monthly')
                ->comment('one_time|monthly|yearly');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('membership_plans');
    }
};
