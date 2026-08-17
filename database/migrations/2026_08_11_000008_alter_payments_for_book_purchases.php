<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['member_id']);

            $table->unsignedBigInteger('member_id')->nullable()->change();

            $table->foreignId('user_id')->nullable()->after('member_id')->constrained()->cascadeOnDelete();
            $table->foreignId('book_id')->nullable()->after('user_id')->constrained()->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['book_id']);

            $table->dropColumn(['user_id', 'book_id']);

            $table->dropForeign(['member_id']);
            $table->unsignedBigInteger('member_id')->nullable(false)->change();
            $table->foreign('member_id')->references('id')->on('members')->cascadeOnDelete();
        });
    }
};
