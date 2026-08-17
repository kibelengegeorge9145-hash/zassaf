<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('weekend_convos', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title_en');
            $table->string('title_sw')->nullable();
            $table->text('description_en');
            $table->text('description_sw')->nullable();
            $table->text('topics_en')->nullable();
            $table->text('topics_sw')->nullable();
            $table->date('event_date')->nullable();
            $table->string('event_time')->nullable();
            $table->string('platform_en')->nullable();
            $table->string('platform_sw')->nullable();
            $table->string('speaker_en')->nullable();
            $table->string('speaker_sw')->nullable();
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weekend_convos');
    }
};
