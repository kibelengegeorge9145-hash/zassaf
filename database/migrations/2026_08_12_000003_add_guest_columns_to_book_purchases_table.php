<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('book_purchases', function (Blueprint $table) {
            $table->string('customer_name')->nullable()->after('payment_id');
            $table->string('customer_email')->nullable()->index()->after('customer_name');
            $table->string('customer_phone')->nullable()->after('customer_email');
            $table->string('download_token_hash', 64)->nullable()->index()->after('customer_phone');
            $table->timestamp('download_token_expires_at')->nullable()->after('download_token_hash');
        });
    }

    public function down(): void
    {
        Schema::table('book_purchases', function (Blueprint $table) {
            $table->dropColumn([
                'customer_name',
                'customer_email',
                'customer_phone',
                'download_token_hash',
                'download_token_expires_at',
            ]);
        });
    }
};
