<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('customer_name')->nullable()->after('book_id');
            $table->string('customer_email')->nullable()->index()->after('customer_name');
            $table->string('customer_phone')->nullable()->after('customer_email');
            $table->string('guest_download_token_hash', 64)->nullable()->after('customer_phone');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn([
                'customer_name',
                'customer_email',
                'customer_phone',
                'guest_download_token_hash',
            ]);
        });
    }
};
