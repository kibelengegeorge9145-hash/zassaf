<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title_en');
            $table->string('title_sw')->nullable();
            $table->text('description_en');
            $table->text('description_sw')->nullable();
            $table->date('event_date');
            $table->string('event_time')->nullable();
            $table->string('location_en')->nullable();
            $table->string('location_sw')->nullable();
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
