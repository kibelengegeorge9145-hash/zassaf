<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title_en');
            $table->string('title_sw')->nullable();
            $table->text('description_en');
            $table->text('description_sw')->nullable();
            $table->string('author');
            $table->string('cover_path')->nullable();
            $table->string('status')->default('coming_soon')
                ->comment('featured|published|preorder|coming_soon');
            $table->date('publication_date')->nullable();
            $table->decimal('price', 12, 2)->nullable();
            $table->string('currency')->default('TZS');
            $table->boolean('preorder_enabled')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
